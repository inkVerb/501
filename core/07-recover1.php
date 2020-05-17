<!DOCTYPE html>
<html>
<head>
  <!-- CSS file included as <link> -->
  <link href="style.css" rel="stylesheet" type="text/css" />
</head>
<body>

<?php

// Include our string functions
include ('./in.string_functions.php');


// Create our string
$random_string = alnumString(32);


// Our form
echo "<h1>This is a random string:</h1>
<p>$random_string</p>";

?>

</body>
</html>
