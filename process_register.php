<?php
include 'includes/db.php';

$dept     = $_GET['dept'] ?? '';
$username = $_POST['username'] ?? '';
$email    = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';
$role     = $_POST['role'] ?? 'user'; // default user if not selected

if (empty($username) || empty($email) || empty($password) || empty($dept) || empty($role)) {
    die("❌ All fields are required!");
}

// Hash password
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

// Insert user
$sql = "INSERT INTO users (username, email, password, role, department) VALUES (?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sssss", $username, $email, $hashedPassword, $role, $dept);

if ($stmt->execute()) {
    header("Location: register.php?dept=" . urlencode($dept) . "&success=1");
    exit;
} else {
    echo "❌ Registration failed: " . $conn->error;
}
?>
