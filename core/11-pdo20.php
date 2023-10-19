<?php

// Database info
$db_name = 'test_pdo';
$db_user = 'pdo_user';
$db_pass = 'pdopassword';
$db_host = 'localhost';

// Database connection
$nameHostChar = "mysql:host=$db_host; dbname=$db_name; charset=utf8mb4";
$opt = [
  PDO::ATTR_EMULATE_PREPARES => false,
  PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
  PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_BOTH,
];
$database = new PDO($nameHostChar, $db_user, $db_pass, $opt);

// PDO error handler function
function pdo_error($query, $error_message) {
    echo "SQL error from <pre>$query</pre><br>$error_message";
    exit ();
}

// Populate the table
try {
  $multiquery = "
  START TRANSACTION;
  INSERT INTO fruit (name) VALUES ('apple');
  INSERT INTO fruit (name) VALUES ('kiwi');
  COMMIT;
  ";
  $statement = $database->exec($multiquery);
} catch (PDOException $error) {
  pdo_error($multiquery, $error->getMessage());
}

// Ternary $statement test
echo ($statement) ? "\$statement success" : "\$statement fail: $statement";

// Check the actual database
try { // apple
  $query = "SELECT * FROM fruit WHERE  name = 'apple'";
  $statement = $database->query($query);
  $success = ($statement->rowCount() > 0) ? true : false; // Success?
} catch (PDOException $error) {
  pdo_error($query, $error->getMessage());
}

if ($success) try { // kiwi
  $query = "SELECT * FROM fruit WHERE  name = 'kiwi'";
  $statement = $database->query($query);
  $success = ($statement->rowCount() > 0) ? true : false; // Success?
} catch (PDOException $error) {
  pdo_error($query, $error->getMessage());
}

// If it worked, show another message
if ($success) {
  echo "<br><br>
  Inserted rows using <code>exec()</code> for this query:<br><code>$multiquery</code><br><br>
  Query for last check: <code>$query</code><br>
  Affected rows: ".$statement->rowCount()."<br>
  <hr><br>";
}

?>
