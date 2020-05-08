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

  // Add the database if everything checks out
  if ($checks_out == true) {
    $query = "INSERT INTO fruit (name, type, prepared) VALUES ('$fruitname', '$type', '$prepared')";
    $call = mysqli_query($database, $query); // This actually runs the INSERT query

    // Check to see that our SQL query worked out
    if ($call) { // Test simply for 'true' since $call has already run and returned a true/false response
      // Unset these so they don't appear in the form
      unset($fruitname);
      unset($type);
      unset($prepared);
      // Show a messsage
      echo '<p class="green">New fruit added!</p>
      <p>SQL query: <code>'.$query.'</code></p>';
    } else {
      echo '<p class="error">Database error!</p>
      <p>SQL query: <code>'.$query.'</code></p>';
    } // End database check
  } else {
    echo '<p class="error">Errors! Try again.</p>';
  }

} // Finish POST if

// Make a MySQLi database call
$query = "SELECT name, type, have, count, prepared, date_created FROM fruit";
$call = mysqli_query($database, $query);
// $row loop below from $call

// Our actual website

// Start our HTML table
echo "
<table>
  <tbody>
    <tr>
      <td>Name:</td>
      <td>Type:</td>
      <td>Have:</td>
      <td>Count:</td>
      <td>Prepared:</td>
      <td>Created:</td>
    </tr>";

//// Each SQL row as a new row in the HTML table

while ( $row = mysqli_fetch_array($call, MYSQLI_NUM) ) {

  $fruit_name = "$row[0]";
  $fruit_type = "$row[1]";
  $fruit_have = "$row[2]";
  $fruit_count = "$row[3]";
  $fruit_prepared = "$row[4]";
  $fruit_date = "$row[5]";

  echo "
    <tr>
      <td>$fruit_name</td>
      <td>$fruit_type</td>
      <td>$fruit_have</td>
      <td>$fruit_count</td>
      <td>$fruit_prepared</td>
      <td>$fruit_date</td>
    </tr>
    ";
} //// End SQL loop

// Finish our table
echo "
  </tbody>
</table>
";

// Our form
echo '<h1>Add a new fruit</h1>
<form action="website.php" method="post">';

echo formInput('fruitname', $fruitname, $check_err);
echo formInput('type', $type, $check_err);
echo formInput('prepared', $prepared, $check_err);

echo '
  <input type="submit" value="Submit Button">
</form>
';

?>

</body>
</html>
