<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    $_SESSION['user_id'] = 1;  // temporary test
    echo "Session set to 1";
} else {
    echo "Session exists: " . $_SESSION['user_id'];
}
?>