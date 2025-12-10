<?php

// $live = true;

// Create our error handler to print errors
function vip_error($e_number, $e_message, $e_file, $e_line) {
	// Get our live setting
	global $live;

	// Build the error message
	$message = "<p style='color:red'>Hey, silly! Error in '$e_file' on line $e_line:<br><pre><b>$e_message</b></pre></p>";

	// Add the backtrace
	$message .= "<pre style='color:magenta'>" . print_r(debug_backtrace(), 1) . "</pre>";

	if ($live != true) {
		echo $message;
	}

	return true; // So that PHP doesn't try to handle the error again

} // End of vip_error() definition

// Use the error handler
set_error_handler('vip_error');

// Create our error
echo $nothere;

// Normal message
echo "This page loaded.";

?>
