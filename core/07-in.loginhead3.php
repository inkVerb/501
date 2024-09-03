<?php

// Just logged out?
if ((isset($_SESSION['just_logged_out'])) && ($_SESSION['just_logged_out'] == true)) {
  echo '<p class="blue">Logged out!</p>';
  // We don't want to see this again
  unset($_SESSION['just_logged_out']);

}

// See if we have a cookie
if (isset($_COOKIE['user_key'])) {
  // Assign the current time
  $time_now = date("Y-m-d H:i:s");

  // Get the user ID from the key strings table
  $user_key = $_COOKIE['user_key'];
  $user_key_sqlesc = escape_sql($user_key); // SQL escape to make sure hackers aren't messing with cookies to inject SQL
  $query = "SELECT userid FROM strings WHERE BINARY random_string='$user_key_sqlesc' AND usable='cookie_login' AND  date_expires > '$time_now'";
  $call = mysqli_query($database, $query);
  if (mysqli_num_rows($call) == 1) {
    // Assign the values
    $row = mysqli_fetch_array($call, MYSQLI_NUM);
      $user_id = "$row[0]";
  } else { // Destroy cookies, SESSION, and redirect
    $query = "UPDATE strings SET usable='dead' WHERE BINARY random_string='$user_key_sqlesc'";
    $call = mysqli_query($database, $query);
    if (!$call) { // It doesn't matter if the key is there or not, just that SQL is working
      echo '<p class="error">SQL key error!</p>';
    } else {
      $_SESSION = array(); // Reset the `_SESSION` array
      session_destroy();
      setcookie(session_name(), '', 86401); // Set any _SESSION cookies to expire in Jan 1970
      unset($_COOKIE['user_key']);
      setcookie('user_key', '', 86401);
    }
    // exit and redirect in one line
    exit (header("Location: webapp.php"));
  }


  // Get the user's info from the users table
  $query = "SELECT fullname FROM users WHERE id='$user_id'";
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
      echo "<h1>501 Blog</h1>
      <p>Hi, $fullname!</p>";
    } else {
      echo "Database error!";
      exit ();
    }


// See if we are already logged in
} elseif ((isset($_SESSION['user_id'])) && (isset($_SESSION['full_name']))) {
  $user_id = $_SESSION['user_id'];
  $fullname = $_SESSION['full_name'];

  // Show a message
  echo "<h1>501 Blog</h1>
  <p>Hi, $fullname!</p>";


// Login POST attempt?
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {

  // Include our POST checks
  include ('./in.checks.php');
  if (($no_form_errors == true) && (!empty($password)) && (!empty($username))) {
    $checks_out = true;

    // if SELECT: Query user info from the database if everything checks out
    $username_sqlesc = escape_sql($username);
    $password_to_check = $password;
    $query = "SELECT id, fullname, pass FROM users WHERE username='$username_sqlesc'";
    $call = mysqli_query($database, $query);
    // Check to see that our SQL query returned exactly 1 row
    if (mysqli_num_rows($call) == 1) {
      // Assign the values
      $row = mysqli_fetch_array($call, MYSQLI_NUM);
        $user_id = "$row[0]";
        $fullname = "$row[1]";
        $hashed_password = "$row[2]";

      // Test our password against the hash
      if (password_verify($password_to_check, $hashed_password)) {

        // Set the $_SESSION array
        $_SESSION['user_id'] = $user_id;
        $_SESSION['full_name'] = $fullname;

        // Remember me for $_COOKIE['user_id'] ?
        if (isset($_POST['rememberme'])) {
          // Calculate the expiration date
          $cookie_expires_30_days_later = time() + (30 * 24 * 60 * 60); // epoch 30 days from now

          // Create a key for the cookie value
            // Include our string functions
            include ('./in.string_functions.php');

            // Create the key
            $random_string = alnumString(255);

            // Check to see if the string already exists in the database
            $query = "SELECT random_string FROM strings WHERE BINARY random_string='$random_string'"; // "BINARY" makes sure case and characters are exact
            $call = mysqli_query($database, $query);
            while (mysqli_num_rows($call) !=0) {
              $random_string = alnumString(32);
              // Check again
              $query = "SELECT random_string FROM strings WHERE BINARY random_string='$random_string'"; // "BINARY" makes sure case and characters are exact
              $call = mysqli_query($database, $query);
              if (mysqli_num_rows($call) == 0) {
                break;
              }
            }

            // Expiration date to SQL format
            $date_expires = date("Y-m-d H:i:s", $cookie_expires_30_days_later);

            // Add the string to the database
            $query = "INSERT INTO strings (userid, random_string, usable, date_expires) VALUES ('$user_id', '$random_string', 'cookie_login', '$date_expires')";
            $call = mysqli_query($database, $query);

            // Database error or success?
            if (mysqli_affected_rows($database) != 1) { // If it didn't run okay
              echo "There was a database error!";
            } else {
              // Set the cookie $_COOKIE['user_key']
              setcookie("user_key", $random_string, $cookie_expires);

            }
        }

        // Show a message
        echo "<h1>501 Blog login success!</h1>
        <p>$fullname, you are logged in.</p>";

      } else { // Password fail
        echo '<p class="error">Login error!</p>';
      }

    } else { // Username fail
      echo '<p class="error">Login error!</p>';
    } // End database check

  // If errors in form
  } else {
      echo '<p class="error">Errors! Try again.</p>';
  }


// Not logged in, no login POST attempt
} else {

// Our form
echo '<h1>Login</h1>
<form action="webapp.php" method="post">';

echo 'Username: '.formInput('username', $username, $check_err).'<br><br>';
echo 'Password: '.formInput('password', $password, $check_err).'<br><br>';

// Checkbox to set $_COOKIE['user_id']
// Tip: <label for="CHECKBOX_ID"> wrapped around the checkbox <input> makes the label clickable
echo '<label for="rememberme"><input type="checkbox" id="rememberme" name="rememberme" /> Remember me (use cookies to stay logged in 30 days)</label>';

echo '
  <input type="submit" value="Submit Button">
</form>
';

// Recover login
echo '<p>Forget your password and need to <a href="recover.php">recover your login</a>?</p>';

}

// Account settings link
if (isset($user_id)) {
  echo '<p><a href="account.php">Account Settings</a> | <a href="logout.php">Logout</a></p>';
}



?>
