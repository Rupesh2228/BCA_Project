<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400..900;1,400..900&family=Poppins:wght@300;400;500;600&display=swap"
      rel="stylesheet"
    />
  <meta charset="UTF-8">
  <title>Login & Signup Form</title>
  <style>
    body {
      margin:0;
      padding:0;
      display:flex;
      justify-content:center;
      align-items:center;
      min-height:100vh;
      font-family:'Jost',sans-serif;
      background:linear-gradient(to bottom,#0f0c29,#302b63,#24243e);
    }

    .main {
      width:350px;
      background:#24243e;
      border-radius:10px;
      box-shadow:5px 20px 50px #000;
      overflow:hidden;
      text-align:center;
      position:relative;
    }

    .arrow {
      position:absolute;
      top:10px;
      left:10px;
      font-size:1.2em;
      z-index:3;
    }

    .arrow a {
      text-decoration:none;
      color:#fff;
      font-weight:bold;
    }

    .tabs {
      display:flex;
      justify-content:space-around;
      background:#302b63;
      margin-top:40px; /* space for arrow */
    }

    .tabs button {
      flex:1;
      padding:15px 0;
      font-size:1.2em;
      font-weight:bold;
      color:#fff;
      background:#302b63;
      border:none;
      cursor:pointer;
      transition:0.3s;
    }

    .tabs button.active {
      background:#573b8a;
    }

    .form-container {
      padding:30px 0;
      background:#24243e;
    }

    .form-container form {
      display:none;
      flex-direction:column;
      align-items:center;
    }

    .form-container form.active {
      display:flex;
    }

    input {
      width:70%;
      background:#e0dede;
      margin:10px 0;
      padding:12px;
      border:none;
      outline:none;
      border-radius:5px;
    }

    button.submit-btn {
      width:70%;
      height:40px;
      margin-top:20px;
      color:#fff;
      background:#573b8a;
      font-size:1em;
      font-weight:bold;
      border:none;
      border-radius:5px;
      cursor:pointer;
      transition:.2s ease-in;
    }

    button.submit-btn:hover {
      background:#6d44b8;
    }

  </style>
</head>
<body>
  <div class="main">
    <!-- Back Arrow -->
    <div class="arrow">
      <a href="landingpage.html">←</a>
    </div>

    <!-- Tabs -->
    <div class="tabs">
      <button id="loginTab" class="active">Login</button>
      <button id="signupTab">Sign Up</button>
    </div>

    <div class="form-container">
      <!-- Login Form -->
      <form id="loginForm" class="active" method="post" action="login.php">
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="pswd" placeholder="Password" required>
        <button type="submit" class="submit-btn">Login</button>
      </form>

      <!-- Signup Form -->
      <form id="signupForm" method="post" action="signup.php">
        <input type="text" name="name" placeholder="User name" required>
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="pswd" placeholder="Password" required>
        <button type="submit" class="submit-btn">Sign Up</button>
      </form>
    </div>
  </div>

  <script>
    const loginTab = document.getElementById('loginTab');
    const signupTab = document.getElementById('signupTab');
    const loginForm = document.getElementById('loginForm');
    const signupForm = document.getElementById('signupForm');

    loginTab.addEventListener('click', () => {
      loginTab.classList.add('active');
      signupTab.classList.remove('active');
      loginForm.classList.add('active');
      signupForm.classList.remove('active');
    });

    signupTab.addEventListener('click', () => {
      signupTab.classList.add('active');
      loginTab.classList.remove('active');
      signupForm.classList.add('active');
      loginForm.classList.remove('active');
    });
  </script>

  <!-- PHP Status Alert -->
  <?php if (!empty($_GET['status'])): ?>
  <script>
    let message = "";
    let redirectUrl = "landingpage.html"; // redirect after OK

    switch ("<?= $_GET['status'] ?>") {
      case "signup_success":
        message = "🎉 Signup successful! Click OK to continue.";
        break;
      case "login_success":
        message = "✅ Login successful! Click OK to continue.";
        break;
      case "wrong_password":
        message = "❌ Invalid password. Please try again.";
        break;
      case "no_user":
        message = "❌ No user found with that email.";
        break;
      case "exists":
        message = "⚠️ Email already registered. Please log in.";
        break;
      case "error":
        message = "⚠️ Something went wrong. Please try again.";
        break;
    }

    if (message !== "") {
      alert(message);
      window.location.href = redirectUrl;
    }
  </script>
  <?php endif; ?>
</body>
</html>