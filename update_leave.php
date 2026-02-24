<?php
session_start();
include 'includes/db.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'user') {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get submitted form values
    $leave_id = intval($_POST['leave_id']);
    $reason = trim($_POST['reason']);
    $from_date = $_POST['from_date'];
    $to_date = $_POST['to_date'];
    $user_id = $_SESSION['user']['id'];

    // Verify leave request belongs to user and is still pending
    $stmt = $conn->prepare("SELECT proof_file FROM leave_requests WHERE id = ? AND user_id = ? AND status = 'Pending'");
    $stmt->bind_param("ii", $leave_id, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows !== 1) {
        die("Invalid request or leave request cannot be updated.");
    }

    $leave = $result->fetch_assoc();
    $proof_file = $leave['proof_file'];

    // Handle optional file upload
    if (!empty($_FILES['proof_file']['name'])) {
        $upload_dir = "uploads/";
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        $file_name = time() . "_" . basename($_FILES['proof_file']['name']);
        $target_path = $upload_dir . $file_name;

        if (move_uploaded_file($_FILES['proof_file']['tmp_name'], $target_path)) {
            // Remove old file if it exists
            if (!empty($proof_file) && file_exists($upload_dir . $proof_file)) {
                unlink($upload_dir . $proof_file);
            }
            $proof_file = $file_name;
        } else {
            die("Failed to upload file. Please try again.");
        }
    }

    // Update leave request
    $stmt = $conn->prepare("UPDATE leave_requests 
                            SET reason = ?, from_date = ?, to_date = ?, proof_file = ? 
                            WHERE id = ? AND user_id = ? AND status = 'Pending'");
    $stmt->bind_param("ssssii", $reason, $from_date, $to_date, $proof_file, $leave_id, $user_id);

    if ($stmt->execute()) {
        header("Location: user_dashboard.php?success=Leave+request+updated");
        exit;
    } else {
        die("Failed to update leave request. Please try again.");
    }
} else {
    die("Invalid access.");
}
?>
