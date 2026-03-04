<?php
session_start();
include 'connection.php';

if(!isset($_SESSION['user_id'])){
    http_response_code(401);
    echo json_encode(["error" => "Unauthorized"]);
    exit;
}

$user_id = $_SESSION['user_id'];

// fetch latest ticket for user
$stmt = $conn->prepare("SELECT * FROM event_tickets WHERE user_id=? ORDER BY id DESC LIMIT 1");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$res = $stmt->get_result();

if($res->num_rows > 0){
    $ticket = $res->fetch_assoc();
    echo json_encode($ticket);
} else {
    echo json_encode(["error" => "No ticket found"]);
}
?>