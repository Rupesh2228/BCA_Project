<?php
include 'connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $uname = $_POST['txt'];
    $email = $_POST['email'];
    $pass  = password_hash($_POST['pswd'], PASSWORD_DEFAULT);

    // Check if email already exists
    $check = $conn->prepare("SELECT id FROM users WHERE email=?");
    if (!$check) {
        header("Location: login_signup.php?status=error");
        exit;
    }
    $check->bind_param("s", $email);
    $check->execute();
    $result = $check->get_result();

    if ($result && $result->num_rows > 0) {
        $check->close();
        $conn->close();
        header("Location: login_signup.php?status=exists");
        exit;
    }
    $check->close();

    // Insert new user
    $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
    if (!$stmt) {
        header("Location: login_signup.php?status=error");
        exit;
    }
    $stmt->bind_param("sss", $uname, $email, $pass);

    if ($stmt->execute()) {
        $stmt->close();
        $conn->close();
        header("Location: login_signup.php?status=signup_success");
    } else {
        $stmt->close();
        $conn->close();
        header("Location: login_signup.php?status=error");
    }
    exit;
}
?>