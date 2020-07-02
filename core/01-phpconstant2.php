<?php

// Include our config file with the constants and function
require_once ('./in.config.php');

?>

<!DOCTYPE html>
<html>
<head>
  <title> <?php echo WEBSITE_TITLE; ?> </title>
</head>
<body>

<?php

echo "<h1>".WEBSITE_TITLE."</h1>";

echo "<h2>".WEBSITE_SLOGAN."</h2>";

echo "<h3>Here are our constants...</h3>";

// Use the simple function
echoConstants();

?>

</body>
</html>
