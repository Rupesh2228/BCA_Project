<?php
session_start();
include 'connection.php';

if(!isset($_SESSION['user_id'])){
    header("Location: login_signup.php");
    exit;
}

if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['book'])) {

    $user_id = $_SESSION['user_id'];
    $fullName = trim($_POST['fullName']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $age = (int)$_POST['age'];
    $price = (int)$_POST['price'];

    // Event info from index
    $events = [
        ['name'=>'Music Fest 2026', 'date'=>'2026-02-21', 'price'=>1299],
        ['name'=>'DJ Night Party', 'date'=>'2026-03-05', 'price'=>999],
        ['name'=>'Food Carnival', 'date'=>'2026-04-10', 'price'=>799]
    ];

    $event_index = (int)$_POST['event_index'];
    if(!isset($events[$event_index])){
        die("Invalid event selected");
    }

    $event_name = $events[$event_index]['name'];
    $event_date = $events[$event_index]['date'];

    $ticket_code = "TKT-" . strtoupper(uniqid());

    $stmt = $conn->prepare("INSERT INTO event_tickets
        (user_id, full_name, email, phone, age, event_name, event_date, price, ticket_code)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");

    $stmt->bind_param("isssissis",
        $user_id, $fullName, $email, $phone, $age, $event_name, $event_date, $price, $ticket_code
    );

  if($stmt->execute()){
    echo "<script>
            alert('🎉 Booking Successful! Your ticket has been generated.');
            window.location.href='dashboard.php';
          </script>";
    exit;
}else {
        echo "<script>
                alert('Error: Could not complete booking. Please try again.');
                window.location.href='register.php';
              </script>";
    }
}
?>