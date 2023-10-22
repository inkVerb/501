<?php

// Start our $_SESSION
session_start();

// In case you want to show errors
// ini_set('display_errors', '1');
// ini_set('display_startup_errors', '1');
// error_reporting(E_ALL);

// MySQLi config
require_once ('./in.sql.php');

// Database connection
$database = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

// Character seting
mysqli_set_charset($database, 'utf8');

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
