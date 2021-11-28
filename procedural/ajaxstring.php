<?php
//if ((isset($_POST['go'])) && (isset($_POST['time']))) {
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $userID = $_POST['rsuid'];

  // Include our config (with SQL) up near the top of our PHP file
  include ('./in.db.php');

  // Include our string functions
  include ('./in.string_functions.php');

  // Create our string
  $random_string = alnumString(32);

  // Check to see if the string already exists in the database
  $query = "SELECT random_string FROM strings WHERE BINARY random_string='$random_string'"; // "BINARY" makes sure case and characters are exact
  $call = mysqli_query($database, $query);
  while (mysqli_num_rows($call) != 0) {
    $random_string = alnumString(32);
    // Check again
    $query = "SELECT random_string FROM strings WHERE BINARY random_string='$random_string'"; // "BINARY" makes sure case and characters are exact
    $call = mysqli_query($database, $query);
    if (mysqli_num_rows($call) == 0) {
      break;
    }
  }

  // Get the time 30 seconds from now
  $date_expires = date("Y-m-d H:i:s", time() + 20);

  // Add the string to the database
  $userID_sqlesc = escape_sql($userID); // SQL espace just in case, even though it is not user input
  $query = "INSERT INTO strings (userid, random_string, date_expires) VALUES ('$userID_sqlesc', '$random_string', '$date_expires')";
  $call = mysqli_query($database, $query);

  // Database error or success?
  if (mysqli_affected_rows($database) != 1) { // If it didn't run okay
    echo "There was a database error!<br>$query";

  } else { // It ran okay

    echo '<p>Normally, this would be emailed to the address in your account...</p>';
    echo '<p><b>Click here to login:</b> <a href="recover_login.php?s='.$random_string.'">localhost/web/recover_login.php?s='.$random_string.'</a><br><i>(This link expires in 20 seconds)</i></p><br>';
  }

  // Show a teaching tip

  // Uppercase string
  $uppercase_random_string = strtoupper($random_string); // Rember from Lesson 1

  // The SQL query
  echo "
  <p><b>. . .Teaching tip. . .</b></p>
  <p>
  Run this SQL query: (string in all uppercase)<br>
  <code>SELECT * FROM strings WHERE random_string='$uppercase_random_string';</code><br><br>
  Then run this: (string in all uppercase)<br>
  <code>SELECT * FROM strings WHERE BINARY random_string='$uppercase_random_string';</code><br><br>
  Then run this: (string in normal, original case)<br>
  <code>SELECT * FROM strings WHERE BINARY random_string='$random_string';</code><br><br>
  This is why we use \"BINARY\" in the SQL call
  </p>
  ";



}
?>
