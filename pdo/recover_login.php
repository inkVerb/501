<?php

// Include our config (with SQL) up near the top of our PHP file
include ('./in.config.php');

// Include our recover cluster
if (isset($_GET['s'])) {

  // Assign the GET value
  $secure_string = $_GET['s'];
  $secure_string_sqlesc = DB::esc($secure_string); // We need to SQL escape this because it is a user input

  // Assign the current time
  $time_now = date("Y-m-d H:i:s");

  // See if the string is in the database and has not yet expired
  $query = "SELECT userid FROM strings WHERE BINARY random_string='$secure_string_sqlesc' AND date_expires > '$time_now'";
  $call = mysqli_query($database, $query);
  // Check to see that our SQL query returned exactly 1 row
  if (mysqli_num_rows($call) == 1) {
    // Assign the values
    $row = mysqli_fetch_array($call, MYSQLI_NUM);
      $user_id = "$row[0]";


    $query = "SELECT id, fullname FROM users WHERE id='$user_id'";
    $call = mysqli_query($database, $query);
    // Check to see that our SQL query returned exactly 1 row
    if (mysqli_num_rows($call) == 1) {
      // Assign the values
      $row = mysqli_fetch_array($call, MYSQLI_NUM);
      $user_id = "$row[0]"; // Reassign this just to be sure
      $fullname = "$row[1]";
      // Set the $_SESSION array
      $_SESSION['user_id'] = $user_id;
      $_SESSION['full_name'] = $fullname;

      // Set the key to "dead" so it can't be used again
      $query = "UPDATE strings SET usable='dead' WHERE random_string='$secure_string_sqlesc' AND userid='$user_id'";
      $call = mysqli_query($database, $query);
      if (!$call) {
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
