<?php

// No header/footer in this .php script because it is a simple "job", not a webpage

// Our config file must have absolute paths, so let's re-arrange thigns...

// MySQLi Connection
require_once ('./in.sql.php');

// Database connection
require_once ('./in.conf.php');

// Run the SQL query
$pdo->exec_($database->prepare("DELETE FROM strings WHERE usable NOT LIKE 'live' OR date_expires < NOW()"));
// Check to see that our SQL query returned exactly 1 row
if ($pdo->ok) {
  echo "Old recovery keys deleted.";
} else {
  echo "Error deleting old recovery keys.";
}

?>
