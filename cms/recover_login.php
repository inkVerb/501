<?php

// Include our config (with SQL) up near the top of our PHP file
include ('./in.db.php');

// Include our recover cluster
if (isset($_GET['s'])) {

  // Assign the GET value
  $secure_string = $_GET['s'];
  $secure_string_trim = DB::trimspace($secure_string); // We need to SQL escape this because it is a user input

  // Assign the current time
  $time_now = date("Y-m-d H:i:s");

  // See if the string is in the database and has not yet expired
  $query = $database->prepare("SELECT userid FROM strings WHERE BINARY random_string=:random_string AND date_expires > :date_expires");
  $query->bindParam(':random_string', $secure_string_trim);
  $query->bindParam(':date_expires', $time_now);
  $row = $pdo->exec_($query);
  // Check to see that our SQL query returned exactly 1 row
  if ($pdo->numrows == 1) {
    foreach ($rows as $row) {
      // Assign the values
      $user_id = "$row->userid";
    }
    $query = "SELECT fullname FROM users WHERE id=:id";
    $query->bindParam(':id', $user_id);
    $rows = $pdo->exec_($query);
    // Check to see that our SQL query returned exactly 1 row
    if ($pdo->numrows == 1) {
      foreach ($rows as $row) {
        // Assign the value
        $fullname = "$row->fullname";
      }
      // Set the $_SESSION array
      $_SESSION['user_id'] = $user_id;
      $_SESSION['full_name'] = $fullname;

      // Set the key to "dead" so it can't be used again
      $query = $database->prepare("UPDATE strings SET usable='dead' WHERE random_string=:random_string AND userid=:userid");
      $query->bindParam(':random_string', $secure_string_trim);
      $query->bindParam(':userid', $user_id);
      $pdo->exec_($query);
      if (!$pdo->ok) {
        echo '<p class="error">Strange error killing a real key!</p>';
      }
      // Redirect to the webapp page
      header("Location: webapp.php");


    } else { // No user by that ID
        echo '<p class="error">Sorry, user does not exist!</p>';
      }

  } else { // No such random string
    echo '<p class="error">Sorry, link does not exist or is expired!</p>';
  }


// Someone tried this without a string
} else {

echo "No script kiddies!";

}


?>

</body>
</html>
