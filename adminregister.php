<?php
session_start();
require_once 'db.php';
$error = '';

$admin_check = $conn->query("SELECT COUNT(*) AS cnt FROM users WHERE role = 'admin'");
$admin_exists = false;
if ($admin_check) {
  $row = $admin_check->fetch_assoc();
  $admin_exists = ((int)$row['cnt'] > 0);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if ($admin_exists) {
    $error = 'Admin registration is disabled because an admin account already exists.';
  } else {
    $full_name = trim($_POST['full_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $gender = trim($_POST['gender'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    if ($full_name === '' || $email === '' || $phone === '' || $gender === '' || $password === '' || $confirm_password === '') {
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
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $role = 'admin';
            $insert = $conn->prepare('INSERT INTO users (full_name, email, phone, password, role, gender) VALUES (?, ?, ?, ?, ?, ?)');
            $insert->bind_param('ssssss', $full_name, $email, $phone, $hash, $role, $gender);

            if ($insert->execute()) {
                $_SESSION['user_id'] = $insert->insert_id;
                $_SESSION['full_name'] = $full_name;
                $_SESSION['email'] = $email;
                $_SESSION['role'] = $role;
                header('Location: admindb.php');
                exit;
            }
            $error = 'Unable to create admin account, please try again.';
            $insert->close();
        }
        $stmt->close();
    }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta charset="UTF-8" />
  <title>Admin Register</title>
  <link rel="stylesheet" href="adminloginstylesheet.css">
</head>

<body>
  <div class="login-container">
    <h2>Create Admin Account</h2>
    <?php if ($error): ?>
      <p class="error"><?php echo htmlspecialchars($error); ?></p>
    <?php endif; ?>
    <?php if ($admin_exists): ?>
      <p class="error">Admin account already exists. Registration disabled.</p>
      <p><a href="adminlogin.php">Go to admin login</a></p>
    <?php else: ?>
    <form method="POST">
      <input type="text" name="full_name" placeholder="Full Name" required>
      <select name="gender" required>
        <option value="" disabled selected>Select Gender</option>
        <option value="male">Male</option>
        <option value="female">Female</option>
      </select>
      <input type="text" name="phone" placeholder="Phone Number" required>
      <input type="email" name="email" placeholder="Email" required>
      <input type="password" name="password" placeholder="Password" required>
      <input type="password" name="confirm_password" placeholder="Confirm Password" required>
      <button type="submit">CREATE ACCOUNT</button>
    </form>
    <?php endif; ?>
    <p>Already have an admin account? <a href="adminlogin.php">Sign in</a></p>
  </div>
</body>

</html>
