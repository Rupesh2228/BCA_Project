<?php
session_start();
if(!isset($_SESSION['user_id'])){
    header("Location: login_signup.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Download Ticket</title>

<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script src="download_ticket.js"></script>

<style>
body { font-family: Arial, sans-serif; background: #f5f5f5; padding: 30px; text-align:center; }
h1 { color: #333; margin-bottom:20px; }
#ticket {
    width: 400px;
    margin: 0 auto 20px auto;
    padding: 20px;
    background: #fff;
    border: 3px solid #28a745;
    border-radius: 10px;
    text-align:left;
    box-shadow: 0 4px 10px rgba(0,0,0,0.1);
}
#ticket h2 { text-align:center; color:#28a745; margin-bottom:15px; }
#ticket p { margin:5px 0; font-size:14px; }
.btn-download { padding: 10px 20px; background: #28a745; color: #fff; border: none; cursor: pointer; border-radius: 5px; font-size:16px; }
.btn-download:hover { background: #218838; }
</style>
</head>
<body>

<h1>Welcome, <?= $_SESSION['user_name'] ?? "User" ?></h1>

<div id="ticket">
  <h2>🎫 EVENT TICKET</h2>
  <p><strong>Event:</strong> <span id="event_name">Loading...</span></p>
  <p><strong>Name:</strong> <span id="full_name">Loading...</span></p>
  <p><strong>Email:</strong> <span id="email">Loading...</span></p>
  <p><strong>Phone:</strong> <span id="phone">Loading...</span></p>
  <p><strong>Ticket ID:</strong> <span id="ticket_code">Loading...</span></p>
  <p><strong>Date:</strong> <span id="event_date">Loading...</span></p>
</div>

<button onclick="downloadTicket()" class="btn-download">Download Ticket 🎫</button>

</body>
</html>