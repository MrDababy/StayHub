<?php
$conn = new mysqli('localhost', 'root', '', 'stayhub_db');
if ($conn->connect_error) {
    die('DB FAILED: ' . $conn->connect_error);
}
$have = $conn->query('SELECT COUNT(*) AS cnt FROM beds')->fetch_assoc()['cnt'];
if ($have > 0) {
    echo 'SKIPPED: beds already exist';
    exit;
}
$conn->query("INSERT INTO blocks (block_name) VALUES ('Block A')");
$block_id = $conn->insert_id;
$conn->query("INSERT INTO rooms (block_id, room_number, floor, room_type, capacity, price, status, image) VALUES ($block_id, '101', 1, 'Double', 2, 1200.00, 'Available', 'room1.jpg')");
$room_id = $conn->insert_id;
$conn->query("INSERT INTO beds (room_id, bunk_number, bed_position, status) VALUES ($room_id, 1, 'Lower', 'Available')");
$conn->query("INSERT INTO beds (room_id, bunk_number, bed_position, status) VALUES ($room_id, 2, 'Upper', 'Available')");
echo 'INSERTED sample block/room/beds';
?>