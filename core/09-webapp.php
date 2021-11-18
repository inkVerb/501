<?php

// Include our config (with SQL) up near the top of our PHP file
include ('./in.config.php');

// Include our functions
include ('./in.functions.php');

// Include our login cluster
$head_title = "Webapp Dashboard"; // Set a <title> name used next
$nologin_allowed = true; // Login required?
include ('./in.logincheck.php');
include ('./in.head.php');

// Just logged out?
if ((isset($_SESSION['just_logged_out'])) && ($_SESSION['just_logged_out'] == true)) {
  echo '<p class="blue">Logged out!</p>';
  // We don't want to see this again
  unset($_SESSION['just_logged_out']);

// Just logged in?
} elseif ((isset($_SESSION['just_logged_in'])) && ($_SESSION['just_logged_in'] == true)) {
  echo '<p class="blue">Logged in!</p>';
  // We don't want to see this again
  unset($_SESSION['just_logged_in']);

}

// Login POST attempt?
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

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

          // Create a key for the cookie value
            // Include our string functions
            include ('./in.string_functions.php');

            // Create the key
            $random_string = alnumString(255);

            // Check to see if the string already exists in the database
            $query = "SELECT random_string FROM strings WHERE BINARY random_string='$random_string'"; // "BINARY" makes sure case and characters are exact
            $call = mysqli_query($database, $query);
            while (mysqli_num_rows($call) == 1) {
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

        // SESSION note, then reload this page so the logincheck processes
        $_SESSION['just_logged_in'] = true;
        exit (header("Location: blog.php"));

      } else { // Password fail
        echo '<h1>501 Blog login error!</h1>
        <p class="error">Login error!</p>';
      }

    } else { // Username fail
      echo '<h1>501 Blog login error!</h1>
      <p class="error">Login error!</p>';
    } // End database check

  // If errors in form
  } else {
      echo '
      <h1>501 Blog login error!</h1>
      <p class="error">Errors! Try again.</p>';
  }

}



// Not logged in, no login POST attempt
if (!isset($user_id)) {

  // Our form
  echo '<h1>Login</h1>
  <form action="webapp.php" method="post">';

  echo 'Username: '.formInput('username', $username, $check_err).'<br><br>';
  echo 'Password: '.formInput('password', $password, $check_err).'<br><br>';

  // Checkbox to set $_COOKIE['user_id']
  // Tip: <label for="CHECKBOX_ID"> makes the label clickable
  echo '<label for="rememberme"><input type="checkbox" id="rememberme" name="rememberme" /> Remember me (use cookies to stay logged in 30 days)</label>';

  echo '
    <input type="submit" value="Login">
  </form>
  ';

  // Recover login
  echo '<p>Forget your password and need to <a href="recover.php">recover your login</a>?</p>';

}

// Footer
include ('./in.footer.php');
