<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {

  $name = $_POST['fullName'];
  $email = $_POST['email'];
  $eventType = $_POST['eventType'];

  echo "Name: " . $name . "<br>";
  echo "Email: " . $email . "<br>";
  echo "Event Type: " . $eventType . "<br>";

}
?>
