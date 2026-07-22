<?php
$conn = new mysqli('localhost', 'root', '', 'stayhub_db');
if ($conn->connect_error) {
    die('DB FAILED: ' . $conn->connect_error);
}
$rows = [];
$res = $conn->query("SELECT COUNT(*) AS cnt FROM beds WHERE status='Available'");
$rows['available'] = $res->fetch_assoc()['cnt'];
$res = $conn->query("SELECT COUNT(*) AS cnt FROM beds WHERE status='Occupied'");
$rows['occupied'] = $res->fetch_assoc()['cnt'];
$res = $conn->query("SELECT bed_id, status FROM beds LIMIT 10");
$rows['sample'] = [];
while ($r = $res->fetch_assoc()) { $rows['sample'][] = $r; }
header('Content-Type: application/json');
echo json_encode($rows);
?>