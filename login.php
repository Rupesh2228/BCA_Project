<?php
session_start();
include 'connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $email = trim($_POST['email']);
    $password = trim($_POST['pswd']);

    $stmt = $conn->prepare("SELECT id, password FROM users WHERE email=?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 0) {
        echo "<script>alert('No user found with that email.'); window.location.href='login_signup.php';</script>";
        exit;
    }

    $stmt->bind_result($id, $hashedPassword);
    $stmt->fetch();

    if (password_verify($password, $hashedPassword)) {
        session_regenerate_id(true); // Fix session issues
        $_SESSION['user_id'] = $id;
        header("Location: dashboard.php");
        exit;
    } else {
        echo "<script>alert('Invalid password.'); window.location.href='login_signup.php';</script>";
        exit;
    }
}
?>