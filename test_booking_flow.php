<?php
$conn = new mysqli('localhost', 'root', '', 'stayhub_db');
if ($conn->connect_error) {
    echo "DB FAILED: {$conn->connect_error}\n";
    exit(1);
}
$user = $conn->query("SELECT user_id, full_name, role FROM users WHERE role = 'student' LIMIT 1")->fetch_assoc();
echo "USER: ".json_encode($user)."\n";
$booking = $conn->query("SELECT bookings.booking_id, bookings.status, beds.bed_id FROM bookings LEFT JOIN beds ON bookings.bed_id=beds.bed_id WHERE bookings.user_id=".intval($user['user_id'])." ORDER BY bookings.booking_date DESC LIMIT 1")->fetch_assoc();
echo "LATEST_BOOKING: ".json_encode($booking)."\n";
$available = $conn->query("SELECT beds.bed_id, rooms.room_number, beds.bed_position, rooms.floor, blocks.block_name FROM beds JOIN rooms ON beds.room_id=rooms.room_id JOIN blocks ON rooms.block_id=blocks.block_id WHERE beds.status='Available' LIMIT 1")->fetch_assoc();
echo "AVAILABLE_BED: ".json_encode($available)."\n";
?>