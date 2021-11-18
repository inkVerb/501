<?php

// Include our config (with SQL) up near the top of our PHP file
include ('./in.config.php');

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
  if ($no_form_errors == true) {

    // Update the user

    // Prepare our database values for entry
    $password_hashed = password_hash($password, PASSWORD_BCRYPT);
    $fullname_sqlesc = DB::esc($fullname);
    $username_sqlesc = DB::esc($username);
    $email_sqlesc = DB::esc($email);
    $favnumber_sqlesc = DB::esc($favnumber);

    // Prepare the query
    if (isset($password)) { // Changing password?
      $query = "UPDATE users SET fullname='$fullname_sqlesc', username='$username_sqlesc', email='$email_sqlesc', favnumber='$favnumber_sqlesc', pass='$password_hashed' WHERE id='$user_id'";
  		// inline password hash: $query = "INSERT INTO users (name, username, email, pass) VALUES ('$fullname', '$username', '$email', '"  .  password_hash($password, PASSWORD_BCRYPT) .  "')";
    } else { // Not changing password
      $query = "UPDATE users SET fullname='$fullname_sqlesc', username='$username_sqlesc', email='$email_sqlesc', favnumber='$favnumber_sqlesc' WHERE id='$user_id'";
    }
    // Run the query
    $pdo->try_update($query);
    // Test the query
    if ($pdo->ok) {
      // Change
      if ($pdo->change) {
        echo '<p class="green">Updated! Some changes may take a moment.</p>';
      // No change
    } elseif (!$pdo->change) {
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
$row = $pdo->select('users', 'id', $user_id, 'fullname, username, email, favnumber');
// Test the query
if ($pdo->numrows == 1) {
	$fullname = "$row->fullname";
	$username = "$row->username";
  $email = "$row->email";
  $favnumber = "$row->favnumber";

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
