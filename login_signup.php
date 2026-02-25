<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Modern Login & Signup Form</title>
  <style>
    body {margin:0; padding:0; display:flex; justify-content:center; align-items:center; min-height:100vh; font-family:'Jost',sans-serif; background:linear-gradient(to bottom,#0f0c29,#302b63,#24243e);}
    .main {width:350px; height:500px; background:#24243e; border-radius:10px; box-shadow:5px 20px 50px #000; overflow:hidden; position:relative; text-align:center;}
    #chk { display:none; }
    .signup, .login { position:relative; width:100%; height:100%; }
    label { color:#fff; font-size:2.3em; justify-content:center; display:flex; margin:50px; font-weight:bold; cursor:pointer; transition:.5s ease-in-out; }
    input { width:60%; background:#e0dede; display:flex; margin:20px auto; padding:12px; border:none; outline:none; border-radius:5px; }
    button { width:60%; height:40px; margin:10px auto; display:block; color:#fff; background:#573b8a; font-size:1em; font-weight:bold; margin-top:30px; border:none; border-radius:5px; cursor:pointer; transition:.2s ease-in; }
    button:hover { background:#6d44b8; }
    .login { height:460px; background:#eee; border-radius:60%/10%; transform:translateY(-180px); transition:.8s ease-in-out; }
    .login label { color:#573b8a; transform:scale(.6); }
    #chk:checked ~ .login { transform:translateY(-500px); }
    #chk:checked ~ .login label { transform:scale(1); }
    #chk:checked ~ .signup label { transform:scale(.6); }
    .arrow { padding-right: 280px; margin-bottom:-40px; }
    .arrow a { text-decoration:none; color:#fff; font-weight:bold; }
  </style>
</head>
<body>
  <div class="main">
    <input type="checkbox" id="chk" aria-hidden="true">

    <div class="arrow">
      <a href="#">← Back</a>
    </div>

    <!-- Signup Form -->
    <div class="signup">
      <form method="post" action="signup.php">
        <label for="chk" aria-hidden="true">Sign up</label>
        <input type="text" name="txt" placeholder="User name" required>
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="pswd" placeholder="Password" required>
        <button type="submit">Sign up</button>
      </form>
    </div>

    <!-- Login Form -->
    <div class="login">
      <form method="post" action="login.php">
        <label for="chk" aria-hidden="true">Login</label>
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="pswd" placeholder="Password" required>
        <button type="submit">Login</button>
      </form>
    </div>
  </div>

  <?php if (!empty($_GET['status'])): ?>
    <script>
      <?php if ($_GET['status'] === 'signup_success'): ?>
        alert("Signup successful! Redirecting to dashboard...");
        window.location.href = "dashboard.php";
      <?php elseif ($_GET['status'] === 'login_success'): ?>
        alert("Login successful! Redirecting to dashboard...");
        window.location.href = "dashboard.php";
      <?php elseif ($_GET['status'] === 'wrong_password'): ?>
        alert("Invalid password. Please try again.");
      <?php elseif ($_GET['status'] === 'no_user'): ?>
        alert("No user found with that email.");
      <?php elseif ($_GET['status'] === 'exists'): ?>
        alert("Email already registered. Please log in.");
      <?php elseif ($_GET['status'] === 'error'): ?>
        alert("Something went wrong. Please try again.");
      <?php endif; ?>
    </script>
  <?php endif; ?>
</body>
</html>