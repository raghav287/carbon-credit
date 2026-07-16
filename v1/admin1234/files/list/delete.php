<?php
require_once dirname(__DIR__, 2) . "/app/init.php";
require_once APP_ROOT . "/app/auth.php";
requireAdminLogin();
require_once APP_ROOT . "/app/module-data.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: " . file_url("list/list.php"));
    exit();
}

$listingId = filter_input(INPUT_POST, "id", FILTER_VALIDATE_INT);
if ($listingId === null || $listingId <= 0) {
    header("Location: " . file_url("list/list.php") . "?deleted=0");
    exit();
}

$deleted = deleteListingItem($listingId);
$redirect = file_url("list/list.php") . "?deleted=" . ($deleted ? "1" : "0");
header("Location: " . $redirect);
exit();
