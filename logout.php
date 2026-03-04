<?php
session_start();

// Destroy all session data
session_unset();
session_destroy();

// Redirect to login or landing page
header("Location: landingpage.html"); // change this to your login page
exit;
?>