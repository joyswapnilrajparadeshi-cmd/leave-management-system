<?php
session_start();
include 'includes/header.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'user') {
    header("Location: login.php"); exit;
}
include 'includes/db.php';

$user_id = $_SESSION['user']['id'];

// Determine current leave type (from ?type= or stored in session)
$leave_type_id = isset($_GET['type']) ? (int)$_GET['type'] : ($_SESSION['current_leave_type_id'] ?? 0);
if ($leave_type_id <= 0) {
    header("Location: leave_types.php"); exit;
}
$_SESSION['current_leave_type_id'] = $leave_type_id;

// Pull leave type + quota (with user override if any)
$sql = "
    SELECT lt.id, lt.name, lt.code,
           COALESCE(ulo.annual_quota, lt.annual_quota) AS quota,
           lt.requires_proof
    FROM leave_types lt
    LEFT JOIN user_leave_overrides ulo
       ON ulo.leave_type_id = lt.id AND ulo.user_id = ?
    WHERE lt.id = ? AND lt.status = 'active'
    LIMIT 1
";
$st = $conn->prepare($sql);
$st->bind_param("ii", $user_id, $leave_type_id);
$st->execute();
$leave_type = $st->get_result()->fetch_assoc();
if (!$leave_type) {
    // Invalid/Inactive type
    header("Location: leave_types.php"); exit;
}

// Fetch current user's leave requests for this type
$stmt = $conn->prepare("SELECT * FROM leave_requests WHERE user_id = ? AND leave_type_id = ? ORDER BY id DESC");
$stmt->bind_param("ii", $user_id, $leave_type_id);
$stmt->execute();
$leave_results = $stmt->get_result();

// Balance calc for this type
$max_leaves = (int)$leave_type['quota'];
$used_leaves_query = $conn->prepare("SELECT SUM(DATEDIFF(to_date, from_date) + 1) AS used
                                     FROM leave_requests WHERE user_id = ? AND leave_type_id = ? AND status = 'Approved'");
$used_leaves_query->bind_param("ii", $user_id, $leave_type_id);
$used_leaves_query->execute();
$used_leaves = (int)($used_leaves_query->get_result()->fetch_assoc()['used'] ?? 0);
$remaining_leaves = max(0, $max_leaves - $used_leaves);
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>User Dashboard - <?= htmlspecialchars($leave_type['code']) ?></title>
    <style>
        body{margin:0;padding:0;font-family:"Segoe UI",sans-serif;background:linear-gradient(-45deg,#ffafbd,#ffc3a0,#2193b0,#6dd5ed);
             background-size:400% 400%;animation:gradientBG 15s ease infinite;color:#fff;}
        @keyframes gradientBG{0%{background-position:0% 50%}50%{background-position:100% 50%}100%{background-position:0% 50%}}
        .container{max-width:700px;margin:80px auto;background:rgba(255,255,255,.1);padding:30px;border-radius:20px;box-shadow:0 0 20px rgba(255,255,255,.4)}
        h2,h3{text-align:center;color:#fff}
        input,button,select,textarea{width:100%;padding:12px;margin:10px 0;border:none;border-radius:10px;background:rgba(255,255,255,.8);color:#000 !important;font-size:16px;}
        input::placeholder,textarea::placeholder{color:#333 !important;}
        button:hover{background:#0072ff;cursor:pointer;color:#fff;}
        table{width:100%;margin-top:25px;background:rgba(255,255,255,.1);border-collapse:collapse;color:#fff;}
        th,td{padding:10px;text-align:left;border-bottom:1px solid #ccc;}
        th{background:rgba(255,255,255,.2);color:#000;}
        .balance{text-align:center;font-weight:bold;margin-bottom:10px}
        .subhead{text-align:center;margin-top:-8px;opacity:.95}
        .logout-btn{position:fixed;top:20px;right:20px;background:#ff4b5c;color:#fff;padding:10px 18px;border-radius:6px;font-weight:bold;text-decoration:none;font-size:14px;transition:.2s;box-shadow:0 2px 4px rgba(0,0,0,.15);z-index:9999}
        .logout-btn:hover{transform:scale(1.05);box-shadow:0 0 15px rgba(255,75,92,.8)}
        .chip{display:inline-block;padding:6px 10px;border-radius:999px;background:rgba(255,255,255,.2);text-decoration:none;color:#fff;border:1px solid rgba(255,255,255,.35)}
        .action-btn{padding:6px 10px;border:none;border-radius:6px;background:#fff;color:#000;font-size:14px;cursor:pointer;margin-right:5px}
        .cancel-btn{background:red;color:#fff}
        .view-link{color:#00f}
    </style>
</head>
<body>
<div class="container">
    <a href="logout.php" class="logout-btn">Logout</a>

    <h2>Apply for Leave — <?= htmlspecialchars($leave_type['name']) ?> (<?= htmlspecialchars($leave_type['code']) ?>)</h2>
    <div class="subhead">
        <a class="chip" href="leave_types.php">← Change Type</a>
        <?php if ($leave_type['requires_proof']): ?>
            <span class="chip">Proof Required</span>
        <?php else: ?>
            <span class="chip">Proof Not Required</span>
        <?php endif; ?>
    </div>

    <div class="balance">
        Leave Balance (<?= htmlspecialchars($leave_type['code']) ?>):
        <?= $remaining_leaves ?> / <?= $max_leaves ?> (Used: <?= $used_leaves ?>)
    </div>

    <form action="submit_leave.php" method="POST" enctype="multipart/form-data" onsubmit="return confirmApply();">
        <input type="hidden" name="leave_type_id" value="<?= (int)$leave_type['id'] ?>">

        <label>Reason for Leave</label>
        <input type="text" name="reason" placeholder="Reason for leave" required>

        <label>From Date</label>
        <input type="date" name="from_date" required>

        <label>To Date</label>
        <input type="date" name="to_date" required>

        <?php if ($leave_type['requires_proof']): ?>
            <label>Upload Proof (required)</label>
            <input type="file" name="proof_file" accept=".pdf,.jpg,.png" required>
        <?php else: ?>
            <p><em>No proof required for <?= htmlspecialchars($leave_type['code']) ?> leave.</em></p>
        <?php endif; ?>

        <button type="submit" name="submit_leave">Submit Leave Request</button>

    </form>

    <h3>Your Leave Requests (<?= htmlspecialchars($leave_type['code']) ?>)</h3>
    <table>
        <thead>
            <tr>
                <th>Reason</th>
                <th>From</th>
                <th>To</th>
                <th>Status</th>
                <th>Proof</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php if ($leave_results->num_rows > 0): ?>
            <?php while ($row = $leave_results->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row['reason']) ?></td>
                    <td><?= htmlspecialchars($row['from_date']) ?></td>
                    <td><?= htmlspecialchars($row['to_date']) ?></td>
                    <td><?= ucfirst(htmlspecialchars($row['status'])) ?></td>
                    <td>
                        <?php if (!empty($row['proof_file'])): ?>
                            <a class="view-link" href="uploads/<?= urlencode($row['proof_file']) ?>" target="_blank">View</a>
                        <?php else: ?>
                            N/A
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if ($row['status'] === 'Pending'): ?>
                            <form action="edit_leave.php" method="GET" style="display:inline;">
                                <input type="hidden" name="id" value="<?= $row['id'] ?>">
                                <button type="submit" class="action-btn">Edit</button>
                            </form>
                            <form action="cancel_leave.php" method="POST" style="display:inline;"
                                  onsubmit="return confirm('Are you sure you want to cancel this leave?');">
                                <input type="hidden" name="id" value="<?= $row['id'] ?>">
                                <button type="submit" class="action-btn cancel-btn">Cancel</button>
                            </form>
                        <?php else: ?>
                            —
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr><td colspan="6" style="text-align:center;">No leave requests found.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>

<script>
function confirmApply(){
    const f = document.querySelector('form');
    const from = new Date(f.from_date.value);
    const to = new Date(f.to_date.value);
    if (to < from) { alert('To Date cannot be before From Date'); return false; }
    return true;
}
</script>
</body>
</html>
