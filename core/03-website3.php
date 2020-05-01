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
$query = "SELECT name, type, have, count, prepared FROM fruit WHERE name='bananas'";
// Tell our $query to use our database connection (from in.config.php)
$call = mysqli_query($database, $query);
// Arrange the SQL response row into an auto-indexed array, like this: $row[KEY]
$row = mysqli_fetch_array($call, MYSQLI_NUM);
// Assign each array item to a variable
$fruit_name = "$row[0]";
$fruit_type = "$row[1]";
$fruit_have = "$row[2]";
$fruit_count = "$row[3]";
$fruit_prepared = "$row[4]";

////// SQL End //////


// The functions, POST, and the form from the last step are gone, but they started here


// Our actual website

echo "
<table>
  <tbody>
    <tr>
      <td>Name:</td>
      <td>Type:</td>
      <td>Have:</td>
      <td>Count:</td>
      <td>Prepared:</td>
    </tr>
    <tr>
      <td>$fruit_name</td>
      <td>$fruit_type</td>
      <td>$fruit_have</td>
      <td>$fruit_count</td>
      <td>$fruit_prepared</td>
    </tr>
  </tbody>
</table>
";

?>

</body>
</html>
