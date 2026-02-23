<?php
include 'connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $pass  = $_POST['pswd'];

    $stmt = $conn->prepare("SELECT * FROM users WHERE email=?");
    if (!$stmt) {
        header("Location: login_signup.php?status=error");
        exit;
    }
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if (password_verify($pass, $row['password'])) {
            $stmt->close();
            $conn->close();
            header("Location: login_signup.php?status=login_success&user=" . urlencode($row['username']));
        } else {
            $stmt->close();
            $conn->close();
            header("Location: login_signup.php?status=wrong_password");
        }
    } else {
        $stmt->close();
        $conn->close();
        header("Location: login_signup.php?status=no_user");
    }
    exit;
}
?>