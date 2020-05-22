<?php

// No header/footer in this .php script because it is a simple "job", not a webpage

// Our config file has the SQL credentials
include ('./in.config.php');

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
