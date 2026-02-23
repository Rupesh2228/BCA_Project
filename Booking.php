<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Connect to MySQL
$conn = new mysqli("localhost", "root", "", "event_db");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Only handle POST requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullName = $_POST['fullName'] ?? '';
    $email = $_POST['email'] ?? '';
    $eventType = $_POST['eventType'] ?? '';

    // Prepared statement for safety
    $stmt = $conn->prepare("INSERT INTO bookings (fullName, email, eventType) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $fullName, $email, $eventType);

    if ($stmt->execute()) {
        echo "Registration successful!<br>";
        echo "Name: " . htmlspecialchars($fullName) . "<br>";
        echo "Email: " . htmlspecialchars($email) . "<br>";
        echo "Event: " . htmlspecialchars($eventType);
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
} else {
    echo "Please submit the form first!";
}

$conn->close();
?>
