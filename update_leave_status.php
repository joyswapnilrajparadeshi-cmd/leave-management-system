<?php
session_start();
include 'includes/db.php';

// PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require 'PHPMailer-master/PHPMailer-master/src/PHPMailer.php';
require 'PHPMailer-master/PHPMailer-master/src/SMTP.php';
require 'PHPMailer-master/PHPMailer-master/src/Exception.php';

// Only admin can access
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $status = $_POST['status'];
    $rejection_reason = $_POST['rejection_reason'] ?? null;

    // Validate rejection reason
    if ($status === 'Rejected' && empty($rejection_reason)) {
        $_SESSION['error'] = "Rejection reason is required!";
        header("Location: admin_dashboard.php");
        exit;
    }

    // Update leave status
    if ($status === 'Rejected') {
        $stmt = $conn->prepare("UPDATE leave_requests SET status=?, rejection_reason=?, updated_at=NOW() WHERE id=?");
        $stmt->bind_param("ssi", $status, $rejection_reason, $id);
    } else {
        $stmt = $conn->prepare("UPDATE leave_requests SET status=?, updated_at=NOW() WHERE id=?");
        $stmt->bind_param("si", $status, $id);
    }

    if ($stmt->execute()) {
        // Fetch user email and username
        $q = $conn->prepare("SELECT u.email, u.username FROM users u 
                             JOIN leave_requests l ON u.id = l.user_id WHERE l.id=?");
        $q->bind_param("i", $id);
        $q->execute();
        $user = $q->get_result()->fetch_assoc();

        if ($user) {
            $to = $user['email'];
            $username = $user['username'];
            $subject = "Leave Request Update";
            $message = "Dear $username,\n\nYour leave request has been $status.";
            if ($status === 'Rejected' && $rejection_reason) {
                $message .= "\nReason: $rejection_reason";
            }
            $message .= "\n\nRegards,\nLeave Management System";

            // Send email
            $mail = new PHPMailer(true);
            try {
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = 'joyswapnilrajparadeshi@gmail.com'; // your Gmail
                $mail->Password = 'kdbddmffsotggrjt'; // 16-char app password
                $mail->SMTPSecure = 'tls';
                $mail->Port = 587;

                // Disable SSL certificate verification (for local XAMPP testing)
                $mail->SMTPOptions = [
                    'ssl' => [
                        'verify_peer' => false,
                        'verify_peer_name' => false,
                        'allow_self_signed' => true
                    ]
                ];

                $mail->setFrom('joyswapnilrajparadeshi@gmail.com', 'Leave Management System');
                $mail->addAddress($to, $username);

                $mail->isHTML(false);
                $mail->Subject = $subject;
                $mail->Body = $message;

                $mail->send();
                $_SESSION['message'] = "Leave status updated and email sent successfully!";
            } catch (Exception $e) {
                $_SESSION['error'] = "Leave updated but email failed: " . $mail->ErrorInfo;
            }
        }

        header("Location: admin_dashboard.php");
        exit;
    } else {
        $_SESSION['error'] = "Error updating leave status!";
        header("Location: admin_dashboard.php");
        exit;
    }
}
?>
