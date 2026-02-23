<?php
session_start();

// If not logged in, send back to login page
if (!isset($_SESSION['user'])) {
    header("Location: login_signup.php");
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
  <title>Dashboard</title>
</head>
<body>
  <h1>Welcome, <?php echo htmlspecialchars($_SESSION['user']); ?>!</h1>
  <p>You are now logged in.</p>
  <a href="logout.php">Logout</a>
</body>
</html>