<?php

// See if we have a cookie
if (isset($_COOKIE['user_key'])) {
  // Get the user ID from the key strings table
  $user_key = $_COOKIE['user_key'];
  $user_key_sqlesc = escape_sql($user_key);
  $query = "SELECT userid FROM strings WHERE random_string='$user_key_sqlesc'";
  $call = mysqli_query($database, $query);
  if (mysqli_num_rows($call) == 1) {
    // Assign the values
    $row = mysqli_fetch_array($call, MYSQLI_NUM);
      $user_id = "$row[0]";
  } else { // Destroy cookies, SESSION, and redirect
    $_SESSION = array(); // Reset the `_SESSION` array
    session_destroy();
    setcookie(session_name(), null, 86401); // Set any _SESSION cookies to expire in Jan 1970
    unset($_COOKIE['user_key']);
    setcookie('user_key', null, 86401);

    // exit and redirect in one line
    exit(header("Location: webapp.php"));
  }

  // Get the user's info from the users table
  $query = "SELECT fullname FROM users WHERE id='$username_sqlesc'";
  $call = mysqli_query($database, $query);
  // Check to see that our SQL query returned exactly 1 row
  if (mysqli_num_rows($call) == 1) {
    // Assign the values
    $row = mysqli_fetch_array($call, MYSQLI_NUM);
      $fullname = "$row[0]";

      // Set the $_SESSION array
      $_SESSION['user_id'] = $user_id;
      $_SESSION['user_name'] = $fullname;

    } else {
      echo "Database error!";
      exit();
    }


// See if we are already logged in
} elseif ((isset($_SESSION['user_id'])) && (isset($_SESSION['user_name']))) {

  // Set our variables
  $user_id = $_SESSION['user_id'];
  $fullname = $_SESSION['user_name'];

// Not logged in
} else {
  // exit and redirect in one line
  exit(header("Location: webapp.php"));
}

// Echo our header links

// End our PHP so the following HTML simply appears without echo
?>
<!DOCTYPE html>
<html>
<head>
  <!-- CSS file included as <link> -->
  <link href="style.css" rel="stylesheet" type="text/css" />

  <!-- One line of PHP with our <title> -->
  <title><?php echo $head_title; ?></title>

</head>
<body>

<?php // Restart php

echo '<p>Hello '.$fullname.'! <a href="account.php">Account Settings</a> | <a href="logout.php">Logout</a></p>';


?>
