<?php
session_start();
if(!isset($_SESSION['user_id'])){
    header("Location: login_signup.php");
    exit;
}

// Example event list (can also fetch from database)
$events = [
    ['name'=>'Music Fest 2026', 'date'=>'2026-02-21', 'price'=>1299],
    ['name'=>'DJ Night Party', 'date'=>'2026-03-05', 'price'=>999],
    ['name'=>'Food Carnival', 'date'=>'2026-04-10', 'price'=>799]
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400..900;1,400..900&family=Poppins:wght@300;400;500;600&display=swap"
      rel="stylesheet"
    />
<link rel="stylesheet" href="Reg.css">
<title>Booking Form</title>
</head>
<body>
<div class="container">
    <div class="arrow">
        <a href="landingpage.html">←</a>
    </div>
    <h1>🎉 Book your event now</h1>
    <div class="info-text">Fill out the form. All fields are required.</div>

    <form id="registrationForm" action="register.php" method="POST">
        <div class="form-group">
            <label for="event">Select Event *</label>
            <select name="event_index" id="eventSelect" required>
                <option value="">-- Select an Event --</option>
                <?php foreach($events as $i => $e): ?>
                    <option value="<?= $i ?>"><?= htmlspecialchars($e['name']) ?> | <?= $e['date'] ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label for="price">Price (Rs.)</label>
            <input type="number" id="price" name="price" readonly>
        </div>

        <div class="form-group">
            <label for="fullName">Full Name *</label>
            <input type="text" id="fullName" name="fullName" placeholder="Enter your full name" required>
        </div>

        <div class="form-group">
            <label for="email">Email Address *</label>
            <input type="email" id="email" name="email" placeholder="your.email@example.com" required>
        </div>

        <div class="form-group">
            <label for="phone">Phone Number *</label>
            <input type="tel" id="phone" name="phone" placeholder="+977XXXXXXXXXX" required>
        </div>

        <div class="form-group">
            <label for="age">Age *</label>
            <input type="number" id="age" name="age" placeholder="Enter your age" required>
        </div>

        <button type="submit" class="submit-btn" name="book">Register Now</button>
    </form>
</div>

<script>
const events = <?= json_encode($events) ?>;
const select = document.getElementById('eventSelect');
const priceInput = document.getElementById('price');

select.addEventListener('change', function() {
    const index = parseInt(this.value);
    if(!isNaN(index)){
        priceInput.value = events[index].price;
    } else {
        priceInput.value = '';
    }
});
</script>
</body>
</html>