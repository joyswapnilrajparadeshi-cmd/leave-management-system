<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'user') {
    header("Location: login.php"); exit;
}
include 'includes/db.php';

$user_id = $_SESSION['user']['id'];

// Fetch active leave types with any user override
$sql = "
    SELECT lt.id, lt.name, lt.code,
           COALESCE(ulo.annual_quota, lt.annual_quota) AS quota,
           lt.requires_proof, lt.description
    FROM leave_types lt
    LEFT JOIN user_leave_overrides ulo
        ON ulo.leave_type_id = lt.id AND ulo.user_id = ?
    WHERE lt.status = 'active'
    ORDER BY lt.name ASC
";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$types = $stmt->get_result();
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Choose Leave Type</title>
<style>
    body{margin:0;padding:0;font-family:"Segoe UI",sans-serif;color:#fff;
         background:linear-gradient(-45deg,#ffafbd,#ffc3a0,#2193b0,#6dd5ed);
         background-size:400% 400%;animation:gradientBG 15s ease infinite;}
    @keyframes gradientBG{0%{background-position:0% 50%}50%{background-position:100% 50%}100%{background-position:0% 50%}}
    .container{max-width:900px;margin:80px auto;background:rgba(255,255,255,.1);
               padding:30px;border-radius:20px;box-shadow:0 0 20px rgba(255,255,255,.4)}
    h2{text-align:center;margin:0 0 25px}
    .grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(240px,1fr));gap:16px}
    .card{background:rgba(255,255,255,.12);border-radius:16px;padding:18px;box-shadow:0 6px 20px rgba(0,0,0,.15)}
    .code{font-weight:bold;opacity:.95}
    .meta{font-size:14px;opacity:.9;margin:8px 0}
    .nums{margin:10px 0;font-weight:bold}
    .btn{display:inline-block;width:100%;text-align:center;padding:10px 14px;border:none;border-radius:10px;
         background:#00c6ff;color:#fff;font-weight:bold;cursor:pointer;transition:background .3s}
    .btn:hover{background:#0072ff}
    .topbar{display:flex;justify-content:space-between;align-items:center;margin-bottom:18px}
    .logout-btn{background:#ff4b5c;color:#fff;padding:8px 14px;border-radius:8px;text-decoration:none;font-weight:bold}
    .logout-btn:hover{filter:brightness(.95)}
</style>
</head>
<body>
<div class="container">
    <div class="topbar">
        <h2>Select a Leave Type</h2>
        <a class="logout-btn" href="logout.php">Logout</a>
    </div>

    <div class="grid">
        <?php while ($t = $types->fetch_assoc()): ?>
            <?php
            // Compute used (Approved) days for this user & type
            $used_sql = "SELECT SUM(DATEDIFF(to_date, from_date) + 1) AS used
                         FROM leave_requests
                         WHERE user_id = ? AND leave_type_id = ? AND status = 'Approved'";
            $s2 = $conn->prepare($used_sql);
            $s2->bind_param("ii", $user_id, $t['id']);
            $s2->execute();
            $used = (int)($s2->get_result()->fetch_assoc()['used'] ?? 0);
            $remaining = max(0, (int)$t['quota'] - $used);
            ?>
            <div class="card">
                <div class="code"><?= htmlspecialchars($t['name']) ?> (<?= htmlspecialchars($t['code']) ?>)</div>
                <div class="meta"><?= htmlspecialchars($t['description'] ?? '') ?></div>
                <div class="meta">Proof Required: <?= $t['requires_proof'] ? 'Yes' : 'No' ?></div>
                <div class="nums">Quota: <?= (int)$t['quota'] ?> | Used: <?= $used ?> | Remaining: <?= $remaining ?></div>
                <form action="user_dashboard.php" method="GET">
                    <input type="hidden" name="type" value="<?= (int)$t['id'] ?>">
                    <button class="btn" type="submit">Choose</button>
                </form>
            </div>
        <?php endwhile; ?>
        <?php if ($types->num_rows === 0): ?>
            <p>No active leave types configured. Please contact admin.</p>
        <?php endif; ?>
    </div>
</div>
</body>
</html>
