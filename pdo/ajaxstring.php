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
  $row = $pdo->key_select('strings', 'random_string', $random_string, 'random_string'); // "BINARY" makes sure case and characters are exact
  while ($pdo->numrows != 0) {
    $random_string = alnumString(32);
    // Check again
    $row = $pdo->key_select('strings', 'random_string', $random_string, 'random_string'); // "BINARY" makes sure case and characters are exact
    if ($pdo->numrows == 0) {
      break;
    }
  }

  // Trim userID
  $userID_trim = DB::trimspace($userID); // SQL espace just in case, even though it is not user input

  // This won't work in PDO because the ->insert() method won't allow SQL functions like NOW()
  // This must use a prepared-executed statement rather than
  // // Get the time 30 seconds from now
  // $date_expires = date("Y-m-d H:i:s", time() + 20);

  // // Add the string to the database
  // $call = $pdo->insert('strings', 'userid, random_string, date_expires', "'$userID_trim', '$random_string', '$date_expires'");


  $query = $database->prepare("INSERT INTO strings (userid, random_string, date_expires) VALUES ('$userID_trim', '$random_string', NOW() + INTERVAL '20' SECOND");

  // Try the query
  try {
    $query->execute();

    // Success messages
    echo '<p>Normally, this would be emailed to the address in your account...</p>';
    echo '<p><b>Click here to login:</b> <a href="recover_login.php?s='.$random_string.'">localhost/web/recover_login.php?s='.$random_string.'</a><br><i>(This link expires in 20 seconds)</i></p><br>';
    
  } catch (PDOException $error) {
    echo "There was a database error!<br>$query<br>" . $error->getMessage();
  }

  // Database error or success?


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
