<!DOCTYPE html>
<html>
<head>
</head>
<body>

<?php

// Define our constants
define ('CONSTANT_ONE', 'I am first and I am one!');
define ('WEBSITE_TITLE', 'Codia :: inkVerb');
define ('WEBSITE_SLOGAN', 'Ink is a verb, get inking.');

// Define a variable
$variable = "some variable value";

if (isset($variable)) {
  echo "isset() $variable <br>";
}

if (defined('CONSTANT_ONE')) {
  echo "defined() ".CONSTANT_ONE." <br>";
}

// Make a simple function to use the constants
function echoConstants() {
  //global $variable; // Uncomment to see it work
  echo "variable: $variable<br><br>";
  echo "CONSTANT_ONE: ".CONSTANT_ONE."<br><br>";
  echo "WEBSITE_TITLE: ".WEBSITE_TITLE."<br><br>";
  echo "WEBSITE_SLOGAN: ".WEBSITE_SLOGAN."<br><br>";
}

// Render our HTML
echo "<p>$variable</p>";

$variable = "some other value"; // Same variable
echo '<p>'.$variable.'</p>'; // It is different here

echo "<h1>WEBSITE_TITLE</h1>"; // Wrong

echo "<h2>WEBSITE_SLOGAN</h2>"; // Wrong

echo "<h1>".WEBSITE_TITLE."</h1>";

define ('WEBSITE_TITLE', 'Codia :: inkVerb'); // Re-define existing constant

echo "<h1>".WEBSITE_TITLE."</h1>"; // Constant is unchanged

echo "<h2>".WEBSITE_SLOGAN."</h2>";

echo "<h3>Here are our constants...</h3>";

// Use the simple function
echoConstants();

?>

</body>
</html>
