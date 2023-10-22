<?php

// We must start the session in this file before we can destroy it
session_start();

// Logout with Session Destroy Team Three
$_SESSION = array(); // Reset the `_SESSION` array
session_destroy(); // Destroy the session itself
setcookie(session_name(), '', 86401); // Set any _SESSION cookies to expire in Jan 1970

// Start the session again so variables work
session_start();

// Set a session variable that will persist after this file exits
$_SESSION['just_logged_out'] = true;

// Redirect to our webapp
header("Location: webapp.php");
exit ();

?>
