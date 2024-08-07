<?php

// MySQLi config
DEFINE ('DB_NAME', 'food_db');
DEFINE ('DB_USER', 'food_usr');
DEFINE ('DB_PASSWORD', 'foodpassword');
DEFINE ('DB_HOST', 'localhost');

// Database connection
$database = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

// Character seting
mysqli_set_charset($database, 'utf8mb4');

// Function to escape for SQL
function escape_sql($data) {

  // Database connection $database variable is needed here
	global $database;

  // Don't process null or empty $data
  if ( (is_null($data)) || ($data == '') ) { return ''; }

  // Remove whitespace
  $trimmed_data = trim(preg_replace('/\s+/', ' ', $data));

	// Apply mysqli_real_escape_string()
	return mysqli_real_escape_string($database, $trimmed_data);

}
