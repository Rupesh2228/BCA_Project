<?php
include 'connection.php';

// Form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fullName = trim($_POST['fullName']);
    $email    = trim($_POST['email']);
    $phone    = trim($_POST['phone']);
    $whatsapp = trim($_POST['whatsapp']);
    $eventType= trim($_POST['eventType']);

    // Simple validation
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        die("Invalid email format");
    }

    if (!preg_match("/^[+0-9]{10,15}$/", $phone)) {
        die("Invalid phone number format");
    }

    if (!preg_match("/^[+0-9]{10,15}$/", $whatsapp)) {
        die("Invalid WhatsApp number format");
    }

    if (empty($eventType)) {
        die("Please select an event");
    }

    // Prepare statement
    $stmt = $conn->prepare("INSERT INTO bookings (fullName, email, phone, whatsapp, eventType) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $fullName, $email, $phone, $whatsapp, $eventType);

    if ($stmt->execute()) {
        echo "<script>alert('Registration successful!'); window.location='landingpage.html';</script>";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}

$conn->close();
?>