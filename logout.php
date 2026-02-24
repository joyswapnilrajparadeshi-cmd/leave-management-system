<?php
session_start();
session_unset();  // Clear all session variables
session_destroy();  // Destroy the session

// Redirect to home page
header("Location: index.php");
exit();
?>
