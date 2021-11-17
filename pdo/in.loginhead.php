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
  $user_key_sqlesc = DB::esc($user_key); // SQL escape to make sure hackers aren't messing with cookies to inject SQL
  $query = "SELECT userid FROM strings WHERE BINARY random_string='$user_key_sqlesc' AND usable='live' AND  date_expires > '$time_now'";
  $row = $pdo->try_select($query); // try_ method for complex queries
  if ($pdo->rows == 1) {
    // Assign the values
    $user_id = "$row->userid";
  } else { // Destroy cookies, SESSION, and redirect
    $call = $pdo->key_update('strings', 'usable', 'dead', 'random_string', $user_key_sqlesc);
    if (!$pdo->ok) { // It doesn't matter if the key is there or not, just that SQL is working
      echo '<p class="error">SQL key error!</p>';
    } else {
      $_SESSION = array(); // Reset the `_SESSION` array
      session_destroy();
      setcookie(session_name(), null, 86401); // Set any _SESSION cookies to expire in Jan 1970
      unset($_COOKIE['user_key']);
      setcookie('user_key', null, 86401);
    }
    // exit and redirect in one line
    exit(header("Location: blog.php"));
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

    // Show a message
    echo "<h1>501 Blog</h1>
    <p>Hi, $fullname!</p>";
  } else {
    echo "Database error!";
    exit();
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
    $username_sqlesc = DB::esc($username);
    $password_to_check = DB::esc($password);
    $row = select('users', 'username', $username_sqlesc, 'id, fullname, pass');
    // Check to see that our SQL query returned exactly 1 row
    if ($pdo->rows == 1) {
      // Assign the values
      $user_id = "$row->id";
      $fullname = "$row->fullname";
      $hashed_password = "$row->pass";

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
            $row = $pdo->key_select('strings', 'random_string', $random_string, 'random_string');
            while ($pdo->rows >= 1) {
              $random_string = alnumString(32);
              // Check again
              $row = $pdo->key_select('strings', 'random_string', $random_string, 'random_string');
              if ($pdo->rows == 0) {
                break;
              }
            }

            // Expiration date to SQL format
            $date_expires = date("Y-m-d H:i:s", $cookie_expires_30_days_later);

            // Add the string to the database
            $call = insert('strings', 'userid, random_string, usable, date_expires', "'$user_id', '$random_string', 'live', '$date_expires'");

            // Database error or success?
            if ($pdo->change) { // If it didn't run okay
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
  echo '<p><a href="edit.php">Editor</a> | <a href="account.php">Account Settings</a> | <a href="logout.php">Logout</a></p>';
}



?>
