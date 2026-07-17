<?php
declare(strict_types=1);

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: contact-us.php");
    exit;
}

$name = trim($_POST["name"] ?? "");
$email = trim($_POST["email"] ?? "");
$mobile = trim($_POST["mobile"] ?? "");
$message = trim($_POST["message"] ?? "");
$website = trim($_POST["website"] ?? "");
$consent = $_POST["consent"] ?? "";

if ($website !== "") {
    header("Location: contact-us.php?sent=1");
    exit;
}

$isValid =
    $name !== "" &&
    filter_var($email, FILTER_VALIDATE_EMAIL) !== false &&
    $mobile !== "" &&
    $message !== "" &&
    $consent === "1";

if (!$isValid) {
    header("Location: contact-us.php?sent=0");
    exit;
}

require_once __DIR__ . "/admin1234/config/database.php";

function ensureContactInquiriesTable(mysqli $connection): void
{
    $connection->query(
        "CREATE TABLE IF NOT EXISTS inquiries (
            id int(11) NOT NULL AUTO_INCREMENT,
            name varchar(255) NOT NULL,
            email varchar(150) NOT NULL,
            mobile varchar(50) NOT NULL,
            message text NOT NULL,
            created_at timestamp NOT NULL DEFAULT current_timestamp(),
            PRIMARY KEY (id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci"
    );

    $columns = [];
    $result = $connection->query("SHOW COLUMNS FROM inquiries");
    while ($row = $result->fetch_assoc()) {
        $columns[$row["Field"]] = true;
    }
    $result->free();

    if (!isset($columns["email"])) {
        $connection->query("ALTER TABLE inquiries ADD email varchar(150) NOT NULL DEFAULT '' AFTER name");
    }

    if (!isset($columns["mobile"])) {
        $connection->query("ALTER TABLE inquiries ADD mobile varchar(50) NOT NULL DEFAULT '' AFTER email");
    }

    foreach (["phone", "service", "address", "event_date"] as $oldColumn) {
        if (isset($columns[$oldColumn])) {
            $connection->query("ALTER TABLE inquiries DROP COLUMN `$oldColumn`");
        }
    }
}

$connection = getSashDBConnection();
if ($connection === null) {
    header("Location: contact-us.php?sent=0");
    exit;
}

try {
    ensureContactInquiriesTable($connection);

    $stmt = $connection->prepare(
        "INSERT INTO inquiries (name, email, mobile, message) VALUES (?, ?, ?, ?)"
    );
    $stmt->bind_param("ssss", $name, $email, $mobile, $message);
    $stmt->execute();
    $stmt->close();
    $connection->close();

    header("Location: contact-us.php?sent=1");
    exit;
} catch (mysqli_sql_exception $e) {
    error_log("Contact form submission failed: {$e->getMessage()}");
    header("Location: contact-us.php?sent=0");
    exit;
}
