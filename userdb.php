<?php
session_start();
if (empty($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'student') {
    header('Location: login.php');
    exit;
}
require_once 'db.php';
$user_id = $_SESSION['user_id'];
$error = '';
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'save_profile') {
    $first_name = trim($_POST['first_name'] ?? '');
    $last_name = trim($_POST['last_name'] ?? '');
    $gender = trim($_POST['gender'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $email = trim($_POST['email'] ?? '');

    if ($first_name === '' || $last_name === '' || $gender === '' || $phone === '' || $email === '') {
        $error = 'Please complete all profile fields before saving.';
    } elseif (!in_array($gender, ['male', 'female'], true)) {
        $error = 'Please select either male or female.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } else {
        $stmt = $conn->prepare('SELECT user_id FROM users WHERE email = ? AND user_id != ? LIMIT 1');
        $stmt->bind_param('si', $email, $user_id);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $error = 'Email already belongs to another account.';
        } else {
            $full_name = $first_name . ' ' . $last_name;
            $update = $conn->prepare('UPDATE users SET full_name = ?, email = ?, phone = ?, gender = ? WHERE user_id = ?');
            $update->bind_param('ssssi', $full_name, $email, $phone, $gender, $user_id);
            if ($update->execute()) {
                $message = 'Profile updated successfully.';
                $_SESSION['email'] = $email;
            } else {
                $error = 'Unable to update profile at this time.';
            }
            $update->close();
        }
        $stmt->close();
    }
}

$stmt = $conn->prepare('SELECT full_name, email, phone, gender FROM users WHERE user_id = ? LIMIT 1');
$stmt->bind_param('i', $user_id);
$stmt->execute();
$stmt->bind_result($full_name, $email, $phone, $gender);
$stmt->fetch();
$stmt->close();

$first_name = '';
$last_name = '';
if (strpos($full_name, ' ') !== false) {
    [$first_name, $last_name] = explode(' ', $full_name, 2);
} else {
    $first_name = $full_name;
}

$booking = null;
$booking_stmt = $conn->prepare(
    'SELECT rooms.room_number, beds.bed_position, rooms.floor, blocks.block_name, bookings.status 
     FROM bookings 
     LEFT JOIN beds ON bookings.bed_id = beds.bed_id 
     LEFT JOIN rooms ON beds.room_id = rooms.room_id 
     LEFT JOIN blocks ON rooms.block_id = blocks.block_id 
     WHERE bookings.user_id = ? 
     ORDER BY bookings.booking_date DESC 
     LIMIT 1'
);
$booking_stmt->bind_param('i', $user_id);
$booking_stmt->execute();
$booking_stmt->bind_result($room_number, $bed_position, $floor, $block_name, $booking_status);
if ($booking_stmt->fetch()) {
    $booking = [
        'room_number' => $room_number,
        'bed_position' => $bed_position,
        'floor' => $floor,
        'block_name' => $block_name,
        'status' => $booking_status,
    ];
}
$booking_stmt->close();

$available_bed = null;
if (empty($booking)) {
  $bed_q = $conn->prepare(
    'SELECT beds.bed_id, rooms.room_number, beds.bed_position, rooms.floor, blocks.block_name 
     FROM beds 
     JOIN rooms ON beds.room_id = rooms.room_id 
     JOIN blocks ON rooms.block_id = blocks.block_id 
     WHERE beds.status = ? 
     LIMIT 1'
  );
  $avail_status = 'Available';
  $bed_q->bind_param('s', $avail_status);
  $bed_q->execute();
  $bed_q->bind_result($avail_bed_id, $avail_room_number, $avail_bed_position, $avail_floor, $avail_block_name);
  if ($bed_q->fetch()) {
    $available_bed = [
      'bed_id' => $avail_bed_id,
      'room_number' => $avail_room_number,
      'bed_position' => $avail_bed_position,
      'floor' => $avail_floor,
      'block_name' => $avail_block_name,
    ];
  }
  $bed_q->close();
}

$statusLabel = 'Pending';
$statusClass = 'pending';
if (!empty($booking['status'])) {
    if ($booking['status'] === 'Confirmed') {
        $statusLabel = 'Confirmed';
        $statusClass = 'confirmed';
    } elseif ($booking['status'] === 'Cancelled') {
        $statusLabel = 'Cancelled';
        $statusClass = 'cancelled';
    } else {
        $statusLabel = 'Pending';
        $statusClass = 'pending';
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta charset="UTF-8" />
  <title>User Dashboard</title>
  <link rel="stylesheet" href="userdbstylesheet.css">
</head>

<body>
  <header class="navbar">
    <div class="navbar-left">
      <h1>Dashboard</h1>
    </div>
    <div class="navbar-center">
      <a href="shhomepage.php">Home</a>
      <a href="blocka.php">Block A</a>
      <a href="blockb.php">Block B</a>
    </div>
    <div class="navbar-right">
      <a class="button logout" href="logout.php">Log Out</a>
    </div>
  </header>

  <main class="main-content">
    <div class="cards-container profile-area">
    <div class="card profile-card">
      <h2 class="card-heading">Profile</h2>
      <?php if ($error): ?>
      <p class="message error"><?php echo htmlspecialchars($error); ?></p>
      <?php endif; ?>
      <?php if ($message): ?>
      <p class="message success"><?php echo htmlspecialchars($message); ?></p>
      <?php endif; ?>
      <form id="profileForm" method="POST">
        <label class="profile-field">
        <span>First Name</span>
        <input class="profile-input" type="text" name="first_name" value="<?php echo htmlspecialchars($first_name); ?>" readonly required>
        </label>
        <label class="profile-field">
        <span>Second Name</span>
        <input class="profile-input" type="text" name="last_name" value="<?php echo htmlspecialchars($last_name); ?>" readonly required>
        </label>
        <label class="profile-field">
          <span>Gender</span>
          <select class="profile-input" name="gender" disabled required>
          <option value="" <?php echo $gender === '' ? 'selected' : ''; ?>>Select gender</option>
          <option value="male" <?php echo $gender === 'male' ? 'selected' : ''; ?>>Male</option>
          <option value="female" <?php echo $gender === 'female' ? 'selected' : ''; ?>>Female</option>
          </select>
        </label>
        <label class="profile-field">
          <span>Phone Number</span>
          <input class="profile-input" type="text" name="phone" value="<?php echo htmlspecialchars($phone); ?>" readonly required>
        </label>
      <label class="profile-field">
        <span>Email</span>
        <input class="profile-input" type="email" name="email" value="<?php echo htmlspecialchars($email); ?>" readonly required>
      </label>
        <div class="profile-actions">
        <button type="button" class="button" id="editProfileBtn">Update</button>
        <button type="submit" name="action" value="save_profile" class="button" id="saveProfileBtn" hidden>Save</button>
      </div>
      </form>
      </div>
    </div>

    <div class="cards-container booking-area">
    <div class="card booking-card">
      <h2 class="card-heading">Booking details</h2>
      <div class="booking-line"><span>Room Number</span><strong><?php echo htmlspecialchars($booking['room_number'] ?? 'Not assigned'); ?></strong></div>
      <div class="booking-line"><span>Bed Position</span><strong><?php echo htmlspecialchars($booking['bed_position'] ?? 'Not assigned'); ?></strong></div>
      <div class="booking-line"><span>Floor Number</span><strong><?php echo htmlspecialchars($booking['floor'] ?? 'Not assigned'); ?></strong></div>
      <div class="booking-line"><span>Block</span><strong><?php echo htmlspecialchars($booking['block_name'] ?? 'Not assigned'); ?></strong></div>
      <?php if (empty($booking) && !empty($available_bed)): ?>
        <div style="margin-top:12px; text-align:right;">
          <a class="button" href="payment.php?bed_id=<?php echo urlencode($available_bed['bed_id']); ?>">Book</a>
        </div>
      <?php endif; ?>
    </div>
    <div class="card status-card">
      <h2 class="card-heading">Status</h2>
      <div class="status-row">
      <span>Current booking status</span>
      <button class="status-button <?php echo $statusClass; ?>" type="button"><?php echo htmlspecialchars($statusLabel); ?></button>
    </div>
    </div>
    </div>
  </main>

  <script>
    const editButton = document.getElementById('editProfileBtn');
    const saveButton = document.getElementById('saveProfileBtn');
    const profileFields = document.querySelectorAll('.profile-input');

    if (editButton) {
      editButton.addEventListener('click', () => {
        profileFields.forEach(field => {
          field.removeAttribute('readonly');
          field.removeAttribute('disabled');
          field.classList.add('editable');
        });
        editButton.hidden = true;
        saveButton.hidden = false;
      });
    }
  </script>
</body>
</html>
