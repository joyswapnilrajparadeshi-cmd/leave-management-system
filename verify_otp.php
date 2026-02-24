<?php
session_start();
require 'includes/db.php';
require 'PHPMailer-master/src/PHPMailer.php';
require 'PHPMailer-master/src/SMTP.php';
require 'PHPMailer-master/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if (!isset($_SESSION['reset_email'])) {
    header("Location: forgot_password.php");
    exit();
}

$email = $_SESSION['reset_email'];
$error = '';
$success = '';
$otp_expired = false;

// Verify OTP
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['verify_otp'])) {
    $otp_input = trim($_POST['otp']);

    $stmt = $conn->prepare("SELECT reset_token, token_expiry FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user) {
        if (strtotime($user['token_expiry']) < time()) {
            $otp_expired = true;
        } elseif ($user['reset_token'] === $otp_input) {
            $_SESSION['otp_verified'] = true;
            header("Location: reset_password.php");
            exit();
        } else {
            $error = "Invalid OTP.";
        }
    } else {
        $error = "User not found.";
    }
}

// Resend OTP
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['resend_otp'])) {
    $new_otp = rand(100000, 999999);
    $expiry = date("Y-m-d H:i:s", strtotime("+10 minutes"));

    $update = $conn->prepare("UPDATE users SET otp = ?, otp_expiry = ?, reset_token = ?, token_expiry = ? WHERE email = ?");
    $update->bind_param("issss", $new_otp, $expiry, $new_otp, $expiry, $email);
    $update->execute();

    $mail = new PHPMailer();
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'joyswapnilrajparadeshi@gmail.com';
    $mail->Password = 'kdbddmffsotggrjt';
    $mail->SMTPSecure = 'tls';
    $mail->Port = 587;
    $mail->SMTPOptions = array(
        'ssl' => array(
            'verify_peer' => false,
            'verify_peer_name' => false,
            'allow_self_signed' => true
        )
    );

    $mail->setFrom('joyswapnilrajparadeshi@gmail.com', 'Leave App');
    $mail->addAddress($email);
    $mail->Subject = 'Resent OTP for Password Reset';
    $mail->Body = "Your new OTP is: $new_otp";

    if ($mail->send()) {
        $success = "✅ A new OTP has been sent to your email.";
        $otp_expired = false;
    } else {
        $error = "Failed to resend OTP: " . $mail->ErrorInfo;
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Verify OTP</title>
    <style>
        <?php include 'style.css'; ?>
        .error { color: red; font-weight: bold; }
        .success { color: green; font-weight: bold; }
    </style>
</head>
<body>
<div class="form-container">
    <h2>Verify OTP</h2>

    <?php if ($error) echo "<p class='error'>$error</p>"; ?>
    <?php if ($success) echo "<p class='success'>$success</p>"; ?>
    <?php if ($otp_expired) echo "<p class='error'>OTP expired! Please request a new one.</p>"; ?>

    <form method="POST" action="verify_otp.php">
        <input type="text" name="otp" placeholder="Enter OTP" required>
        <button type="submit" name="verify_otp">Verify OTP</button>
    </form>

    <!-- ✅ Always show Resend OTP button -->
    <form method="POST" action="verify_otp.php" style="margin-top:10px;">
        <button type="submit" name="resend_otp">Resend OTP</button>
    </form>
</div>
</body>
</html>
