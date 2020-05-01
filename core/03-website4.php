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

//// Make a MySQLi database call
// $Variable = "SQL query";
$query = "SELECT name, type, have, count, prepared FROM fruit"; // Removed "WHERE name='bananas'" so we get multiple rows
// Tell our $query to use our database connection (from in.config.php)
$call = mysqli_query($database, $query);
// $row became the while ($rows... ) loop below

////// SQL End //////


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
    </tr>";

//// Each SQL row as a new row in the HTML table
// Loop the SQL response rows through an auto-indexed array
while ( $rows = mysqli_fetch_array($call, MYSQLI_NUM) ) {
  // Assign each array item to a variable (loops many times)
  $fruit_name = "$rows[0]";
  $fruit_type = "$rows[1]";
  $fruit_have = "$rows[2]";
  $fruit_count = "$rows[3]";
  $fruit_prepared = "$rows[4]";
  // echo each item as a table row <tr> (loops many times)
  echo "
    <tr>
      <td>$fruit_name</td>
      <td>$fruit_type</td>
      <td>$fruit_have</td>
      <td>$fruit_count</td>
      <td>$fruit_prepared</td>
    </tr>
    ";
} //// End SQL loop

// Finish our table
echo "
  </tbody>
</table>
";

?>

</body>
</html>
