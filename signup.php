<?php
session_start();
include 'connection.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email']);
    $password = trim($_POST['pswd']);

    // Check if user exists
    $stmt = $conn->prepare("SELECT id, password FROM users WHERE email=?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows == 0) {
        echo "<script>
                alert('No user found with that email.');
                window.location.href='landingpage.html';
              </script>";
        exit;
    }

    $stmt->bind_result($id, $hashedPassword);
    $stmt->fetch();

    if (password_verify($password, $hashedPassword)) {
        $_SESSION['user_id'] = $id;
        echo "<script>
                alert('Login successful! Redirecting to landing page...');
                window.location.href='landingpage.html';
              </script>";
    } else {
        echo "<script>
                alert('Invalid password. Please try again.');
                window.location.href='landingpage.html';
              </script>";
    }

    $stmt->close();
    $conn->close();
}
?>