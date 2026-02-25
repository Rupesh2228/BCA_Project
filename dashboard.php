<?php
session_start();
include 'connection.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $email = $_POST['email'] ?? '';
    $dob = $_POST['dob'] ?? '';
    $address = $_POST['address'] ?? '';
    
    // Handle file upload
    $users_img = 'default.png';
    if (isset($_FILES['users_img']) && $_FILES['users_img']['error'] == 0) {
        $upload_dir = 'uploads/';
        $filename = time() . '_' . basename($_FILES['users_img']['name']);
        move_uploaded_file($_FILES['users_img']['tmp_name'], $upload_dir . $filename);
        $users_img = $filename;
    }

    // Check if user already has a record
    $checkQuery = $conn->prepare("SELECT id FROM users_dash_info WHERE id=?");
    $checkQuery->bind_param("i", $user_id);
    $checkQuery->execute();
    $checkResult = $checkQuery->get_result();

    if ($checkResult->num_rows > 0) {
        // Update existing record
        $updateQuery = $conn->prepare("UPDATE users_dash_info SET name=?, phone=?, email=?, dob=?, address=?, users_img=? WHERE id=?");
        $updateQuery->bind_param("ssssssi", $name, $phone, $email, $dob, $address, $users_img, $user_id);
        $updateQuery->execute();
    } else {
        // Insert new record
        $insertQuery = $conn->prepare("INSERT INTO users_dash_info (id, name, phone, email, dob, address, users_img, password) VALUES (?, ?, ?, ?, ?, ?, ?, 'default')");
        $insertQuery->bind_param("issssss", $user_id, $name, $phone, $email, $dob, $address, $users_img);
        $insertQuery->execute();
    }
}

// Fetch user info
$userQuery = $conn->prepare("SELECT * FROM users_dash_info WHERE id=?");
$userQuery->bind_param("i", $user_id);
$userQuery->execute();
$user = $userQuery->get_result()->fetch_assoc();
$user = array_merge([
    'name'=>'','phone'=>'','email'=>'','dob'=>'','address'=>'','users_img'=>'default.png'
], $user);

// Fetch stats
$upcomingQuery = $conn->prepare("SELECT COUNT(*) FROM events e JOIN tickets t ON e.id=t.event_id WHERE t.user_id=? AND e.event_date>=CURDATE()");
$upcomingQuery->bind_param("i",$user_id); $upcomingQuery->execute();
$upcoming = $upcomingQuery->get_result()->fetch_row()[0] ?? 0;

$bookedQuery = $conn->prepare("SELECT COUNT(*) FROM tickets WHERE user_id=?");
$bookedQuery->bind_param("i",$user_id); $bookedQuery->execute();
$booked = $bookedQuery->get_result()->fetch_row()[0] ?? 0;

$historyQuery = $conn->prepare("SELECT COUNT(*) FROM events e JOIN tickets t ON e.id=t.event_id WHERE t.user_id=? AND e.event_date<CURDATE()");
$historyQuery->bind_param("i",$user_id); $historyQuery->execute();
$history = $historyQuery->get_result()->fetch_row()[0] ?? 0;

// Fetch tickets
$ticketsQuery = $conn->prepare("SELECT t.*, e.event_name, e.event_date, t.file_path FROM tickets t JOIN events e ON t.event_id=e.id WHERE t.user_id=?");
$ticketsQuery->bind_param("i",$user_id); $ticketsQuery->execute();
$tickets = $ticketsQuery->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>User Dashboard</title>
<link href="https://fonts.googleapis.com/css2?family=Jost:wght@500&display=swap" rel="stylesheet">
<style>
* { margin:0; padding:0; box-sizing:border-box; font-family:'Jost',sans-serif; }
body { display:flex; min-height:100vh; background:#f2f3f5; }
.sidebar { width:220px; background:#1e1e2f; color:white; padding:20px; }
.sidebar h2 { text-align:center; margin-bottom:30px; font-size:22px; }
.sidebar a { color:white; display:block; padding:12px 0; text-decoration:none; margin-bottom:10px; border-radius:5px; padding-left:15px; }
.sidebar a:hover { background:#2e2e4d; }
.main-content { flex:1; padding:30px; }
.profile { display:flex; align-items:center; gap:15px; margin-bottom:20px; }
.profile img { width:80px; height:80px; border-radius:50%; object-fit:cover; }
.cards { display:flex; flex-wrap:wrap; gap:20px; margin-bottom:30px; }
.card { background:white; padding:20px; border-radius:10px; width:200px; text-align:center; box-shadow:0 2px 5px rgba(0,0,0,0.1); }
.card h3 { margin-bottom:10px; color:#555; }
.card p { font-size:22px; font-weight:bold; }
.user-info, .user-form { background:white; padding:20px; border-radius:10px; margin-bottom:30px; max-width:600px; }
.user-info h2, .user-form h2 { margin-bottom:15px; color:#333; }
.user-info p { margin-bottom:10px; color:#555; font-size:16px; }
.user-form input { width:100%; padding:10px; margin-bottom:10px; border:1px solid #ccc; border-radius:5px; }
.user-form button { padding:10px 20px; background:#3498db; color:white; border:none; border-radius:5px; cursor:pointer; }
.user-form button:hover { background:#2980b9; }
.ticket-section { background:white; padding:20px; border-radius:10px; margin-bottom:30px; }
.ticket-section h2 { margin-bottom:15px; color:#333; }
.ticket-section table { width:100%; border-collapse:collapse; }
.ticket-section table, th, td { border:1px solid #ccc; }
.ticket-section th, td { padding:10px; text-align:center; }
.ticket-section a { color:#3498db; text-decoration:none; }
.ticket-section a:hover { text-decoration:underline; }
.logout { text-align:right; margin-top:20px; }
.btn-logout { padding:10px 20px; background:#e74c3c; color:white; text-decoration:none; border-radius:5px; }
.btn-logout:hover { background:#c0392b; }
</style>
</head>
<body>

<div class="sidebar">
<h2>Perfect Day</h2>
<a href="dashboard.php">Dashboard</a>
<a href="Event.html">Event</a>
<a href="logout.php">Logout</a>
</div>

<div class="main-content">

<div class="profile">
<img src="uploads/<?= htmlspecialchars($user['users_img']) ?>" alt="Profile Image">
<h2><?= htmlspecialchars($user['name']) ?></h2>
</div>

<div class="cards">
<div class="card"><h3>Upcoming Events</h3><p><?= $upcoming ?></p></div>
<div class="card"><h3>Booked Tickets</h3><p><?= $booked ?></p></div>
<div class="card"><h3>Event History</h3><p><?= $history ?></p></div>
</div>

<?php if(empty($user['name'])): ?>
<!-- Show form if user has not submitted details -->
<div class="user-form">
<h2>Enter Your Details</h2>
<form action="" method="POST" enctype="multipart/form-data">
<input type="text" name="name" placeholder="Full Name" required>
<input type="text" name="phone" placeholder="Phone" required>
<input type="email" name="email" placeholder="Email" required>
<input type="date" name="dob" required>
<input type="text" name="address" placeholder="Address" required>
<input type="file" name="users_img">
<button type="submit">Submit</button>
</form>
</div>
<?php else: ?>
<!-- Show read-only details with Edit option -->
<div class="user-info">
<h2>My Details</h2>
<p><strong>Name:</strong> <?= htmlspecialchars($user['name']) ?></p>
<p><strong>Phone:</strong> <?= htmlspecialchars($user['phone']) ?></p>
<p><strong>Email:</strong> <?= htmlspecialchars($user['email']) ?></p>
<p><strong>DOB:</strong> <?= htmlspecialchars($user['dob']) ?></p>
<p><strong>Address:</strong> <?= htmlspecialchars($user['address']) ?></p>
<form action="" method="POST" style="margin-top:15px;">
<button type="submit" name="edit">Edit Details</button>
</form>
</div>
<?php endif; ?>

<div class="ticket-section">
<h2>My Tickets</h2>
<table>
<tr><th>Event</th><th>Date</th><th>Download</th></tr>
<?php while($row = $tickets->fetch_assoc()): ?>
<tr>
<td><?= htmlspecialchars($row['event_name']) ?></td>
<td><?= htmlspecialchars($row['event_date']) ?></td>
<td>
<?php if(!empty($row['file_path'])): ?>
<a href="<?= htmlspecialchars($row['file_path']) ?>" download>Download</a>
<?php else: ?>N/A<?php endif; ?>
</td>
</tr>
<?php endwhile; ?>
</table>
</div>

<div class="logout">
<a href="logout.php" class="btn-logout">Logout</a>
</div>

</div>
</body>
</html>