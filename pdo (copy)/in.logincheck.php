<?php

// See if we have a cookie
if (isset($_COOKIE['user_key'])) {
  // Assign the current time
  $time_now = date("Y-m-d H:i:s");

  // Get the user ID from the key strings table
  $user_key = $_COOKIE['user_key'];
  $user_key_sqlesc = DB::esc($user_key); // SQL escape to make sure hackers aren't messing with cookies to inject SQL
  $row = $pdo->key_select('strings', 'random_string', $user_key_sqlesc, 'userid');
  if ($pdo->rows == 1) {
    // Assign the values
    $user_id = "$row->userid";
  } else { // Destroy cookies, SESSION, and redirect
    $pdo->key_update('strings', 'usable', 'dead', 'random_string', $user_key_sqlesc);
    if (!$pdo->change) { // It doesn't matter if the key is there or not, just that SQL is working
      echo '<p class="error">SQL key error!</p>';
    } else {
      $_SESSION = array(); // Reset the `_SESSION` array
      session_destroy();
      setcookie(session_name(), null, 86401); // Set any _SESSION cookies to expire in Jan 1970
      unset($_COOKIE['user_key']);
      setcookie('user_key', null, 86401);
    }
    if ( (!isset($nologin_allowed)) || ($nologin_allowed != true) ) {
      // exit and redirect in one line
      exit (header("Location: blog.php"));
    }
  }

  // Get the user's info from the users table
  $row = $pdo->select('users', 'id', $user_id, 'fullname');
  // Check to see that our SQL query returned exactly 1 row
  if ($pdo->rows == 1) {
    // Assign the values
    $fullname = "$row->fullname";

    // Set the $_SESSION array
    $_SESSION['user_id'] = $user_id;
    $_SESSION['full_name'] = $fullname;

  } else {
    echo '<p class="error">SQL error!</p>';
    exit ();
  }

// See if we are logged in by now
} elseif ( (isset($_SESSION['user_id'])) && (isset($_SESSION['full_name'])) ) {

  // Set our variables
  $user_id = $_SESSION['user_id'];
  $fullname = $_SESSION['full_name'];

} elseif ( (!isset($nologin_allowed)) || ($nologin_allowed != true) ) {
  exit (header("Location: blog.php"));
}

?>
