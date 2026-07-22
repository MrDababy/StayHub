<?php
session_start();
require_once 'db.php';
$error = '';

$bed_id = isset($_GET['bed_id']) ? intval($_GET['bed_id']) : (isset($_POST['bed_id']) ? intval($_POST['bed_id']) : 0);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $email = trim($_POST['email'] ?? '');
  $password = $_POST['password'] ?? '';

  if ($email === '' || $password === '') {
    $error = 'Please enter both email and password.';
  } else {
    $stmt = $conn->prepare('SELECT user_id, full_name, email, password, role FROM users WHERE email = ? LIMIT 1');
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows === 1) {
      $stmt->bind_result($user_id, $full_name, $user_email, $hash, $role);
      $stmt->fetch();
      $valid = password_verify($password, $hash) || $password === $hash;
      if ($valid) {
        $_SESSION['user_id'] = $user_id;
        $_SESSION['full_name'] = $full_name;
        $_SESSION['email'] = $user_email;
        $_SESSION['role'] = $role;
        if ($role === 'admin') {
          header('Location: admindb.php');
          exit;
        }
        if ($bed_id > 0) {
          header('Location: payment.php?bed_id=' . $bed_id);
          exit;
        }
        header('Location: userdb.php');
        exit;
      }
    }
    $error = 'Invalid email or password.';
  }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta charset="UTF-8" />
  <title>Login</title>
  <link rel="stylesheet" href="loginstylesheet.css">
</head>

<body>
  <div class="login-container">
    <h2>Welcome back!</h2>
    <?php if ($error): ?>
      <p class="error"><?php echo htmlspecialchars($error); ?></p>
    <?php endif; ?>
    <form method="POST" action="login.php<?php echo $bed_id > 0 ? '?bed_id=' . $bed_id : ''; ?>">
      <input type="email" name="email" placeholder="Email" required>
      <input type="password" name="password" placeholder="Password" required>
      <button type="submit">SIGN IN</button>
    </form>
    <p>Don't have an account? <a href="register.php<?php echo $bed_id > 0 ? '?bed_id=' . $bed_id : ''; ?>">Create new account</a></p>
  </div>
</body>

</html>






















</html>