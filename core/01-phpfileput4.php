<?php

// Set file name variable
$file_name = './fileput.4';

// Use a variable in our file
$Variable = "I am a variable";

// Set the heredoc using single quotes
$file_text = <<<'EOF'
I am text in a heredoc-made file
We used this variable: $Variable

EOF;

// Write the file
file_put_contents($file_name, $file_text);

// See if it exists
if (file_exists($file_name)) {
  echo "File 4 exists";
} else {
  echo "Oops, file 4 does not exist";
}

?>
