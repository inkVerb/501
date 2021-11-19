<?php

// No header/footer in this .php script because it is a simple "job", not a webpage

// Our config file must have absolute paths, so let's re-arrange thigns...

// MySQLi Connection
require_once ('./in.sql.php');

// Database connection
$database = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

// Run the SQL query
$query = "DELETE FROM strings WHERE date_expires < NOW()";
$call = mysqli_query($database, $query);
// Check to see that our SQL query returned exactly 1 row
if ($call) {
  echo "Old recovery keys deleted.";
} else {
  echo "Error deleting old recovery keys.";
}

?>
