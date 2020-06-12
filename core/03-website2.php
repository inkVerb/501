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
$call = mysqli_query($database, $query); // This actually runs the query
// Arrange the SQL response row into an auto-indexed array, like this: $row[KEY]
$row = mysqli_fetch_array($call, MYSQLI_NUM);
// Assign each array item to a variable
$fruit_name = "$row[0]";
$fruit_type = "$row[1]";
$fruit_have = "$row[2]";
$fruit_count = "$row[3]";
$fruit_prepared = "$row[4]";

////// SQL End //////

// Tip: If you ever have trouble with your code, simply echo the $query in your PHP script to try it manually
//eg:
//echo "<pre>$query</pre>";


// The functions, POST, and the form from the last step are gone, but they started here


// Our actual website

echo "
Name: $fruit_name
<br>
Type: $fruit_type
<br>
Have: $fruit_have
<br>
Count: $fruit_count
<br>
Prepared: $fruit_prepared
<br>
";

?>

</body>
</html>
