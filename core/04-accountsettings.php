<!DOCTYPE html>
<html>
<head>
  <!-- CSS file included as <link> -->
  <link href="style.css" rel="stylesheet" type="text/css" />
</head>
<body>

<?php

////// SQL //////

// Include our config (with SQL) up near the top of our PHP file
include ('./in.config.php');

// Include our functions
include ('./in.functions.php');

// POSTed form?
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

  // Include our POST checks
  include ('./in.checks.php');


  // If update: Update the database if everything checks out
  if (($checks_out == true) && (!empty($update_fruit))) {
    $query = "UPDATE fruit SET name='$fruitname' WHERE id='$fruitid'";
    $call = mysqli_query($database, $query);
    // Check to see that our SQL query worked out
    if ($call) {

      // See if it actually changed something
      if (mysqli_affected_rows($database) == 1) {
        echo '<p class="green">Fruit updated!</p>';
      } else {
        echo '<p class="orange">No change!</p>';
      }
      echo '<p>SQL query: <code>'.$query.'</code></p>';

    } else { // Database fail
      echo '<p class="error">Database error!</p>
      <p>SQL query: <code>'.$query.'</code></p>';
    } // End database check


  // If delete: RUN THIS FIRST so other checks don't matter
  } elseif (isset($delete_fruit)) {
      // Query to DELETE the row
      $query = "DELETE FROM fruit WHERE id='$delete_fruit'";
      $call = mysqli_query($database, $query);
      // mysqli_affected_rows() will also recognize deleted rows
      if (mysqli_affected_rows($database) == 1) {
        echo '<p class="green">Fruit deleted!</p>';
      } else {
        echo '<p class="error">Database error!</p>';
      }
      echo '<p>SQL query: <code>'.$query.'</code></p>';


    // If new: Insert the new entry if everything checks out
  } elseif (($checks_out == true) && (!empty($new_fruit))) {
    $query = "INSERT INTO fruit (name, type) VALUES ('$fruitname', '$type')";
    $call = mysqli_query($database, $query);
    // Check to see that our SQL query worked out
    if ($call) {

    } else {
      echo '<p class="error">Database error!</p>
      <p>SQL query: <code>'.$query.'</code></p>';
    } // End database check

    // If errors in entry
    } else {
        echo '<p class="error">Errors! Try again.</p>';
    }


} // Finish POST if

// Make a MySQLi database call
$query = "SELECT name, type, have, count, prepared, date_created, id FROM fruit";
$call = mysqli_query($database, $query);
$row = mysqli_fetch_array($call, MYSQLI_NUM);
// Assign the values
  $fruit_name = "$row[0]";
  $fruit_type = "$row[1]";
  $fruit_have = "$row[2]";
  $fruit_count = "$row[3]";
  $fruit_prepared = "$row[4]";
  $fruit_date = "$row[5]";
  $fruit_id = "$row[6]";

// Our actual website

// Start our HTML table
echo '
  <label for="NAME">Name: '.formInput('prepared', $fruit_prepared, $check_err, $fruit_id).'</label><br><br>
  <label for="NAME">Username: '.formInput('prepared', $fruit_prepared, $check_err, $fruit_id).'</label><br><br>
  <label for="NAME">Email: '.formInput('prepared', $fruit_prepared, $check_err, $fruit_id).'</label><br><br>
  <label for="NAME">Website: '.formInput('prepared', $fruit_prepared, $check_err, $fruit_id).'</label><br><br>
  <label for="NAME">Favorite Number: '.formInput('prepared', $fruit_prepared, $check_err, $fruit_id).'</label><br><br>
  <label for="NAME">Password: '.formInput('prepared', $fruit_prepared, $check_err, $fruit_id).'</label><br><br>
  <label for="NAME">Delete? '.formInput('prepared', $fruit_prepared, $check_err, $fruit_id).'</label><br><br>
  <label for="NAME"><input type="submit" value="Update"></label><br><br>
';


// Our form
echo '<h1>Add a new fruit</h1>
<form action="website.php" method="post">
<input type="hidden" name="newfruit" value="true">'; // Add this hidden value to double-check which form to process
// We must put the labels outside because we removed them from the function
echo 'Name: '.formInput('fruitname', $fruitname, $check_err, "new"); // formInput() has an extra argument so our errors only show for the right item
echo 'Type: '.formInput('type', $type, $check_err, "new");
echo 'Prepared:'.formInput('prepared', $prepared, $check_err, "new");

echo '
  <input type="submit" value="Submit Button">
</form>
';

?>

</body>
</html>
