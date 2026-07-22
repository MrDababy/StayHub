<?php
require_once 'db.php';
$block_name = 'Block A';
$bed_status = [];
$query = $conn->prepare(
  'SELECT rooms.room_number, beds.bed_position, beds.status 
     FROM beds 
     JOIN rooms ON beds.room_id = rooms.room_id 
     JOIN blocks ON rooms.block_id = blocks.block_id 
     WHERE blocks.block_name = ?'
);
$query->bind_param('s', $block_name);
$query->execute();
$query->bind_result($room_num, $bed_pos, $status);
while ($query->fetch()) {
  $bed_status["{$room_num}_{$bed_pos}"] = $status;
}
$query->close();

function renderBedStatus($room, $bed, $label, $block, $bed_status)
{
  $key = "{$room}_{$bed}";
  $status = $bed_status[$key] ?? 'Available';
  if ($status === 'Occupied') {
    echo htmlspecialchars($label) . ": Booked<br>\n";
  } else {
    echo htmlspecialchars($label) . ': Available <a class="book-btn" href="book.php?room=' . urlencode($room) . '&bed=' . urlencode($bed) . '&block=' . urlencode($block) . '">Book</a><br>' . "\n";
  }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta charset="UTF-8" />
  <title>StayHub</title>

  <link rel="stylesheet" href="shhomepagestylesheet.css">
</head>

<body class="block-a">
  <header class="navbar">
    <div class="navbar-left">
      <h1>StayHub</h1>
    </div>

    <nav class="navbar-center" id="navbarCenter">
      <h2>Get started</h2>
      <a href="blocka.php?beds=2">Dual stay(2 people max)</a>
      <a href="blocka.php?beds=4">Quadro stay(4 people max)</a>
      <button class="nav-home" onclick="location.href='shhomepage.php'">Home</button>
      <button class="nav-home" onclick="location.href='blockb.php'">Block B</button>
    </nav>
    <div class="navbar-right">
    </div>
  </header>

  <div class="main-content" id="main-content">
  </div>

  <div class="room-card" data-beds="2" id="F1-2a">
    <h2 class="cards-heading">Block: A<br>
      Floor: 1<br>
      Room NO.: F1-2a<br>
      <?php renderBedStatus('F1-2a', 'right', 'Right-side bed', 'Block A', $bed_status); ?>
      <?php renderBedStatus('F1-2a', 'left', 'Left-side bed', 'Block A', $bed_status); ?>
      Price:600k
    </h2>
    <img class="card-img" src="images/roomf12a.jpg">
  </div>

  <div class="room-card" data-beds="2" id="F2-2a">
    <h2 class="cards-heading">Block: A<br>
      Floor: 2<br>
      Room NO.: F2-2a<br>
      <?php renderBedStatus('F2-2a', 'right', 'Right-side bed', 'Block A', $bed_status); ?>
      <?php renderBedStatus('F2-2a', 'left', 'Left-side bed', 'Block A', $bed_status); ?>
      Price:600k
    </h2>
    <img class="card-img" src="images/roomf22a.jpg">
  </div>

  <div class="room-card" data-beds="4" id="F1-4a">
    <h2 class="cards-heading">Block: A<br>
      Floor: 1<br>
      Room NO.: F1-4a<br>
      <?php renderBedStatus('F1-4a', 'rightlower', 'Right-sidelower bunk bed', 'Block A', $bed_status); ?>
      <?php renderBedStatus('F1-4a', 'rightupper', 'Right-side upper bunk bed', 'Block A', $bed_status); ?>
      <?php renderBedStatus('F1-4a', 'leftlower', 'Left-side lower bunk bed', 'Block A', $bed_status); ?>
      <?php renderBedStatus('F1-4a', 'leftupper', 'Left-side upper bunk bed', 'Block A', $bed_status); ?>
      Price:400k
    </h2>
    <img class="card-img" src="images/roomf14a.jpg">
  </div>

  <div class="room-card" data-beds="4" id="F2-4a">
    <h2 class="cards-heading">Block: A<br>
      Floor: 2<br>
      Room NO.: F2-4a<br>
      <?php renderBedStatus('F2-4a', 'rightlower', 'Right-side lower bunk bed', 'Block A', $bed_status); ?>
      <?php renderBedStatus('F2-4a', 'rightupper', 'Right-side upper bunk bed', 'Block A', $bed_status); ?>
      <?php renderBedStatus('F2-4a', 'leftlower', 'Left-side lower bunk bed', 'Block A', $bed_status); ?>
      <?php renderBedStatus('F2-4a', 'leftupper', 'Left-side upper bunk bed', 'Block A', $bed_status); ?>
      Price:400k
    </h2>
    <img class="card-img" src="images/roomf24a.jpg">
  </div>

  <script src="shscript.js"></script>
</body>

</html>