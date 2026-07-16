<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>LOGIN PAGE DEBUG</h1>";

// RESET OPcache
if (function_exists('opcache_reset')) {
    opcache_reset();
    echo "<p style='color:green;'>✅ OPcache reset</p>";
}

echo "<h2>3. Including init.php...</h2>";
require_once dirname(__DIR__, 2) . "/app/init.php";
echo "<p><strong>BASE_URL AFTER INCLUDE:</strong> " . (defined('BASE_URL') ? BASE_URL : 'NOT DEFINED') . "</p>";

echo "<h2>4. Including auth.php...</h2>";
require_once APP_ROOT . "/app/auth.php";

echo "<h2>5. Testing file_url()...</h2>";
echo "<p><strong>file_url('dashboard'):</strong> " . file_url('dashboard') . "</p>";

echo "<p><a href='" . file_url('dashboard') . "'>Test Dashboard Link</a></p>";
?>