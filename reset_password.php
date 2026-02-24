<?php
session_start();
require 'includes/db.php';

if (!isset($_SESSION['reset_email']) || !isset($_SESSION['otp_verified'])) {
    header("Location: forgot_password.php");
    exit();
}

$error = '';
$success = '';
$email = $_SESSION['reset_email'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reset_password'])) {
    $new_password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $stmt = $conn->prepare("UPDATE users 
        SET password = ?, reset_token = NULL, token_expiry = NULL, otp = NULL, otp_expiry = NULL 
        WHERE email = ?");
    $stmt->bind_param("ss", $new_password, $email);

    if ($stmt->execute()) {
        session_destroy();
        $success = "✅ Your password has been reset successfully!";
    } else {
        $error = "❌ Failed to reset password!";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Reset Password</title>
    <style>
        <?php include 'style.css'; ?>
        .success { color: green; margin-bottom: 10px; }
        .error { color: red; margin-bottom: 10px; }
        .btn { padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; }
        .btn-home { background: #4CAF50; color: white; text-decoration: none; }
    </style>
</head>
<body>
<div class="form-container">
    <h2>Reset Password</h2>
    <?php if ($error): ?>
        <p class="error"><?= $error ?></p>
    <?php endif; ?>

    <?php if ($success): ?>
        <p class="success"><?= $success ?></p>
        <a href="index.php" class="btn btn-home">Go to Home</a>
    <?php else: ?>
        <form method="POST" action="reset_password.php">
            <input type="password" name="password" placeholder="Enter new password" required>
            <button type="submit" name="reset_password">Reset Password</button>
        </form>
    <?php endif; ?>
</div>
</body>
</html>
