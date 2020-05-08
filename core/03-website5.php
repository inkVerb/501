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
$query = "SELECT name, type, date_created FROM fruit";
$call = mysqli_query($database, $query);
// $row became the while ($row... ) loop below

////// SQL End //////


// Our actual website

// Start our HTML table
echo "
<table>
  <tbody>
    <tr>
      <td>Name:</td>
      <td>Type:</td>
      <td>Created:</td>
    </tr>";

//// Each SQL row as a new row in the HTML table

while ( $row = mysqli_fetch_array($call, MYSQLI_NUM) ) {

  $fruit_name = "$row[0]";
  $fruit_type = "$row[1]";
  $fruit_date = "$row[2]";

  echo "
    <tr>
      <td>$fruit_name</td>
      <td>$fruit_type</td>
      <td>$fruit_date</td>
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
