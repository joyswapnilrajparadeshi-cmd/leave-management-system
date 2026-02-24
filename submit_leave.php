<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'user') {
    header("Location: login.php"); exit;
}
include 'includes/db.php';

// ✅ Only process if leave form submitted
if (isset($_POST['submit_leave'])) {

    // Helper function to calculate leave units
    function calculateLeaveDays($from_date, $to_date, $leave_code) {
        $days = (strtotime($to_date) - strtotime($from_date)) / 86400 + 1;
        if ($leave_code === 'EL') {
            $month = date('n', strtotime($from_date));
            if (in_array($month, [5,6,12,1])) {
                $days = ceil($days / 3);
            }
        }
        return $days;
    }

    $user_id = $_SESSION['user']['id'];
    $leave_type_id = (int)($_POST['leave_type_id'] ?? 0);
    $reason = trim($_POST['reason'] ?? '');
    $from_date = $_POST['from_date'] ?? '';
    $to_date = $_POST['to_date'] ?? '';

    if ($leave_type_id <= 0 || $reason === '' || $from_date === '' || $to_date === '') {
        echo "<script>alert('Invalid form data.'); window.history.back();</script>"; exit;
    }

    // Ensure leave type is active and get quota
    $sql = "SELECT lt.id, lt.code, COALESCE(ulo.annual_quota, lt.annual_quota) AS quota
            FROM leave_types lt
            LEFT JOIN user_leave_overrides ulo
              ON ulo.leave_type_id = lt.id AND ulo.user_id = ?
            WHERE lt.id = ? AND lt.status='active' LIMIT 1";
    $st = $conn->prepare($sql);
    $st->bind_param("ii", $user_id, $leave_type_id);
    $st->execute();
    $lt = $st->get_result()->fetch_assoc();
    if (!$lt) { echo "<script>alert('Invalid leave type.'); window.location='leave_types.php';</script>"; exit; }

    // Calculate leave days
    $days = calculateLeaveDays($from_date, $to_date, $lt['code']);
    if ($days <= 0) {
        echo "<script>alert('Invalid date range.'); window.history.back();</script>"; exit;
    }

    // Calculate used leaves
    $used_sql = "SELECT from_date, to_date FROM leave_requests
                 WHERE user_id = ? AND leave_type_id = ? AND status='Approved'";
    $su = $conn->prepare($used_sql);
    $su->bind_param("ii", $user_id, $leave_type_id);
    $su->execute();
    $res = $su->get_result();

    $used = 0;
    while ($row = $res->fetch_assoc()) {
        $used += calculateLeaveDays($row['from_date'], $row['to_date'], $lt['code']);
    }

    $remaining = max(0, (int)$lt['quota'] - $used);
    if ($days > $remaining) {
        echo "<script>alert('Not enough balance. Remaining: $remaining days'); window.history.back();</script>"; exit;
    }

    // Handle proof file
    $proof_file = null;
    $leave_code = strtoupper($lt['code']);

    if ($leave_code === 'AL' && empty($_FILES['proof_file']['name'])) {
        echo "<script>alert('Proof document is mandatory for Academic Leave (AL).'); window.history.back();</script>"; exit;
    }

    if (!empty($_FILES['proof_file']['name'])) {
        $allowed = ['pdf','jpg','jpeg','png'];
        $ext = strtolower(pathinfo($_FILES['proof_file']['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, $allowed)) {
            echo "<script>alert('Invalid proof file type. Allowed: pdf, jpg, jpeg, png'); window.history.back();</script>"; exit;
        }
        $fname = 'proof_'.time().'_'.mt_rand(1000,9999).'.'.$ext;
        $dest = __DIR__ . '/uploads/' . $fname;
        if (!is_dir(__DIR__ . '/uploads')) { mkdir(__DIR__ . '/uploads', 0777, true); }
        if (!move_uploaded_file($_FILES['proof_file']['tmp_name'], $dest)) {
            echo "<script>alert('Failed to upload proof file.'); window.history.back();</script>"; exit;
        }
        $proof_file = $fname;
    }

    // Insert leave request
    $ins = $conn->prepare("INSERT INTO leave_requests (user_id, leave_type_id, reason, from_date, to_date, proof_file, status)
                           VALUES (?, ?, ?, ?, ?, ?, 'Pending')");
    $ins->bind_param("iissss", $user_id, $leave_type_id, $reason, $from_date, $to_date, $proof_file);
    if ($ins->execute()) {
        header("Location: user_dashboard.php?type=".$leave_type_id);
        exit;
    } else {
        echo "<script>alert('Failed to submit leave.'); window.history.back();</script>"; exit;
    }

} // ✅ End of leave form POST check

// If this script is accessed without submitting leave, redirect to leave types
header("Location: leave_types.php"); exit;
