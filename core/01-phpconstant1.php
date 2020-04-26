<!DOCTYPE html>
<html>
<head>
</head>
<body>

<?php

// Define our constants
define ('CONSTANT_ONE', 'I am first and I am one!');
define ('WEBSITE_TITLE', 'VIP Linux :: inkVerb');
define ('WEBSITE_SLOGAN', 'Ink is a verb, get inking.');

// Make a simple function to use the constants
function echoConstants() {
  echo "CONSTANT_ONE: ".CONSTANT_ONE."<br><br>";
  echo "WEBSITE_TITLE: ".WEBSITE_TITLE."<br><br>";
  echo "WEBSITE_SLOGAN: ".WEBSITE_SLOGAN."<br><br>";
}

// Render our HTML
echo "<h1>WEBSITE_TITLE</h1>"; // Wrong

echo "<h2>WEBSITE_SLOGAN</h2>"; // Wrong

echo "<h1>".WEBSITE_TITLE."</h1>";

echo "<h2>".WEBSITE_SLOGAN."</h2>";

echo "<h3>Here are our constants...</h3>";

// Use the simple function
echoConstants();

?>

</body>
</html>
