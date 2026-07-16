<?php
require_once __DIR__ . '/config/database.php';

try {
    // Connect to MySQL server
    $conn = new mysqli("localhost", "root", "");
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Create database if not exists
    $conn->query("CREATE DATABASE IF NOT EXISTS `tavix` CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci");
    $conn->select_db("tavix");

    // Create inquiries table
    $sql = "CREATE TABLE IF NOT EXISTS `inquiries` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `name` varchar(255) NOT NULL,
        `phone` varchar(50) NOT NULL,
        `service` varchar(100) NOT NULL,
        `address` text NOT NULL,
        `event_date` date NOT NULL,
        `message` text,
        `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";

    if ($conn->query($sql) === TRUE) {
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
