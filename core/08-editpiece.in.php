<?php

if (($_SERVER['REQUEST_METHOD'] === 'POST') && (isset($_POST['piece']))) {

  // POST checks
    // Only sanitize, no errors

  // Empty first
  if ((!isset($_POST['p_title'])) || ($_POST['p_title'] == '')) {

    // Only process a Date Live if Publishing or it is set
    if (($_POST['p_submit'] == 'Publish') || (!empty($_POST['p_live']))) {
      $p_live = checkPiece('p_live',$_POST['p_live']);
    }

    // Title now because we will use it in the Slug
    $p_title = checkPiece('p_title',$_POST['p_title']);

    // Apply Title to Slug if empty
    if (empty($_POST['p_slug'])) {
      $p_slug = checkPiece('p_slug',$p_title);
    } else {
      $p_slug = checkPiece('p_slug',$_POST['p_slug']);
    }

    // All other fields
    $p_type = checkPiece('p_type',$_POST['p_type']);
    $p_status = checkPiece('p_status',$_POST['p_status']);
    $p_content = checkPiece('p_content',$_POST['p_content']);
    $p_after = checkPiece('p_after',$_POST['p_after']);





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
		// inline password hash: $query = "INSERT INTO users (name, username, email, pass, type) VALUES ('$fullname', '$username', '$email', '"  .  password_hash($password, PASSWORD_BCRYPT) .  "', 'admin')";
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


} // Finish POST if
