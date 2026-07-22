<?php
session_start();
require_once 'db.php';
$error = '';

$bed_id = isset($_GET['bed_id']) ? intval($_GET['bed_id']) : (isset($_POST['bed_id']) ? intval($_POST['bed_id']) : 0);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $first_name = trim($_POST['first_name'] ?? '');
  $last_name = trim($_POST['last_name'] ?? '');
  $gender = trim($_POST['gender'] ?? '');
  $contact = trim($_POST['contact'] ?? '');
  $email = trim($_POST['email'] ?? '');
  $password = $_POST['password'] ?? '';
  $confirm_password = $_POST['confirm_password'] ?? '';

  if ($first_name === '' || $last_name === '' || $gender === '' || $contact === '' || $email === '' || $password === '' || $confirm_password === '') {
    $error = 'Please complete all fields.';
  } elseif (!in_array($gender, ['male', 'female'], true)) {
    $error = 'Please select either male or female.';
  } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $error = 'Please enter a valid email address.';
  } elseif ($password !== $confirm_password) {
    $error = 'Passwords do not match.';
  } else {
    $stmt = $conn->prepare('SELECT user_id FROM users WHERE email = ? LIMIT 1');
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
      $error = 'An account with this email already exists.';
    } else {
      $full_name = $first_name . ' ' . $last_name;
      $hash = password_hash($password, PASSWORD_DEFAULT);
      $role = 'student';

      $insert = $conn->prepare('INSERT INTO users (full_name, email, phone, password, role, gender) VALUES (?, ?, ?, ?, ?, ?)');
      $insert->bind_param('ssssss', $full_name, $email, $contact, $hash, $role, $gender);
      if ($insert->execute()) {
        $_SESSION['user_id'] = $insert->insert_id;
        $_SESSION['full_name'] = $full_name;
        $_SESSION['email'] = $email;
        $_SESSION['role'] = $role;
        if ($bed_id > 0) {
          header('Location: payment.php?bed_id=' . $bed_id);
          exit;
        }
        header('Location: userdb.php');
        exit;
      }
      $error = 'Unable to create account, please try again.';
    }
    $stmt->close();
  }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta charset="UTF-8" />
  <title>Register</title>
  <link rel="stylesheet" href="registerstylesheet.css">
</head>

<body>
  <div class="login-container">
    <h2>Create Your Account</h2>
    <?php if ($error): ?>
      <p class="error"><?php echo htmlspecialchars($error); ?></p>
    <?php endif; ?>
    <form method="POST" action="register.php<?php echo $bed_id > 0 ? '?bed_id=' . $bed_id : ''; ?>">
      <input type="text" name="first_name" placeholder="First Name" required>
      <input type="text" name="last_name" placeholder="Last Name" required>
      <select name="gender" required>
        <option value="" disabled selected>Select Gender</option>
        <option value="male">Male</option>
        <option value="female">Female</option>
      </select>
      <input type="text" name="contact" placeholder="Phone No" required>
      <input type="email" name="email" placeholder="Email" required>
      <input type="password" name="password" placeholder="Password" required>
      <input type="password" name="confirm_password" placeholder="Confirm password" required>
      <button type="submit">CREATE ACCOUNT</button>
    </form>
    <p>Already have an account? <a href="login.php<?php echo $bed_id > 0 ? '?bed_id=' . $bed_id : ''; ?>">Sign in</a></p>
  </div>
</body>

</html>