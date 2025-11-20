<?php

// Define our constants
define ('CONSTANT_ONE', 'I am first and I am one!');
define ('WEBSITE_TITLE', 'Codia :: inkVerb');
define ('WEBSITE_SLOGAN', 'Ink is a verb, get inking.');

// Make a simple function to use the constants
function echoConstants() {
  echo "CONSTANT_ONE: ".CONSTANT_ONE."<br><br>";
  echo "WEBSITE_TITLE: ".WEBSITE_TITLE."<br><br>";
  echo "WEBSITE_SLOGAN: ".WEBSITE_SLOGAN."<br><br>";
}
