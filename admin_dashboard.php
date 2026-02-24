<?php
session_start();
include 'includes/header.php';
include 'includes/db.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

// Get admin's department
$adminDept = $_SESSION['user']['department'];

// Filter
$search = $_GET['search'] ?? '';
$status_filter = $_GET['status'] ?? '';
$filter_sql = "WHERE users.department = ?";

if (!empty($search)) {
    $filter_sql .= " AND (users.username LIKE ? OR leave_requests.reason LIKE ?)";
}
if (!empty($status_filter)) {
    $filter_sql .= " AND leave_requests.status = ?";
}

// Pagination
$limit = 5;
$page = $_GET['page'] ?? 1;
$offset = ($page - 1) * $limit;

// --- Get counts ---
$count_sql = "SELECT 
    SUM(CASE WHEN status='Pending' THEN 1 ELSE 0 END) as pending,
    SUM(CASE WHEN status='Approved' THEN 1 ELSE 0 END) as approved,
    SUM(CASE WHEN status='Rejected' THEN 1 ELSE 0 END) as rejected
    FROM leave_requests 
    JOIN users ON leave_requests.user_id = users.id 
    WHERE users.department = ?";
$stmt = $conn->prepare($count_sql);
$stmt->bind_param("s", $adminDept);
$stmt->execute();
$counts = $stmt->get_result()->fetch_assoc();

// --- Get leave requests ---
$sql = "SELECT leave_requests.*, users.username, users.email 
        FROM leave_requests 
        JOIN users ON leave_requests.user_id = users.id 
        $filter_sql 
        ORDER BY leave_requests.id DESC 
        LIMIT ? OFFSET ?";
$stmt = $conn->prepare($sql);

// Bind params dynamically
if (!empty($search) && !empty($status_filter)) {
    $searchParam = "%$search%";
    $stmt->bind_param("ssssi", $adminDept, $searchParam, $searchParam, $status_filter, $limit, $offset);
} elseif (!empty($search)) {
    $searchParam = "%$search%";
    $stmt->bind_param("sssi", $adminDept, $searchParam, $searchParam, $limit, $offset);
} elseif (!empty($status_filter)) {
    $stmt->bind_param("ssii", $adminDept, $status_filter, $limit, $offset);
} else {
    $stmt->bind_param("sii", $adminDept, $limit, $offset);
}

$stmt->execute();
$result = $stmt->get_result();

// --- Get total for pagination ---
$total_sql = "SELECT COUNT(*) as total 
              FROM leave_requests 
              JOIN users ON leave_requests.user_id = users.id 
              $filter_sql";
$stmt = $conn->prepare($total_sql);

if (!empty($search) && !empty($status_filter)) {
    $stmt->bind_param("sss", $adminDept, $searchParam, $searchParam, $status_filter);
} elseif (!empty($search)) {
    $stmt->bind_param("ss", $adminDept, $searchParam, $searchParam);
} elseif (!empty($status_filter)) {
    $stmt->bind_param("ss", $adminDept, $status_filter);
} else {
    $stmt->bind_param("s", $adminDept);
}

$stmt->execute();
$total = $stmt->get_result()->fetch_assoc()['total'];
$total_pages = ceil($total / $limit);

// --- Export CSV ---
if (isset($_GET['export']) && $_GET['export'] == 'csv') {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment;filename=leave_requests.csv');

    $export_sql = "SELECT leave_requests.*, users.username 
                   FROM leave_requests 
                   JOIN users ON leave_requests.user_id = users.id 
                   WHERE users.department = ?";
    $stmt = $conn->prepare($export_sql);
    $stmt->bind_param("s", $adminDept);
    $stmt->execute();
    $export_result = $stmt->get_result();

    $output = fopen("php://output", "w");
    fputcsv($output, ['ID', 'Username', 'From', 'To', 'Reason', 'Status']);
    while ($row = $export_result->fetch_assoc()) {
        fputcsv($output, [$row['id'], $row['username'], $row['from_date'], $row['to_date'], $row['reason'], $row['status']]);
    }
    fclose($output);
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
    <style>
        body {
            margin: 0; padding: 0;
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
            max-width: 1000px;
            margin: 80px auto;
            background: rgba(255, 255, 255, 0.1);
            padding: 30px;
            border-radius: 20px;
            box-shadow: 0 0 20px rgba(255, 255, 255, 0.4);
        }
        h2 { text-align: center; color: #fff; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; background: rgba(255,255,255,0.1); }
        th, td { padding: 12px; border-bottom: 1px solid #ccc; text-align: left; color:#fff; }
        .actions form { display:inline-block; margin-right:5px; }
        input, select, button, textarea {
            padding: 8px; margin: 5px 0;
            border-radius: 8px; border: none;
            font-size: 14px;
        }
        textarea { width: 200px; resize: vertical; }
        .summary { display: flex; justify-content: space-between; margin-bottom: 15px; }
        .summary div {
            flex:1; background: rgba(0,0,0,0.2);
            margin: 5px; padding: 10px; border-radius: 10px;
            font-weight: bold; text-align:center;
        }
        .pagination { text-align:center; margin-top:15px; }
        .pagination a { color:white; text-decoration:none; margin:0 5px; font-weight:bold; }
        .logout-btn {
            position: fixed;
            top: 20px;
            right: 20px;
            background-color: #ff4b5c;
            color: white;
            padding: 10px 18px;
            border-radius: 6px;
            font-weight: bold;
            text-decoration: none;
            font-size: 14px;
            transition: background 0.2s ease;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.15);
            z-index: 9999;
            pointer-events: auto;
        }
        .logout-btn:hover {
            background: #ff4b5c;
            color: white;
            transform: scale(1.05);
            box-shadow: 0 0 15px rgba(255, 75, 92, 0.8);
        }
    </style>
</head>
<body>
<div class="container">
    <a href="logout.php" class="logout-btn">Logout</a>
    <h2>Admin Dashboard - Leave Requests (<?= htmlspecialchars($adminDept) ?> Department)</h2>

    <div class="summary">
        <div>Pending: <?= $counts['pending'] ?></div>
        <div>Approved: <?= $counts['approved'] ?></div>
        <div>Rejected: <?= $counts['rejected'] ?></div>
    </div>

    <form method="GET" class="filter-form">
        <input type="text" name="search" placeholder="Search by username or reason" value="<?= htmlspecialchars($search) ?>">
        <select name="status">
            <option value="">All Status</option>
            <option value="Pending" <?= $status_filter == 'Pending' ? 'selected' : '' ?>>Pending</option>
            <option value="Approved" <?= $status_filter == 'Approved' ? 'selected' : '' ?>>Approved</option>
            <option value="Rejected" <?= $status_filter == 'Rejected' ? 'selected' : '' ?>>Rejected</option>
        </select>
        <button type="submit">Filter</button>
        <a href="admin_dashboard.php?export=csv"><button type="button">Export CSV</button></a>
    </form>

    <table>
        <tr>
            <th>Username</th>
            <th>From - To</th>
            <th>Reason (User)</th>
            <th>Status</th>
            <th>Actions</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($row['username']) ?></td>
                <td><?= htmlspecialchars($row['from_date']) ?> - <?= htmlspecialchars($row['to_date']) ?></td>
                <td><?= htmlspecialchars($row['reason']) ?></td>
                <td><?= htmlspecialchars($row['status']) ?></td>
                <td class="actions">
                    <?php if ($row['status'] === 'Pending'): ?>
                        <!-- Approve -->
                        <form action="update_leave_status.php" method="POST" style="display:inline;">
                            <input type="hidden" name="id" value="<?= $row['id'] ?>">
                            <input type="hidden" name="status" value="Approved">
                            <button type="submit">Approve</button>
                        </form>

                        <!-- Reject with Reason -->
                        <form action="update_leave_status.php" method="POST" style="display:inline;" 
                              onsubmit="return confirm('Are you sure you want to reject this leave?');">
                            <input type="hidden" name="id" value="<?= $row['id'] ?>">
                            <input type="hidden" name="status" value="Rejected">
                            <textarea name="rejection_reason" placeholder="Enter rejection reason" required></textarea>
                            <button type="submit" style="background-color:red;">Reject</button>
                        </form>
                    <?php endif; ?>

                    <!-- Delete -->
                    <form action="delete_leave.php" method="POST" style="display:inline;" onsubmit="return confirm('Delete this leave request?');">
                        <input type="hidden" name="id" value="<?= $row['id'] ?>">
                        <button type="submit" style="background-color:red;">Delete</button>
                    </form>
                </td>
            </tr>
        <?php endwhile; ?>
    </table>

    <div class="pagination">
        <?php for ($p = 1; $p <= $total_pages; $p++): ?>
            <a href="?page=<?= $p ?>&search=<?= urlencode($search) ?>&status=<?= urlencode($status_filter) ?>">
                <?= $p ?>
            </a>
        <?php endfor; ?>
    </div>
</div>
</body>
</html>
