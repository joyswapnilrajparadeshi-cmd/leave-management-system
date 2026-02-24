<?php
session_start();
include 'includes/db.php';
include 'includes/header.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'user') {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user']['id'];

// Fetch leave request by ID and validate ownership
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Invalid request.");
}

$leave_id = $_GET['id'];
$stmt = $conn->prepare("SELECT * FROM leave_requests WHERE id = ? AND user_id = ? AND status = 'Pending'");
$stmt->bind_param("ii", $leave_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows !== 1) {
    die("Leave request not found or cannot be edited.");
}

$leave = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Edit Leave Request</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: "Segoe UI", sans-serif;
            background: linear-gradient(-45deg, #ffafbd, #ffc3a0, #2193b0, #6dd5ed);
            background-size: 400% 400%;
            animation: gradientBG 15s ease infinite;
            color: #fff;
        }

        @keyframes gradientBG {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        .container {
            max-width: 600px;
            margin: 80px auto;
            background: rgba(255, 255, 255, 0.1);
            padding: 30px;
            border-radius: 20px;
            box-shadow: 0 0 20px rgba(255, 255, 255, 0.4);
        }

        h2 {
            text-align: center;
            color: #fff;
        }

        input, textarea, button {
            width: 100%;
            padding: 12px;
            margin: 10px 0;
            border: none;
            border-radius: 10px;
            background-color: rgba(255, 255, 255, 0.8);
            color: #000;
            font-size: 16px;
        }

        input::placeholder,
        textarea::placeholder {
            color: #333;
        }

        button {
            background-color: #0072ff;
            color: #fff;
            font-weight: bold;
            transition: background 0.2s ease;
        }

        button:hover {
            cursor: pointer;
            background-color: #005bd1;
        }

        .back-link {
            display: block;
            margin-top: 20px;
            text-align: center;
            color: #fff;
            text-decoration: underline;
        }
    </style>
</head>
<body>
<div class="container">
    <h2>Edit Leave Request</h2>
    <form action="update_leave.php" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="leave_id" value="<?= $leave['id'] ?>">

        <label>Reason for Leave</label>
        <input type="text" name="reason" required value="<?= htmlspecialchars($leave['reason']) ?>">

        <label>From Date</label>
        <input type="date" name="from_date" required value="<?= $leave['from_date'] ?>">

        <label>To Date</label>
        <input type="date" name="to_date" required value="<?= $leave['to_date'] ?>">

        <label>Update Medical Proof (optional)</label>
        <input type="file" name="proof_file" accept=".pdf,.jpg,.png">

        <?php if (!empty($leave['proof_file'])): ?>
            <p style="color:#fff;">Current File: 
                <a href="uploads/<?= urlencode($leave['proof_file']) ?>" target="_blank" style="color:#00f;">View</a>
            </p>
        <?php endif; ?>

        <button type="submit">Update Leave Request</button>
    </form>
    <a href="user_dashboard.php" class="back-link">← Back to Dashboard</a>
</div>
</body>
</html>
