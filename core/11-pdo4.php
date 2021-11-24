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

// Update row for apple
try {
  $query = "UPDATE fruit SET color='green', locale='Japan', market='global' WHERE name='apple'";
  $statement = $database->query($query);
} catch (PDOException $error) {
  pdo_error($multiquery, $error->getMessage());
}

// Ternary $statement test
echo ($statement) ? "\$statement 'apple' success" : "\$statement fail: $statement";

// If it worked, show another message
if ($statement) {
  echo "<br><br>
  Updated row using <code>exec()</code> for this query:<br><code>$query</code><br><br>
  Affected rows: ".$statement->rowCount()."<br>
  <hr><br>";
}

// Update row for apple
try {
  $query = "UPDATE fruit SET color='golden', locale='Taiwan', market='Asia' WHERE name='kiwi'";
  $statement = $database->query($query);
} catch (PDOException $error) {
  pdo_error($multiquery, $error->getMessage());
}

// Ternary $statement test
echo ($statement) ? "\$statement 'kiwi' success" : "\$statement fail: $statement";

// If it worked, show another message
if ($statement) {
  echo "<br><br>
  Updated row using <code>exec()</code> for this query:<br><code>$query</code><br><br>
  Affected rows: ".$statement->rowCount()."<br>
  <hr><br>";
}

?>
