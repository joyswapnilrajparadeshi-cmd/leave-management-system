<?php
// db.php for leave-management.kesug.com

$host = "sql107.infinityfree.com";   // InfinityFree MySQL host
$user = "if0_41196114";              // MySQL username for your account
$pass = "AM5RlhTPgwx";               // vPanel password for MySQL
$db   = "if0_41196114_lms";          // Leave Management System database

// Connect to MySQL
$conn = new mysqli($host, $user, $pass, $db);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Optional: set charset
$conn->set_charset("utf8mb4");

// Enable error reporting (for debugging only)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
?>