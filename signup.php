<?php
session_start();
include 'connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $username = trim($_POST['name'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $password = trim($_POST['pswd'] ?? '');

    if (empty($username) || empty($email) || empty($password)) {
        header("Location: landingpage.html?status=error_empty");
        exit;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        header("Location: landingpage.html?status=error_invalid_email");
        exit;
    }

    if (strlen($password) < 6) {
        header("Location: landingpage.html?status=error_short_password");
        exit;
    }

    // Check if email exists
    $check = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $check->bind_param("s", $email);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {
        header("Location: landingpage.html?status=exists");
        exit;
    }

    $hashed = password_hash($password, PASSWORD_DEFAULT);

    // Insert user
    $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $username, $email, $hashed);

    if ($stmt->execute()) {
        // ✅ Redirect with a friendly success message
        header("Location: landingpage.html?status=signup_success&msg=" . urlencode("Signup successful! Welcome, $username!"));
    } else {
        header("Location: landingpage.html?status=error_insert");
    }

    $stmt->close();
    $check->close();
    $conn->close();
}
?>