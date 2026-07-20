<?php
declare(strict_types=1);

require_once dirname(__DIR__, 2) . "/app/init.php";
require_once APP_ROOT . "/app/auth.php";
requireAdminLogin();

$message = "";
$success = false;

try {
    $connection = getSashDBConnection();
    if ($connection === null) {
        throw new RuntimeException("Database connection unavailable.");
    }

    $suffix = date("Ymd_His");
    $backupTable = "inquiries_backup_" . $suffix;
    $fixedTable = "inquiries_fixed_" . $suffix;

    $connection->query(
        "CREATE TABLE `{$fixedTable}` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `name` varchar(255) NOT NULL,
            `email` varchar(150) NOT NULL,
            `mobile` varchar(50) NOT NULL,
            `message` text NOT NULL,
            `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
            PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci"
    );

    $columns = [];
    $result = $connection->query("SHOW COLUMNS FROM `inquiries`");
    while ($row = $result->fetch_assoc()) {
        $columns[$row["Field"]] = true;
    }
    $result->free();

    $nameSelect = isset($columns["name"]) ? "`name`" : "''";
    $emailSelect = isset($columns["email"]) ? "`email`" : "''";
    $mobileSelect = isset($columns["mobile"])
        ? "`mobile`"
        : (isset($columns["phone"]) ? "`phone`" : "''");
    $messageSelect = isset($columns["message"]) ? "`message`" : "''";
    $createdSelect = isset($columns["created_at"]) ? "`created_at`" : "CURRENT_TIMESTAMP";
    $orderBy = isset($columns["created_at"]) ? "`created_at` ASC" : "`id` ASC";

    $connection->query(
        "INSERT INTO `{$fixedTable}` (`name`, `email`, `mobile`, `message`, `created_at`)
         SELECT {$nameSelect}, {$emailSelect}, {$mobileSelect}, {$messageSelect}, {$createdSelect}
         FROM `inquiries`
         ORDER BY {$orderBy}"
    );

    $connection->query("RENAME TABLE `inquiries` TO `{$backupTable}`, `{$fixedTable}` TO `inquiries`");
    $success = true;
    $message = "Inquiry IDs repaired successfully. Backup table created: {$backupTable}";
    $connection->close();
} catch (Throwable $e) {
    $message = "Repair failed: " . $e->getMessage();
    error_log("Inquiry ID repair failed: {$e->getMessage()}");
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Repair Inquiry IDs</title>
    <link rel="stylesheet" href="<?= asset_url("plugins/bootstrap/css/bootstrap.min.css") ?>">
</head>
<body class="p-4">
    <div class="container" style="max-width: 760px;">
        <div class="alert alert-<?= $success ? "success" : "danger" ?>" role="alert">
            <?= htmlspecialchars($message, ENT_QUOTES, "UTF-8") ?>
        </div>
        <a class="btn btn-primary" href="<?= file_url("inquiries/inquiries.php") ?>">Back to Inquiries</a>
    </div>
</body>
</html>
