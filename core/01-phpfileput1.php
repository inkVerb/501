<?php

// Write the file
file_put_contents('./fileput.1', "I am text in a file\n");

// See if it exists
if (file_exists('./fileput.1')) {
  echo "File exists";
} else {
  echo "Oops, file does not exist";
}

?>
