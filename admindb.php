<?php
session_start();
if (empty($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'admin') {
  header('Location: adminlogin.php');
  exit;
}
require_once 'db.php';
$user_id = $_SESSION['user_id'];
$error = '';
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $action = $_POST['action'] ?? '';

  if ($action === 'add_room') {
    $room_number = trim($_POST['room_number'] ?? '');
    $bed_position = trim($_POST['bed_position'] ?? 'Upper');
    $block_name = trim($_POST['block_name'] ?? '');
    $floor = trim($_POST['floor'] ?? '');
    $image_url = trim($_POST['image_url'] ?? '');

    if ($room_number === '' || $block_name === '' || $floor === '') {
      $error = 'Please provide room number, block, and floor for the new room.';
    } else {
      $block_stmt = $conn->prepare('SELECT block_id FROM blocks WHERE block_name = ? LIMIT 1');
      $block_stmt->bind_param('s', $block_name);
      $block_stmt->execute();
      $block_stmt->store_result();
      if ($block_stmt->num_rows === 0) {
        $insert_block = $conn->prepare('INSERT INTO blocks (block_name) VALUES (?)');
        $insert_block->bind_param('s', $block_name);
        $insert_block->execute();
        $block_id = $insert_block->insert_id;
        $insert_block->close();
      } else {
        $block_stmt->bind_result($block_id);
        $block_stmt->fetch();
      }
      $block_stmt->close();

      $room_type = 'Double';
      $capacity = 2;
      $price = 0.00;
      $status = 'Available';
      $insert_room = $conn->prepare('INSERT INTO rooms (block_id, room_number, floor, room_type, capacity, price, status, image) VALUES (?, ?, ?, ?, ?, ?, ?, ?)');
      $insert_room->bind_param('isisdiss', $block_id, $room_number, $floor, $room_type, $capacity, $price, $status, $image_url);
      if ($insert_room->execute()) {
        $room_id = $insert_room->insert_id;
        $insert_room->close();
        $bed_status = 'Available';
        $bunk_number = 1;
        $bed_stmt = $conn->prepare('INSERT INTO beds (room_id, bunk_number, bed_position, status) VALUES (?, ?, ?, ?)');
        $bed_stmt->bind_param('iiss', $room_id, $bunk_number, $bed_position, $bed_status);
        $bed_stmt->execute();
        $bed_stmt->close();
        $message = 'Room added successfully.';
      } else {
        $error = 'Unable to add new room.';
      }
    }
  }

  if ($action === 'delete_room') {
    $room_id = intval($_POST['room_id'] ?? 0);
    if ($room_id > 0) {
      $delete_beds = $conn->prepare('DELETE FROM beds WHERE room_id = ?');
      $delete_beds->bind_param('i', $room_id);
      $delete_beds->execute();
      $delete_beds->close();

      $delete_room = $conn->prepare('DELETE FROM rooms WHERE room_id = ?');
      $delete_room->bind_param('i', $room_id);
      if ($delete_room->execute()) {
        $message = 'Room deleted successfully.';
      } else {
        $error = 'Unable to delete that room.';
      }
      $delete_room->close();
    }
  }

  if ($action === 'add_user') {
    $first_name = trim($_POST['first_name'] ?? '');
    $last_name = trim($_POST['last_name'] ?? '');
    $gender = trim($_POST['gender'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $email = trim($_POST['email'] ?? '');

    if ($first_name === '' || $last_name === '' || $gender === '' || $phone === '' || $email === '') {
      $error = 'Please complete all user fields before adding.';
    } elseif (!in_array($gender, ['male', 'female'], true)) {
      $error = 'Please select either male or female.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
      $error = 'Please enter a valid email address.';
    } else {
      $check = $conn->prepare('SELECT user_id FROM users WHERE email = ? LIMIT 1');
      $check->bind_param('s', $email);
      $check->execute();
      $check->store_result();
      if ($check->num_rows > 0) {
        $error = 'A user with that email already exists.';
      } else {
        $full_name = $first_name . ' ' . $last_name;
        $password = password_hash('password123', PASSWORD_DEFAULT);
        $role = 'student';
        $insert_user = $conn->prepare('INSERT INTO users (full_name, email, phone, password, role, gender) VALUES (?, ?, ?, ?, ?, ?)');
        $insert_user->bind_param('ssssss', $full_name, $email, $phone, $password, $role, $gender);
        if ($insert_user->execute()) {
          $message = 'User added successfully.';
        } else {
          $error = 'Unable to add user.';
        }
        $insert_user->close();
      }
      $check->close();
    }
  }

  if ($action === 'delete_user') {
    $delete_user_id = intval($_POST['delete_user_id'] ?? 0);
    if ($delete_user_id > 0) {
      $delete_user = $conn->prepare('DELETE FROM users WHERE user_id = ? AND role = ?');
      $role = 'student';
      $delete_user->bind_param('is', $delete_user_id, $role);
      if ($delete_user->execute()) {
        $message = 'User deleted successfully.';
      } else {
        $error = 'Unable to delete that user.';
      }
      $delete_user->close();
    }
  }
}

$stmt = $conn->prepare('SELECT full_name, email FROM users WHERE user_id = ? LIMIT 1');
$stmt->bind_param('i', $user_id);
$stmt->execute();
$stmt->bind_result($full_name, $email);
$stmt->fetch();
$stmt->close();

$roomsBooked = 0;
$roomsAvailable = 0;
$usersRegistered = 0;
$result = $conn->query("SELECT COUNT(*) AS count FROM bookings WHERE status = 'Confirmed'");
if ($result) {
  $row = $result->fetch_assoc();
  $roomsBooked = (int)$row['count'];
  $result->free();
}
$result = $conn->query("SELECT COUNT(*) AS count FROM rooms WHERE status = 'Available'");
if ($result) {
  $row = $result->fetch_assoc();
  $roomsAvailable = (int)$row['count'];
  $result->free();
}
$result = $conn->query("SELECT COUNT(*) AS count FROM users WHERE role = 'student'");
if ($result) {
  $row = $result->fetch_assoc();
  $usersRegistered = (int)$row['count'];
  $result->free();
}

$rooms = [];
$room_query = $conn->prepare(
  'SELECT rooms.room_id, rooms.room_number, beds.bed_position, blocks.block_name, rooms.floor, rooms.image 
     FROM rooms 
     LEFT JOIN beds ON beds.room_id = rooms.room_id 
     LEFT JOIN blocks ON rooms.block_id = blocks.block_id 
     GROUP BY rooms.room_id 
     ORDER BY rooms.room_id DESC'
);
$room_query->execute();
$room_query->bind_result($room_id, $room_number, $bed_position, $block_name, $floor, $image_url);
while ($room_query->fetch()) {
  $rooms[] = [
    'room_id' => $room_id,
    'room_number' => $room_number,
    'bed_position' => $bed_position,
    'block_name' => $block_name,
    'floor' => $floor,
    'image_url' => $image_url,
  ];
}
$room_query->close();

$users = [];
$user_query = $conn->prepare('SELECT user_id, full_name, gender, phone, email FROM users WHERE role = ? ORDER BY user_id DESC');
$user_role = 'student';
$user_query->bind_param('s', $user_role);
$user_query->execute();
$user_query->bind_result($registered_user_id, $registered_full_name, $registered_gender, $registered_phone, $registered_email);
while ($user_query->fetch()) {
  $users[] = [
    'user_id' => $registered_user_id,
    'full_name' => $registered_full_name,
    'gender' => $registered_gender,
    'phone' => $registered_phone,
    'email' => $registered_email,
  ];
}
$user_query->close();

$blocks = [];
$block_result = $conn->query('SELECT block_name FROM blocks ORDER BY block_name');
if ($block_result) {
  while ($row = $block_result->fetch_assoc()) {
    $blocks[] = $row['block_name'];
  }
  $block_result->free();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta charset="UTF-8" />
  <title>Admin Dashboard</title>
  <link rel="stylesheet" href="admindbstylesheet.css">
</head>

<body>
  <header class="navbar">
    <div class="navbar-left">
      <h1>Dashboard</h1>
    </div>

    <div class="navbar-center">
      <a href="shhomepage.php">Home</a>
      <a href="#manage-rooms">Manage Rooms</a>
      <a href="#manage-users">Manage Users</a>
    </div>

    <div class="navbar-right">
      <a class="button logout" href="logout.php">Log Out</a>
    </div>
  </header>

  <main class="main-content">
    <section class="stats-section">
      <div class="stat-card">
        <h2>Beds Booked</h2>
        <p class="stat-value"><?php echo $roomsBooked; ?></p>
      </div>
      <div class="stat-card">
        <h2>Beds Available</h2>
        <p class="stat-value"><?php echo $roomsAvailable; ?></p>
      </div>
      <div class="stat-card">
        <h2>Users Registered</h2>
        <p class="stat-value"><?php echo $usersRegistered; ?></p>
      </div>
    </section>

    <?php if ($error): ?>
      <p class="message error"><?php echo htmlspecialchars($error); ?></p>
    <?php endif; ?>

    <?php if ($message): ?>
      <p class="message success"><?php echo htmlspecialchars($message); ?></p>
    <?php endif; ?>

    <section class="management-section" id="manage-rooms">
      <div class="card">
        <h2 class="card-heading">Manage rooms</h2>
        <div class="management-row headings">
          <span>Room Number</span>
          <span>Bed Position</span>
          <span>Block</span>
          <span>Floor</span>
          <span>Bedroom Image</span>
          <div class="action-buttons">
            <button type="button" class="button" id="toggleRoomForm">Add</button>
          </div>
        </div>
        <form class="management-form" id="roomForm" method="POST" hidden>
          <div class="form-row">
            <input type="text" name="room_number" placeholder="Room Number" required>
            <select name="bed_position" required>
              <option value="Upper">Upper</option>
              <option value="Lower">Lower</option>
            </select>
            <input type="text" name="block_name" placeholder="Block" required list="blocks-list">
            <input type="number" name="floor" placeholder="Floor" required>
            <input type="text" name="image_url" placeholder="Bedroom image URL">
            <button type="submit" name="action" value="add_room" class="button">Save</button>
          </div>
          <datalist id="blocks-list">
            <?php foreach ($blocks as $block): ?>
              <option value="<?php echo htmlspecialchars($block); ?>"></option>
            <?php endforeach; ?>
          </datalist>
        </form>
      </div>
    </section>

    <section class="listing-section">
      <div class="card">
        <h2 class="card-heading">Existing rooms</h2>
        <div class="listing-row headings">
          <span>ID</span>
          <span>Room Number</span>
          <span>Bed Position</span>
          <span>Block</span>
          <span>Floor</span>
          <span>Bedroom Image</span>
          <span>Delete</span>
        </div>
        <?php foreach ($rooms as $room): ?>
          <div class="listing-row">
            <span><?php echo htmlspecialchars($room['room_id']); ?></span>
            <span><?php echo htmlspecialchars($room['room_number']); ?></span>
            <span><?php echo htmlspecialchars($room['bed_position'] ?: 'Unknown'); ?></span>
            <span><?php echo htmlspecialchars($room['block_name'] ?: 'Unknown'); ?></span>
            <span><?php echo htmlspecialchars($room['floor'] ?: 'N/A'); ?></span>
            <span>
              <?php if (!empty($room['image_url'])): ?>
                <img class="room-thumb" src="<?php echo htmlspecialchars($room['image_url']); ?>" alt="Room image">
              <?php else: ?>
                N/A
              <?php endif; ?>
            </span>
            <span>
              <form method="POST" class="inline-delete-form">
                <input type="hidden" name="room_id" value="<?php echo htmlspecialchars($room['room_id']); ?>">
                <button type="submit" name="action" value="delete_room" class="button">Delete</button>
              </form>
            </span>
          </div>
        <?php endforeach; ?>
      </div>
    </section>

    <section class="management-section" id="manage-users">
      <div class="card">
        <h2 class="card-heading">Manage Users</h2>
        <div class="management-row headings">
          <span>First Name</span>
          <span>Second Name</span>
          <span>Gender</span>
          <span>Phone Number</span>
          <span>Email</span>
          <div class="action-buttons">
            <button type="button" class="button" id="toggleUserForm">Add</button>
          </div>
        </div>
        <form class="management-form" id="userForm" method="POST" hidden>
          <div class="form-row">
            <input type="text" name="first_name" placeholder="First Name" required>
            <input type="text" name="last_name" placeholder="Second Name" required>
            <select name="gender" required>
              <option value="male">Male</option>
              <option value="female">Female</option>
            </select>
            <input type="text" name="phone" placeholder="Phone Number" required>
            <input type="email" name="email" placeholder="Email" required>
            <button type="submit" name="action" value="add_user" class="button">Save</button>
          </div>
        </form>
      </div>
    </section>

    <section class="listing-section">
      <div class="card">
        <h2 class="card-heading">Registered users</h2>
        <div class="listing-row headings">
          <span>ID</span>
          <span>Name</span>
          <span>Gender</span>
          <span>Phone</span>
          <span>Email</span>
          <span>Delete</span>
        </div>
        <?php foreach ($users as $user): ?>
          <div class="listing-row">
            <span><?php echo htmlspecialchars($user['user_id']); ?></span>
            <span><?php echo htmlspecialchars($user['full_name']); ?></span>
            <span><?php echo htmlspecialchars($user['gender'] ?: 'N/A'); ?></span>
            <span><?php echo htmlspecialchars($user['phone']); ?></span>
            <span><?php echo htmlspecialchars($user['email']); ?></span>
            <span>
              <form method="POST" class="inline-delete-form">
                <input type="hidden" name="delete_user_id" value="<?php echo htmlspecialchars($user['user_id']); ?>">
                <button type="submit" name="action" value="delete_user" class="button">Delete</button>
              </form>
            </span>
          </div>
        <?php endforeach; ?>
      </div>
    </section>
  </main>

  <script>
    document.getElementById('toggleRoomForm').addEventListener('click', function() {
      const form = document.getElementById('roomForm');
      form.hidden = !form.hidden;
    });
    document.getElementById('toggleUserForm').addEventListener('click', function() {
      const form = document.getElementById('userForm');
      form.hidden = !form.hidden;
    });
  </script>
</body>

</html>