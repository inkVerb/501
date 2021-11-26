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
    $fullname_trim = DB::trimspace($fullname);
    $username_trim = DB::trimspace($username);
    $email_trim = DB::trimspace($email);
    $favnumber_trim = DB::trimspace($favnumber);

    // Prepare the query
    if (isset($password)) { // Changing password?
      $query = $database->prepare("UPDATE users SET fullname=:fullname, username=:username, email=:email, favnumber=:favnumber, pass=:pass WHERE id=:id");
      $query->bindParam(':id', $user_id);
      $query->bindParam(':fullname', $fullname_trim);
      $query->bindParam(':username', $username_trim);
      $query->bindParam(':email', $email_trim);
      $query->bindParam(':favnumber', $favnumber_trim);
      $query->bindParam(':pass', $password_hashed);
  		// inline password hash: $query = "INSERT INTO users (name, username, email, pass) VALUES ('$fullname', '$username', '$email', '"  .  password_hash($password, PASSWORD_BCRYPT) .  "')";
    } else { // Not changing password
      $query = $database->prepare("UPDATE users SET fullname=:fullname, username=:username, email=:email, favnumber=:favnumber WHERE id=:id");
      $query->bindParam(':id', $user_id);
      $query->bindParam(':fullname', $fullname_trim);
      $query->bindParam(':username', $username_trim);
      $query->bindParam(':email', $email_trim);
      $query->bindParam(':favnumber', $favnumber_trim);
    }
    // Run the query
    $pdo->exec_($query);
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
$rows = $pdo->select('users', 'id', $user_id, 'fullname, username, email, favnumber');
// Test the query
if ($pdo->numrows == 1) {
  foreach ($rows as $row) {
  	$fullname = "$row->fullname";
  	$username = "$row->username";
    $email = "$row->email";
    $favnumber = "$row->favnumber";
  }
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
