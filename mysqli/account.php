<?php

// Include our config (with SQL) up near the top of our PHP file
include ('./in.db.php');

// Include our functions
include ('./in.functions.php');

// Include our login cluster
$head_title = "Account Settings"; // Set a <title> name used next
$edit_page_yn = false; // Include JavaScript for TinyMCE?
include ('./in.logincheck.php');
include ('./in.head.php');

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

    // No empty password variables
    $password = (!isset($password)) ? '' : $password;
    $password2 = (!isset($password2)) ? '' : $password2;

  // Our actual settings page

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

// Footer
include ('./in.footer.php');
