<?php
session_start();
require 'includes/db.php';
require 'PHPMailer-master/src/PHPMailer.php';
require 'PHPMailer-master/src/SMTP.php';
require 'PHPMailer-master/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['send_otp'])) {
    $email = trim($_POST['email']);
    $check = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $check->bind_param("s", $email);
    $check->execute();
    $result = $check->get_result();

    if ($result->num_rows > 0) {
        $otp = rand(100000, 999999);
        $expiry = date("Y-m-d H:i:s", strtotime("+10 minutes"));

        $update = $conn->prepare("UPDATE users SET otp = ?, otp_expiry = ?, reset_token = ?, token_expiry = ? WHERE email = ?");
        $update->bind_param("issss", $otp, $expiry, $otp, $expiry, $email);
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
        $mail->Subject = 'OTP for Password Reset';
        $mail->Body = "Your OTP is: $otp";

        if ($mail->send()) {
            $_SESSION['reset_email'] = $email;
            header("Location: verify_otp.php");
            exit();
        } else {
            $error = "Failed to send OTP: " . $mail->ErrorInfo;
        }
    } else {
        $error = "Email not found!";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Forgot Password</title>
    <style>
        <?php include 'style.css'; ?>
    </style>
</head>
<body>
<div class="form-container">
    <h2>Forgot Password</h2>
    <?php if ($error) echo "<p class='error'>$error</p>"; ?>
    <form method="POST" action="forgot_password.php">
        <label>Email</label>
        <input type="email" name="email" placeholder="Enter your email" required>
        <button type="submit" name="send_otp">Send OTP</button>
    </form>
</div>
</body>
</html>
