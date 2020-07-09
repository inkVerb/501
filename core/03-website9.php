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

  // if UPDATE: Update the database if everything checks out
  if (($checks_out == true) && (!empty($update_fruit))) {
    $fruitname_sql_safe = escape_sql($fruitname);
    $type_sql_safe = escape_sql($type);
    $have_sql_safe = escape_sql($have);
    $count_sql_safe = escape_sql($count);
    $prepared_sql_safe = escape_sql($prepared);
    $fruitid_sql_safe = escape_sql($fruitid);
    $query = "UPDATE fruit SET name='$fruitname_sql_safe', type='$type_sql_safe', have=$have_sql_safe, count='$count_sql_safe', prepared='$prepared_sql_safe' WHERE id='$fruitid_sql_safe'";
    $call = mysqli_query($database, $query);
    // Check to see that our SQL query worked out
    if ($call) { // Test simply for 'true' since $call has already run and returned a true/false response
      // Unset these so they don't appear in the form below
      unset($fruitname);
      unset($type);
      unset($have);
      unset($count);
      unset($prepared);

      // See if it actually changed something
      if (mysqli_affected_rows($database) == 1) {
        echo '<p class="green">Fruit updated!</p>';
      } else {
        echo '<p class="orange">No change!</p>';
      }
      echo '<p>SQL query: <code>'.$query.'</code></p>';
    } else {
      echo '<p class="error">Database error!</p>
      <p>SQL query: <code>'.$query.'</code></p>';
    } // End database check


  // if INSERT: Insert the new entry if everything checks out
  } elseif (($checks_out == true) && (!empty($new_fruit))) {
    $fruitname_sql_safe = escape_sql($fruitname);
    $type_sql_safe = escape_sql($type);
    $prepared_sql_safe = escape_sql($prepared);
    $query = "INSERT INTO fruit (name, type, prepared) VALUES ('$fruitname_sql_safe', '$type_sql_safe', '$prepared_sql_safe')";
    $call = mysqli_query($database, $query);
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
$query = "SELECT name, type, have, count, prepared, date_created, id FROM fruit";
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
      <td>Have?</td>
      <td>Count:</td>
      <td>Prepared:</td>
      <td>Created:</td>
      <td>Update:</td>
    </tr>";

//// Each SQL row as a new row in the HTML table

while ( $row = mysqli_fetch_array($call, MYSQLI_NUM) ) {

  $fruit_name = "$row[0]";
  $fruit_type = "$row[1]";
  $fruit_have = "$row[2]";
  $fruit_count = "$row[3]";
  $fruit_prepared = "$row[4]";
  $fruit_date = "$row[5]";
  $fruit_id = "$row[6]";

  // formInput() has an extra argument so our errors only show for the right item
  echo '
    <tr>
      <form action="website.php" method="post">
        <input type="hidden" name="fruitid" value="'.$fruit_id.'">
        <td>'.formInput('fruitname', $fruit_name, $check_err, $fruit_id).'</td>
        <td>'.formInput('type', $fruit_type, $check_err, $fruit_id).'</td>
        <td>'.formInput('have', $fruit_have, $check_err, $fruit_id).'</td>
        <td>'.formInput('count', $fruit_count, $check_err, $fruit_id).'</td>
        <td>'.formInput('prepared', $fruit_prepared, $check_err, $fruit_id).'</td>
        <td>'.$fruit_date.'</td>
        <td><input type="submit" value="Update"></td>
      </form>
    </tr>
    ';
} //// End SQL loop

// Finish our table
echo "
  </tbody>
</table>
";

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
