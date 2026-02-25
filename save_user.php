<?php
session_start();
include 'connection.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Collect POST data
$name    = trim($_POST['name']);
$phone   = trim($_POST['phone']);
$email   = trim($_POST['email']);
$dob     = $_POST['dob'];
$address = trim($_POST['address']);

// Handle file upload if a new image is provided
$users_img = null;
if (isset($_FILES['users_img']) && $_FILES['users_img']['error'] === 0) {
    $allowed = ['jpg','jpeg','png','gif'];
    $file_name = $_FILES['users_img']['name'];
    $file_tmp  = $_FILES['users_img']['tmp_name'];
    $file_ext  = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

    if (in_array($file_ext, $allowed)) {
        $new_name = 'user_'.$user_id.'_'.time().'.'.$file_ext;
        $destination = 'uploads/' . $new_name;
        if (move_uploaded_file($file_tmp, $destination)) {
            $users_img = $new_name;
        }
    }
}

// Update query
if ($users_img) {
    $stmt = $conn->prepare("UPDATE users_dash_info SET name=?, phone=?, email=?, dob=?, address=?, users_img=? WHERE id=?");
    $stmt->bind_param("ssssssi", $name, $phone, $email, $dob, $address, $users_img, $user_id);
} else {
    $stmt = $conn->prepare("UPDATE users_dash_info SET name=?, phone=?, email=?, dob=?, address=? WHERE id=?");
    $stmt->bind_param("sssssi", $name, $phone, $email, $dob, $address, $user_id);
}

if ($stmt->execute()) {
    $_SESSION['success'] = "Profile updated successfully!";
} else {
    $_SESSION['error'] = "Failed to update profile.";
}

$stmt->close();
$conn->close();

// Redirect back to dashboard
header("Location: dashboard.php");
exit;
?>