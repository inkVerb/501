<?php

// Create our error handler to print errors
function vip_error($e_number, $e_message, $e_file, $e_line) {

	// Build the error message
	$message = "Hey, silly! Error in '$e_file' on line $e_line:\n$e_message\n";

	// Add the backtrace
	$message .= "<pre>" .print_r(debug_backtrace(), 1) . "</pre>\n";


	echo nl2br($message); // This is "new line to break", so lines will be seen in HTML
  // nl2br($string) : \n --> <br>

	return true; // So that PHP doesn't try to handle the error again

} // End of vip_error() definition

// Use the error handler
set_error_handler('vip_error');

// Create our error
echo $nothere;

// Normal message
echo "This page loaded.";

?>
