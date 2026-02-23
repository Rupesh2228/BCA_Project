<?php
session_start();
include("db.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch user info
$sql = "SELECT * FROM users WHERE id=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Count tickets purchased
$sqlTickets = "SELECT SUM(quantity) AS total_tickets FROM tickets WHERE user_id=?";
$stmtTickets = $conn->prepare($sqlTickets);
$stmtTickets->bind_param("i", $user_id);
$stmtTickets->execute();
$resultTickets = $stmtTickets->get_result();
$tickets = $resultTickets->fetch_assoc();
$totalTickets = $tickets['total_tickets'] ?? 0;
?>

<!DOCTYPE html>
<html>
<head>
    <title><?php echo $user['username']; ?> - Profile</title>
    <style>
        body { background: #121212; color: #fff; font-family: Arial; }
        .profile { text-align: center; margin-top: 50px; }
        .profile img { border-radius: 50%; width: 120px; height: 120px; }
        .stats { display: flex; justify-content: center; gap: 30px; margin-top: 20px; }
        .btn { background: #333; color: #fff; padding: 8px 15px; border: none; cursor: pointer; margin: 5px; }
        .btn:hover { background: #555; }
    </style>
</head>
<body>
    <div class="profile">
        <img src="default_profile.png" alt="Profile Picture">
        <h2><?php echo htmlspecialchars($user['fullname']); ?></h2>
        <p>@<?php echo htmlspecialchars($user['username']); ?></p>

        <div class="stats">
            <div><?php echo $user['posts']; ?> Events Created</div>
            <div><?php echo $totalTickets; ?> Tickets Bought</div>
        </div>

        <button class="btn" onclick="window.location.href='edit_profile.php'">Edit Profile</button>
        <button class="btn" onclick="window.location.href='logout.php'">Logout</button>
    </div>
</body>
</html>