<?php

// Just logged out?
if ((isset($_SESSION['just_logged_out'])) && ($_SESSION['just_logged_out'] == true)) {
  echo '<p class="blue">Logged out!</p>';
  // We don't want to see this again
  unset($_SESSION['just_logged_out']);

}

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
    } else {
      echo "Database error!";
      exit ();
    }


// See if we are already logged in
} elseif ((isset($_SESSION['user_id'])) && (isset($_SESSION['full_name']))) {
  $user_id = $_SESSION['user_id'];
  $fullname = $_SESSION['full_name'];

  // Show a message
  echo "<h1>Logged In</h1>
  <p>$fullname, you are logged in and ready to do stuff!</p>";


// Login POST attempt?
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {

  // Include our POST checks
  include ('./in.checks.php');
  if (($no_form_errors == true) && (!empty($password)) && (!empty($username))) {
    $checks_out = true;

    // if SELECT: Query user info from the database if everything checks out
    $username_sqlesc = escape_sql($username);
    $password_to_check = escape_sql($password);
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

          // Set the cookie $_COOKIE['user_id'] // WRONG WAY, just an example
          setcookie("user_id", $user_id, $cookie_expires); // Never set user_id, username, or password as the cookie value!
        }

        // Show a message
        echo "<h1>Login success!</h1>
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
