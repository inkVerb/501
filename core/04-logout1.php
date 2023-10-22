<?php

// We must start the session in this file before we can destroy it
session_start();

// Logout with Session Destroy Team Three
$_SESSION = array(); // Reset the `_SESSION` array
session_destroy(); // Destroy the session itself
setcookie(session_name(), '', 86401); // Set any _SESSION cookies to expire in Jan 1970

// This message won't show because of PHP Rule #1: PHP renders HTML **after**
echo "<p>Waiting 5 seconds</p>";

// Wait 5 seconds
sleep(5);

// Redirect to our webapp
header("Location: webapp.php");
exit ();

?>
