<!DOCTYPE html>
<html>
<head>
  <!-- CSS file included as <link> -->
  <link href="style.css" rel="stylesheet" type="text/css" />
</head>
<body>

<?php

// Include our config (with SQL) up near the top of our PHP file
include ('./in.config.php');

// Include our functions
include ('./in.functions.php');

// We must be logged in!
// See if we have a cookie
if (isset($_COOKIE['user_id'])) {
  $user_id = $_COOKIE['user_id'];
  $user_id_sqlesc = escape_sql($user_id);
  $query = "SELECT fullname FROM users WHERE id='$user_id_sqlesc'";
  $call = mysqli_query($database, $query);
  // Check to see that our SQL query returned exactly 1 row
  if (mysqli_num_rows($call) == 1) {
    // Assign the values
    $row = mysqli_fetch_array($call, MYSQLI_NUM);
      $fullname = "$row[0]";

      // Set the $_SESSION array
      $_SESSION['user_id'] = $user_id;
      $_SESSION['full_name'] = $fullname;

      // Show a message
      echo "<h1>Cookie</h1>
      <p>$fullname, you are already logged in from a cookie!</p>";

    } else { // Back-up plan just in case the impossible happens
      echo '<p class="error">Serious error.</p>';

      // We must finish the HTML page before we exit
      echo '
      </body>
      </html>';

      // We could just redirect to the main page instead
      //header("Location: webapp.php");
      exit();
    }


// See if we are already logged in
} elseif ((isset($_SESSION['user_id'])) && (isset($_SESSION['full_name']))) {
  $user_id = $_SESSION['user_id'];
  $fullname = $_SESSION['full_name'];

  // Show a message
  echo "<h1>Logged In</h1>
  <p>$fullname, you are logged in and ready to do stuff!</p>";

} else {
  // Show a message
  echo "<h1>Not logged in</h1>
  <p>You are not logged in!</p>";

  // We must finish the HTML page before we exit
  echo '
  </body>
  </html>';

  // We could just redirect to the main page instead
  //header("Location: webapp.php");
  exit();
}



// POSTed form?
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

  // Include our POST checks
  include ('./in.checks.php');

  // No errors, all ready
  if (($no_form_errors == true)) {

    // Update the user

    // Prepare our database values for entry
    $password_hashed = password_hash($password, PASSWORD_BCRYPT);
    $fullname_sqlesc = escape_sql($fullname);
    $username_sqlesc = escape_sql($username);
    $email_sqlesc = escape_sql($email);
    $favnumber_sqlesc = escape_sql($favnumber);

"UPDATE ads SET epoch_wk_reset='$resetEpoch', week_view_count=0, week_cat_count=0, week_tag_count=0, week_search_count=0, WHERE ad_id='$ad_id'";

    // Prepare the query
    if (isset($password)) { // Changing password?
      $query = "UPDATE users SET fullname='$fullname_sqlesc', username='$username_sqlesc', email='$email_sqlesc', favnumber='$favnumber_sqlesc', pass='$password_hashed' WHERE id='$user_id'";
  		// inline password hash: $query = "INSERT INTO users (name, username, email, pass) VALUES ('$fullname', '$username', '$email', '"  .  password_hash($password, PASSWORD_BCRYPT) .  "')";
    } else { // Not changing password
      $query = "UPDATE users SET fullname='$fullname_sqlesc', username='$username_sqlesc', email='$email_sqlesc', favnumber='$favnumber_sqlesc' WHERE id='$user_id'";
    }
    // Run the query
    $call = mysqli_query($database, $query);
    // Test the query
    if ($call) {
      // Change
      if (mysqli_affected_rows($database) == 1) {
        echo '<p class="green">Updated!</p>';
      // No change
      } elseif (mysqli_affected_rows($database) == 0) {
        echo '<p class="orange">No change.</p>';
      }
    } else {
      echo '<p class="error">Serious error.</p>';
    }

  } else {
    echo '<p class="error">Errors, try again.</p>';
  }

} // Finish POST if


// Retrieve the user info from the database
$query = "SELECT fullname, username, email, favnumber FROM users WHERE id='$user_id'";
// Run the query
$call = mysqli_query($database, $query);
// Test the query
if (mysqli_num_rows($call) == 1) {
  $row = mysqli_fetch_array($call, MYSQLI_NUM);
		$fullname = "$row[0]";
		$username = "$row[1]";
    $email = "$row[2]";
    $favnumber = "$row[3]";


  // Our actual settings page

  echo '<h1>Account Settings</h1>';

  // Logout & webapp links
  echo '<p><a href="webapp.php">Back to webapp</a> | <a href="logout.php">Logout</a></p>';

  // Settings form
  echo '
  <form action="account.php" method="post">';

  echo 'Name: '.formInput('fullname', $fullname, $check_err).'<br><br>';
  echo 'Username: '.formInput('username', $username, $check_err).'<br><br>';
  echo 'Email: '.formInput('email', $email, $check_err).'<br><br>';
  echo 'Favorite number: '.formInput('favnumber', $favnumber, $check_err).' (1-100 security question)<br><br>';
  echo 'Password: '.formInput('password', $password, $check_err).'<br><br>';
  echo 'Confirm password: '.formInput('password2', $password2, $check_err).'<br><br>';

  echo '
    <input type="submit" value="Save changes">
  </form>
  ';

} else {
  echo '<p class="errors">No account detected!</p>';
}

?>

</body>
</html>
