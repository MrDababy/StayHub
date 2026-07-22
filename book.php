<?php
session_start();
require_once 'db.php';

$params = '';

// If room and bed parameters exist, map them to bed_id
if (isset($_GET['room']) && isset($_GET['bed'])) {
    $room_number = $_GET['room'];
    $bed_position = $_GET['bed'];
    $block_name = $_GET['block'] ?? 'Block A';

    // Normalize block name to match database
    if (strcasecmp($block_name, 'A') === 0 || strcasecmp($block_name, 'Block A') === 0) {
        $block_name = 'Block A';
    } elseif (strcasecmp($block_name, 'B') === 0 || strcasecmp($block_name, 'Block B') === 0) {
        $block_name = 'Block B';
    }

    // Query database to find bed_id for this room, bed position, and block
    $query = $conn->prepare(
        'SELECT beds.bed_id 
         FROM beds 
         JOIN rooms ON beds.room_id = rooms.room_id 
         JOIN blocks ON rooms.block_id = blocks.block_id 
         WHERE rooms.room_number = ? AND beds.bed_position = ? AND blocks.block_name = ?
         LIMIT 1'
    );
    $query->bind_param('sss', $room_number, $bed_position, $block_name);
    $query->execute();
    $query->bind_result($bed_id);

    if ($query->fetch()) {
        $params = '?bed_id=' . urlencode($bed_id);
    }
    $query->close();
}

// Redirect based on login status
if (isset($_SESSION['user_id']) || isset($_SESSION['user']) || isset($_SESSION['user_logged_in']) || isset($_SESSION['email'])) {
    header('Location: payment.php' . $params);
    exit;
} else {
    header('Location: register.php' . $params);
    exit;
}
