<?php
include 'connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $fullName  = $_POST['fullName'];
    $email     = $_POST['email'];
    $phone     = $_POST['phone'];
    $age       = (int)$_POST['age']; // cast to integer for safety
    $eventType = $_POST['eventType'];
    $address   = $_POST['address'];
    $comments  = $_POST['comments'];

    $sql = "INSERT INTO event_bookings 
            (full_name, email, phone, age, event_type, address, comments) 
            VALUES (?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }

    // bind_param types: s = string, i = integer
    $stmt->bind_param("sssisss",
        $fullName, $email, $phone, $age, $eventType, $address, $comments
    );

    if ($stmt->execute()) {
        echo "<script>
                alert('🎉 Booking Successful!');
                window.location.href='landingpage.html';
              </script>";
    } else {
        echo "Execute Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>