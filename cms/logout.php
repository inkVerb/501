<?php

// Using SQL requires our config, which also includes session_start()
include ('./in.db.php');

// Delete the cookie in the database if one exists
if (isset($_COOKIE['user_key'])) {
  $user_key = $_COOKIE['user_key'];
  $user_key_trim = DB::trimspace($user_key); // SQL escape to make sure hackers aren't messing with cookies to inject SQL
  $pdo->key_update('strings', 'usable', 'dead', 'random_string', $user_key_trim);
  if (!$pdo->change) {
    echo '<p class="error">SQL key error!</p>';
  }
}

// Logout with Session Destroy Team Three
$_SESSION = array(); // Reset the `_SESSION` array
session_destroy(); // Destroy the session itself
setcookie(session_name(), '', 86401); // Set any _SESSION cookies to expire in Jan 1970

// Remove our "Remember me" user_key cookie
unset($_COOKIE['user_key']); // Unset the cookie so if tests don't find it later
setcookie('user_key', '', 86401); // Set our cookie value to "" (nothing) and expire in Jan 1970

// Start the session again so variables work
session_start();

// Set a session variable that will persist after this file exits
$_SESSION['just_logged_out'] = true;

// Redirect to our webapp
header("Location: webapp.php");
exit ();

?>
