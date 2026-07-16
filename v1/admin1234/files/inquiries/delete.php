<?php
require_once dirname(__DIR__, 2) . "/app/init.php";
require_once APP_ROOT . "/app/auth.php";
requireAdminLogin();
require_once APP_ROOT . "/app/module-data.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: " . file_url("inquiries/inquiries.php"));
    exit();
}

$inquiryId = filter_input(INPUT_POST, "id", FILTER_VALIDATE_INT);
if ($inquiryId === null || $inquiryId <= 0) {
    header("Location: " . file_url("inquiries/inquiries.php") . "?deleted=0");
    exit();
}

$deleted = deleteInquiry($inquiryId);
$redirect = file_url("inquiries/inquiries.php") . "?deleted=" . ($deleted ? "1" : "0");
header("Location: " . $redirect);
exit();