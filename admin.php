<?php
session_start();

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "project_db";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


$login_error = '';
if (isset($_POST['login_btn'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM admin WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        if ($password === $row['password']) {
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_email'] = $row['email'];
            header("Location: admin.php");
            exit;
        }
    }
    $login_error = "Invalid Email or Password";
}

if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: admin.php");
    exit;
}


if (isset($_GET['delete_id']) && isset($_SESSION['admin_logged_in'])) {
    $id = intval($_GET['delete_id']);
    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    header("Location: admin.php");
    exit;
}

if (isset($_POST['save_user']) && isset($_SESSION['admin_logged_in'])) {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $user_id = isset($_POST['user_id']) && $_POST['user_id'] != '' ? intval($_POST['user_id']) : 0;

    if ($user_id > 0) {
       
        $stmt = $conn->prepare("UPDATE users SET username=?, email=?, password=? WHERE id=?");
        $stmt->bind_param("sssi", $username, $email, $password, $user_id);
        $stmt->execute();
    } else {
        
        $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $username, $email, $password);
        $stmt->execute();
    }
    header("Location: admin.php");
    exit;
}


$users = [];
if (isset($_SESSION['admin_logged_in'])) {
    $result = $conn->query("SELECT * FROM users ORDER BY id DESC");
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $users[] = $row;
        }
    }
}


$edit_user = null;
if (isset($_GET['edit_id']) && isset($_SESSION['admin_logged_in'])) {
    $id = intval($_GET['edit_id']);
    $stmt = $conn->prepare("SELECT * FROM users WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        $edit_user = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Panel</title>
<style>
body { 
    font-family: Arial, sans-serif; 
    margin:0; 
    background:#f4f4f4;
 }
.login-wrapper { 
    display:flex; 
    justify-content:center; 
    align-items:center; 
    height:100vh; 
}
.login-box { 
    background:white; 
    padding:30px; 
    border-radius:8px; 
    box-shadow:0 0 10px rgba(0,0,0,0.1);
     width:300px; 
    }
.navbar { 
    background:#333; 
    color:white; 
    padding:15px 20px; 
    display:flex; 
    justify-content:space-between; 
}
.navbar a {
     color:white; 
     text-decoration:none; 
    }
.container { 
    display:flex; 
    min-height:100vh; 
}
.sidebar {
     width:200px; 
     background:#222; 
     color:white; 
     padding-top:20px; 
    }
.sidebar a {
     display:block; 
     padding:15px; 
     color:#aaa; 
     text-decoration:none; }
.sidebar a:hover {
     background:#444; 
     color:white;
     }
.content {
     flex:1; 
     padding:30px; 
    }
.card {
     background:white; 
     padding:20px; 
     margin-bottom:20px;
      border-radius:5px; 
      box-shadow:0 2px 5px rgba(0,0,0,0.1); 
    }
input {
     padding:10px; 
     margin:5px 0; 
     width:100%; 
     box-sizing:border-box; 
     border:1px solid #ccc; 
     border-radius:4px; 
    }
.btn {
     background:#007bff; 
     color:white;
      border:none;
       padding:10px 15px; 
       cursor:pointer; 
       border-radius:4px; 
    }
.btn-del {
     background:#dc3545; 
     color:white; 
     text-decoration:none; 
     padding:5px 10px; 
     border-radius:4px; 
     font-size:14px;
     }
table { 
    width:100%; 
    border-collapse:collapse; 
    background:white; }
th, td {
     padding:12px;
      border:1px solid #ddd; 
      text-align:left;
     }
th {
     background:#007bff;
      color:white;
     }
.error {
     color:red; 
     font-size:14px;
      }
</style>
</head>
<body>

<?php if (!isset($_SESSION['admin_logged_in'])): ?>
<!-- LOGIN FORM -->
<div class="login-wrapper">
    <div class="login-box">
        <h2 style="text-align:center;">Admin Login</h2>
        <?php if($login_error): ?>
            <p class="error"><?php echo $login_error; ?></p>
        <?php endif; ?>
        <form method="POST">
            <label>Email</label>
            <input type="email" name="email" placeholder="admin@test.com" required>
            <label>Password</label>
            <input type="password" name="password" placeholder="Enter Password" required>
            <button type="submit" name="login_btn" class="btn" style="width:100%; margin-top:10px;">Login</button>
        </form>
    </div>
</div>

<?php else: ?>
<!-- DASHBOARD -->
<div class="navbar">
    <h3>Admin Panel</h3>
    <div>
        <span>Welcome, <b><?php echo $_SESSION['admin_email']; ?></b></span>
        <a href="?logout=true" style="margin-left:15px;">Logout</a>
    </div>
</div>

<div class="container">
    <div class="sidebar">
        <a href="#">Dashboard</a>
        <a href="#">Users</a>
        <a href="#">Settings</a>
    </div>

    <div class="content">
        <h2><?php echo $edit_user ? "Edit User" : "Add User"; ?></h2>
        <div class="card">
            <form method="POST">
                <input type="hidden" name="user_id" value="<?php echo $edit_user['id'] ?? ''; ?>">
                <label>Username</label>
                <input type="text" name="username" placeholder="Enter Username" required value="<?php echo $edit_user['username'] ?? ''; ?>">
                <label>Email</label>
                <input type="email" name="email" placeholder="Enter Email" required value="<?php echo $edit_user['email'] ?? ''; ?>">
                <label>Password</label>
                <input type="text" name="password" placeholder="Enter Password" required value="<?php echo $edit_user['password'] ?? ''; ?>">
                <button type="submit" name="save_user" class="btn" style="width:100%; margin-top:10px;">
                    <?php echo $edit_user ? "Update User" : "Add User"; ?>
                </button>
            </form>
        </div>

        <h2>User List</h2>
        <div class="card">
            <table>
                <thead>
                    <tr>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(count($users) > 0): ?>
                        <?php foreach($users as $u): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($u['username']); ?></td>
                                <td><?php echo htmlspecialchars($u['email']); ?></td>
                                <td>
                                    <a href="?edit_id=<?php echo $u['id']; ?>" class="btn" style="background:#ffc107;">Edit</a>
                                    <a href="?delete_id=<?php echo $u['id']; ?>" class="btn-del" onclick="return confirm('Delete this user?');">Delete</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="3" style="text-align:center;">No users found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php endif; ?>

</body>
</html>