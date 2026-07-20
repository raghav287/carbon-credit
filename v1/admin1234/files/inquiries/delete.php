<?php
require_once dirname(__DIR__, 2) . "/app/init.php";
require_once APP_ROOT . "/app/auth.php";
requireAdminLogin();

function redirectToInquiries(bool $deleted): void
{
    header("Location: " . file_url("inquiries/inquiries.php") . "?deleted=" . ($deleted ? "1" : "0"));
    exit();
}

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: " . file_url("inquiries/inquiries.php"));
    exit();
}

$inquiryId = filter_input(INPUT_POST, "id", FILTER_VALIDATE_INT);
$name = trim((string) ($_POST["name"] ?? ""));
$email = trim((string) ($_POST["email"] ?? ""));
$mobile = trim((string) ($_POST["mobile"] ?? ""));
$message = trim((string) ($_POST["message"] ?? ""));
$createdAt = trim((string) ($_POST["created_at"] ?? ""));

$deleted = false;

try {
    $connection = getSashDBConnection();
    if ($connection === null) {
        error_log("Inquiry delete failed: database connection unavailable.");
        redirectToInquiries(false);
    }

    if ($inquiryId !== null && $inquiryId !== false && $inquiryId > 0) {
        $stmt = $connection->prepare("DELETE FROM `inquiries` WHERE `id` = ? LIMIT 1");
        $stmt->bind_param("i", $inquiryId);
        $stmt->execute();
        $deleted = $stmt->affected_rows > 0;
        $stmt->close();
    }

    if (!$deleted && $name !== "" && $email !== "" && $createdAt !== "") {
        $stmt = $connection->prepare(
            "DELETE FROM `inquiries`
             WHERE `name` = ?
               AND `email` = ?
               AND `mobile` = ?
               AND `message` = ?
               AND `created_at` = ?
             LIMIT 1"
        );
        $stmt->bind_param("sssss", $name, $email, $mobile, $message, $createdAt);
        $stmt->execute();
        $deleted = $stmt->affected_rows > 0;
        $stmt->close();
    }

    $connection->close();
} catch (Throwable $e) {
    error_log("Inquiry delete failed: {$e->getMessage()}");
}

redirectToInquiries($deleted);
