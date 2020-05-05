<?php

// Set variables
$file_name = './fileput.2';
$file_text = "I am text in another file\n";

// Write the file
file_put_contents($file_name, $file_text);

// See if it exists
if (file_exists($file_name)) {
  echo "File 2 exists";
} else {
  echo "Oops, file 2 does not exist";
}

?>
