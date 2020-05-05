<?php

// Include our config file with the constants and function
require_once ('./in.config.php');

// Include the header
require_once ('./in.header.php');

echo "<h1>".WEBSITE_TITLE."</h1>";

echo "<h2>".WEBSITE_SLOGAN."</h2>";

echo "<h3>Here are our constants...</h3>";

// Use the simple function
echoConstants();

// Include the footer
require_once ('./in.footer.php');
?>
