<?php
session_start();
include 'connection.php';

if(!isset($_SESSION['user_id'])){
    header("Location: login_signup.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch user profile
$stmtUser = $conn->prepare("SELECT * FROM users_dash_info WHERE id=?");
$stmtUser->bind_param("i",$user_id);
$stmtUser->execute();
$user = $stmtUser->get_result()->fetch_assoc();
$user = array_merge(['name'=>'','phone'=>'','email'=>'','dob'=>'','address'=>'','users_img'=>'default.png'],$user??[]);

// Profile update
if($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['save'])){
    $name = trim($_POST['name']);
    $phone = trim($_POST['phone']);
    $email = trim($_POST['email']);
    $dob = trim($_POST['dob']);
    $address = trim($_POST['address']);

    if(!$name || !$phone || !$email || !$dob || !$address){
        echo "<script>alert('All fields are required!');window.location.href='dashboard.php';</script>";
        exit;
    }

    $users_img = $user['users_img'];
    if(isset($_FILES['users_img']) && $_FILES['users_img']['error']==0){
        $file_name = $_FILES['users_img']['name'];
        $file_tmp = $_FILES['users_img']['tmp_name'];
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        $allowed = ['jpg','jpeg','png','gif'];
        if(in_array($file_ext,$allowed)){
            $new_name = 'user_'.$user_id.'.'.$file_ext;
            if(!is_dir('uploads')) mkdir('uploads',0777,true);
            move_uploaded_file($file_tmp,'uploads/'.$new_name);
            $users_img = $new_name;
        }
    }

    $stmtCheck = $conn->prepare("SELECT id FROM users_dash_info WHERE id=?");
    $stmtCheck->bind_param("i",$user_id);
    $stmtCheck->execute();
    $resCheck = $stmtCheck->get_result();

    if($resCheck->num_rows>0){
        $stmtUpdate = $conn->prepare("UPDATE users_dash_info SET name=?, phone=?, email=?, dob=?, address=?, users_img=? WHERE id=?");
        $stmtUpdate->bind_param("ssssssi",$name,$phone,$email,$dob,$address,$users_img,$user_id);
    } else {
        $stmtUpdate = $conn->prepare("INSERT INTO users_dash_info (id,name,phone,email,dob,address,users_img) VALUES (?,?,?,?,?,?,?)");
        $stmtUpdate->bind_param("issssss",$user_id,$name,$phone,$email,$dob,$address,$users_img);
    }
    $stmtUpdate->execute();
    header("Location: dashboard.php?success=1");
    exit;
}

// Fetch tickets
$stmtTickets = $conn->prepare("SELECT * FROM event_tickets WHERE user_id=? ORDER BY booked_at DESC");
$stmtTickets->bind_param("i",$user_id);
$stmtTickets->execute();
$tickets = $stmtTickets->get_result();

?>
<!DOCTYPE html>
<html>
<head>
    <title>User Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Jost:wght@400;600&display=swap" rel="stylesheet">
    <style>
        *{
        margin:0;
        padding:0;
        box-sizing:border-box;
        font-family:'Jost',sans-serif;
        }
        body{
        background:#f4f6f9;
        }
        .container{
        display:flex;
        min-height:100vh;
        }
        .sidebar{
        width:220px;
        background:#111827;
        color:#fff;
        padding:25px;
        }
        .sidebar h2{
        text-align:center;
        margin-bottom:30px;
        font-size:24px;
        color:#f59e0b;
        }
        .sidebar a{
        display:block;
        color:#fff;
        padding:12px 20px;
        margin-bottom:12px;
        border-radius:8px;
        text-decoration:none;
        }
        .sidebar a:hover{
            background:#374151;
        }
        .main{
            flex:1;
            padding:35px;
        }
        .header{
            margin-bottom:25px;
        }
        .header h2{
            font-size:28px;
            color:#111;
        }
        .profile-box, .tickets-box{
            background:#fff;
            padding:30px;
            border-radius:15px;
            box-shadow:0 8px 20px rgba(0,0,0,0.08);
            margin-bottom:40px;
        }
        .profile-box h3,.tickets-box h3{
            margin-bottom:20px;
            color:#111;
            font-size:22px;
            text-align:center;
        }
        .profile-box input[type="text"], .profile-box input[type="email"], .profile-box input[type="date"], .profile-box input[type="file"]{
            width:100%;
            padding:12px 15px;
            border-radius:8px;
            border:1px solid #ccc;
            margin-bottom:15px;
        }
        .profile-box button{
            width:100%;
            background:#2563eb;
            color:#fff;
            border:none;
            padding:12px 0;
            border-radius:10px;
            font-weight:600;
            font-size:16px;
            cursor:pointer;
        }
        .profile-img{
            width:130px;
            height:130px;
            border-radius:50%;
            object-fit:cover;
            border:3px solid #f59e0b;
            margin-bottom:20px;
            display:block;
            margin-left:auto;
            margin-right:auto;
        }
        table{
            width:100%;
            border-collapse:collapse;
        }
        th,td{
            padding:12px;
            text-align:center;
            border-bottom:1px solid #e5e7eb;
        }
        th{
            background:#f3f4f6;
        }
        td a{
            color:#2563eb;
            text-decoration:none;
            font-weight:600;
        }
        td a:hover{
            text-decoration:underline;
        }
        .logout{
            display:inline-block;
            margin-top:30px;
            background:#ef4444;
            color:#fff;
            padding:12px 25px;
            border-radius:10px;
            text-decoration:none;
            font-weight:600;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="sidebar">
        <h2>Perfect Day</h2>
        <a href="dashboard.php">Dashboard</a>
        <a href="Landingpage.html">Home</a>
        <a href="Event.html">Events</a>
        <a href="logout.php">Logout</a>
    </div>
    <div class="main">
        <div class="header">
            <h2>Welcome <?= htmlspecialchars($user['name']) ?: 'User'; ?>!</h2>
        </div>

        <?php if(isset($_GET['success'])): ?>
        <div style="background:#d1fae5;color:#065f46;padding:15px;border-radius:8px;text-align:center;margin-bottom:20px;">
            🎉 Booking Successful! Your ticket has been added.
        </div>
        <?php endif; ?>

        <!-- Profile Section -->
        <div class="profile-box">
            <h3>Update Your Profile</h3>
            <form method="POST" enctype="multipart/form-data">
                <img class="profile-img" src="uploads/<?= htmlspecialchars($user['users_img']) ?>" alt="Profile Image">
                <input type="text" name="name" placeholder="Full Name" value="<?= htmlspecialchars($user['name']) ?>" required>
                <input type="text" name="phone" placeholder="Phone" value="<?= htmlspecialchars($user['phone']) ?>" required>
                <input type="email" name="email" placeholder="Email" value="<?= htmlspecialchars($user['email']) ?>" required>
                <input type="date" name="dob" value="<?= htmlspecialchars($user['dob']) ?>" required>
                <input type="text" name="address" placeholder="Address" value="<?= htmlspecialchars($user['address']) ?>" required>
                <input type="file" name="users_img" accept="image/*">
                <button type="submit" name="save">Save Changes</button>
            </form>
        </div>

        <!-- Tickets Section -->
        <div class="tickets-box">
            <h3>My Tickets</h3>
            <?php if($tickets->num_rows>0): ?>
            <table>
                <tr>
                    <th>Event</th>
                    <th>Date</th>
                    <th>Price</th>
                    <th>Ticket Code</th>
                    <th>Booked At</th>
                    <th>Download</th>
                </tr>
                <?php while($t=$tickets->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($t['event_name']) ?></td>
                    <td><?= htmlspecialchars($t['event_date']) ?></td>
                    <td>Rs. <?= htmlspecialchars($t['price']) ?></td>
                    <td><?= htmlspecialchars($t['ticket_code']) ?></td>
                    <td><?= htmlspecialchars($t['booked_at']) ?></td>
                    <td><a href="download_ticket.php?id=<?= $t['id'] ?>">Download</a></td>
                </tr>
                <?php endwhile; ?>
            </table>
            <?php else: ?>
            <p style="text-align:center;">No tickets booked yet.</p>
            <?php endif; ?>
        </div>

        <a href="logout.php" class="logout">Logout</a>
    </div>
</div>
</body>
</html>