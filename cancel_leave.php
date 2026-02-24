<?php
session_start();
include 'includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $user_id = $_SESSION['user']['id'];

    $stmt = $conn->prepare("DELETE FROM leave_requests WHERE id = ? AND user_id = ? AND status = 'pending'");
    $stmt->bind_param("ii", $id, $user_id);
    $stmt->execute();
}

header("Location: user_dashboard.php");
exit;
