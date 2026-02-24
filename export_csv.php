<?php
include 'includes/db.php';

header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="leave_requests.csv"');

$output = fopen('php://output', 'w');
fputcsv($output, ['Username', 'Reason', 'From', 'To', 'Status', 'Proof File']);

$sql = "SELECT leave_requests.*, users.username 
        FROM leave_requests 
        JOIN users ON leave_requests.user_id = users.id";
$result = $conn->query($sql);

while ($row = $result->fetch_assoc()) {
    fputcsv($output, [
        $row['username'],
        $row['reason'],
        $row['from_date'],
        $row['to_date'],
        $row['status'],
        $row['proof_file']
    ]);
}

fclose($output);
exit;
