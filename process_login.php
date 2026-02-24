<?php
session_start();
include 'includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate inputs
    $username   = trim($_POST['username'] ?? '');
    $password   = trim($_POST['password'] ?? '');
    $department = trim($_POST['department'] ?? '');

    if (empty($username) || empty($password) || empty($department)) {
        echo "All fields are required!";
        exit;
    }

    // Prepare query
    $sql = "SELECT * FROM users WHERE username = ? AND department = ?";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }
    $stmt->bind_param("ss", $username, $department);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();

        if (password_verify($password, $user['password'])) {
            $_SESSION['user'] = $user;

            // ✅ Redirect based on role
            if ($user['role'] === 'admin') {
                header("Location: admin_dashboard.php");
            } elseif ($user['role'] === 'user') {
                header("Location: leave_types.php");
            }
            exit;
        }
    }

    echo "Invalid login credentials or department mismatch!";
}
?>
