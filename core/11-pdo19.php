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

// Create a table
try {
  $query = "
  CREATE TABLE IF NOT EXISTS `fruit` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(90) NOT NULL,
    `color` VARCHAR(90) DEFAULT NULL,
    `locale` VARCHAR(90) DEFAULT NULL,
    `market` VARCHAR(90) DEFAULT NULL,
    `date_updated` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
  ) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;
  ";
  $statement = $database->query($query);
} catch (PDOException $error) {
  pdo_error($query, $error->getMessage());
}

// Ternary $statement test
echo ($statement) ? "\$statement success" : "\$statement fail: $statement";

// If it worked, show a message
if ($statement) {
  echo "<br><br>
  Created table using <code>query()</code> for this query:<br>
  <code>$query</code><br>
  <hr><br>";
}

?>
