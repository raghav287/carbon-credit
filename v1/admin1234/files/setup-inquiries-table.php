<?php
require_once __DIR__ . '/config/database.php';

try {
    // Connect to MySQL server
    $conn = new mysqli("localhost", "root", "");
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Create database if not exists
    $conn->query("CREATE DATABASE IF NOT EXISTS `carbon` CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci");
    $conn->select_db("carbon");

    // Create inquiries table
    $sql = "CREATE TABLE IF NOT EXISTS `inquiries` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `name` varchar(255) NOT NULL,
        `email` varchar(150) NOT NULL,
        `mobile` varchar(50) NOT NULL,
        `message` text NOT NULL,
        `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";

    if ($conn->query($sql) === TRUE) {
        $columns = [];
        $result = $conn->query("SHOW COLUMNS FROM `inquiries`");
        while ($row = $result->fetch_assoc()) {
            $columns[$row["Field"]] = true;
        }
        $result->free();

        if (!isset($columns["email"])) {
            $conn->query("ALTER TABLE `inquiries` ADD `email` varchar(150) NOT NULL DEFAULT '' AFTER `name`");
        }

        if (!isset($columns["mobile"])) {
            $conn->query("ALTER TABLE `inquiries` ADD `mobile` varchar(50) NOT NULL DEFAULT '' AFTER `email`");
        }

        foreach (["phone", "service", "address", "event_date"] as $oldColumn) {
            if (isset($columns[$oldColumn])) {
                $conn->query("ALTER TABLE `inquiries` DROP COLUMN `$oldColumn`");
            }
        }

        echo "<h2>Inquiries table created successfully!</h2>";
        echo "<p><a href='/admin/files/inquiries/inquiries.php'>Go to Inquiries Admin Page</a></p>";
    } else {
        echo "Error creating table: " . $conn->error;
    }

    $conn->close();
} catch (Exception $e) {
    die("Error: " . $e->getMessage());
}
?>
