<?php
include 'connection.php';

// Check if form submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $fullName  = $_POST['fullName'];
    $email     = $_POST['email'];
    $phone     = $_POST['phone'];
    $eventType = $_POST['eventType'];
    $address   = $_POST['address'];
    $comments  = $_POST['comments'];

    $sql = "INSERT INTO registrations 
            (fullName, email, phone, eventType, address, comments)
            VALUES (?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssss", 
        $fullName, 
        $email, 
        $phone, 
        $eventType, 
        $address, 
        $comments
    );

    if ($stmt->execute()) {
        echo "<script>
                alert('🎉 Registration Successful!');
                window.location.href='Register.html';
              </script>";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}

$conn->close();
?>

?>