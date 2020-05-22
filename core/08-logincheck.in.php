<?php

// See if we have a cookie
if (isset($_COOKIE['username'])) {
  $username = $_COOKIE['username'];
  $username_sqlesc = escape_sql($username);
  $query = "SELECT id, fullname FROM users WHERE username='$username_sqlesc'";
  $call = mysqli_query($database, $query);
  // Check to see that our SQL query returned exactly 1 row
  if (mysqli_num_rows($call) == 1) {
    // Assign the values
    $row = mysqli_fetch_array($call, MYSQLI_NUM);
      $user_id = "$row[0]";
      $fullname = "$row[1]";

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
