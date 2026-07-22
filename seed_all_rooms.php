<?php
require_once 'db.php';

// 1. Alter bed_position column size/type to support all formats
echo "Altering tables...\n";
$alter_query = "ALTER TABLE beds MODIFY COLUMN bed_position VARCHAR(50) NOT NULL";
if ($conn->query($alter_query)) {
    echo "Successfully altered table beds.bed_position to VARCHAR(50).\n";
} else {
    echo "Error altering table: " . $conn->error . "\n";
}

// 2. Insert Block B if it doesn't exist
$conn->query("INSERT IGNORE INTO blocks (block_id, block_name) VALUES (2, 'Block B')");

// 3. Clean up previously inserted beds with ID >= 3 and rooms with ID >= 2 to avoid conflicts
$conn->query("DELETE FROM beds WHERE bed_id >= 3");
$conn->query("DELETE FROM rooms WHERE room_id >= 2");

// 4. Data lists to insert
$roomsToInsert = [
    // Block A rooms (block_id = 1)
    ['room_id' => 2, 'block_id' => 1, 'room_number' => 'F1-2a', 'floor' => 1, 'room_type' => 'Double', 'capacity' => 2, 'price' => 600000.00, 'image' => 'images/roomf12a.jpg'],
    ['room_id' => 3, 'block_id' => 1, 'room_number' => 'F2-2a', 'floor' => 2, 'room_type' => 'Double', 'capacity' => 2, 'price' => 600000.00, 'image' => 'images/roomf22a.jpg'],
    ['room_id' => 4, 'block_id' => 1, 'room_number' => 'F1-4a', 'floor' => 1, 'room_type' => 'Quadro', 'capacity' => 4, 'price' => 400000.00, 'image' => 'images/roomf14a.jpg'],
    ['room_id' => 5, 'block_id' => 1, 'room_number' => 'F2-4a', 'floor' => 2, 'room_type' => 'Quadro', 'capacity' => 4, 'price' => 400000.00, 'image' => 'images/roomf24a.jpg'],
    // Block B rooms (block_id = 2)
    ['room_id' => 6, 'block_id' => 2, 'room_number' => 'F1-2a', 'floor' => 1, 'room_type' => 'Double', 'capacity' => 2, 'price' => 600000.00, 'image' => 'images/roomf12a.jpg'],
    ['room_id' => 7, 'block_id' => 2, 'room_number' => 'F2-2a', 'floor' => 2, 'room_type' => 'Double', 'capacity' => 2, 'price' => 600000.00, 'image' => 'images/roomf22a.jpg'],
    ['room_id' => 8, 'block_id' => 2, 'room_number' => 'F1-4a', 'floor' => 1, 'room_type' => 'Quadro', 'capacity' => 4, 'price' => 400000.00, 'image' => 'images/roomf14a.jpg'],
    ['room_id' => 9, 'block_id' => 2, 'room_number' => 'F2-4a', 'floor' => 2, 'room_type' => 'Quadro', 'capacity' => 4, 'price' => 600000.00, 'image' => 'images/roomf24a.jpg']
];

$bedsToInsert = [
    // Room 2 beds
    ['bed_id' => 3, 'room_id' => 2, 'bunk_number' => 1, 'bed_position' => 'right', 'status' => 'Available'],
    ['bed_id' => 4, 'room_id' => 2, 'bunk_number' => 2, 'bed_position' => 'left', 'status' => 'Available'],
    // Room 3 beds
    ['bed_id' => 5, 'room_id' => 3, 'bunk_number' => 1, 'bed_position' => 'right', 'status' => 'Available'],
    ['bed_id' => 6, 'room_id' => 3, 'bunk_number' => 2, 'bed_position' => 'left', 'status' => 'Available'],
    // Room 4 beds
    ['bed_id' => 7, 'room_id' => 4, 'bunk_number' => 1, 'bed_position' => 'rightlower', 'status' => 'Available'],
    ['bed_id' => 8, 'room_id' => 4, 'bunk_number' => 2, 'bed_position' => 'rightupper', 'status' => 'Available'],
    ['bed_id' => 9, 'room_id' => 4, 'bunk_number' => 3, 'bed_position' => 'leftlower', 'status' => 'Available'],
    ['bed_id' => 10, 'room_id' => 4, 'bunk_number' => 4, 'bed_position' => 'leftupper', 'status' => 'Available'],
    // Room 5 beds
    ['bed_id' => 11, 'room_id' => 5, 'bunk_number' => 1, 'bed_position' => 'rightlower', 'status' => 'Available'],
    ['bed_id' => 12, 'room_id' => 5, 'bunk_number' => 2, 'bed_position' => 'rightupper', 'status' => 'Available'],
    ['bed_id' => 13, 'room_id' => 5, 'bunk_number' => 3, 'bed_position' => 'leftlower', 'status' => 'Available'],
    ['bed_id' => 14, 'room_id' => 5, 'bunk_number' => 4, 'bed_position' => 'leftupper', 'status' => 'Available'],
    // Room 6 beds
    ['bed_id' => 15, 'room_id' => 6, 'bunk_number' => 1, 'bed_position' => 'right', 'status' => 'Available'],
    ['bed_id' => 16, 'room_id' => 6, 'bunk_number' => 2, 'bed_position' => 'left', 'status' => 'Available'],
    // Room 7 beds
    ['bed_id' => 17, 'room_id' => 7, 'bunk_number' => 1, 'bed_position' => 'right', 'status' => 'Available'],
    ['bed_id' => 18, 'room_id' => 7, 'bunk_number' => 2, 'bed_position' => 'left', 'status' => 'Available'],
    // Room 8 beds
    ['bed_id' => 19, 'room_id' => 8, 'bunk_number' => 1, 'bed_position' => 'rightlower', 'status' => 'Available'],
    ['bed_id' => 20, 'room_id' => 8, 'bunk_number' => 2, 'bed_position' => 'rightupper', 'status' => 'Available'],
    ['bed_id' => 21, 'room_id' => 8, 'bunk_number' => 3, 'bed_position' => 'leftlower', 'status' => 'Available'],
    ['bed_id' => 22, 'room_id' => 8, 'bunk_number' => 4, 'bed_position' => 'leftupper', 'status' => 'Available'],
    // Room 9 beds
    ['bed_id' => 23, 'room_id' => 9, 'bunk_number' => 1, 'bed_position' => 'rightlower', 'status' => 'Available'],
    ['bed_id' => 24, 'room_id' => 9, 'bunk_number' => 2, 'bed_position' => 'rightupper', 'status' => 'Available'],
    ['bed_id' => 25, 'room_id' => 9, 'bunk_number' => 3, 'bed_position' => 'leftlower', 'status' => 'Available'],
    ['bed_id' => 26, 'room_id' => 9, 'bunk_number' => 4, 'bed_position' => 'leftupper', 'status' => 'Available']
];

// Insert rooms
echo "Inserting rooms...\n";
foreach ($roomsToInsert as $r) {
    $stmt = $conn->prepare("INSERT INTO rooms (room_id, block_id, room_number, floor, room_type, capacity, price, status, image) VALUES (?, ?, ?, ?, ?, ?, ?, 'Available', ?)");
    $stmt->bind_param("iisisids", $r['room_id'], $r['block_id'], $r['room_number'], $r['floor'], $r['room_type'], $r['capacity'], $r['price'], $r['image']);
    $stmt->execute();
    $stmt->close();
}

// Insert beds
echo "Inserting beds...\n";
foreach ($bedsToInsert as $b) {
    $stmt = $conn->prepare("INSERT INTO beds (bed_id, room_id, bunk_number, bed_position, status) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("iiiss", $b['bed_id'], $b['room_id'], $b['bunk_number'], $b['bed_position'], $b['status']);
    $stmt->execute();
    $stmt->close();
}

echo "Database migration and seeding finished successfully!\n";
