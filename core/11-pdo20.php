<?php

// Database info
$db_name = 'test_pdo';
$db_user = 'pdo_user';
$db_pass = 'pdopassword';
$db_host = 'localhost';

// Database connection
$nameHostChar = "mysql:host=$db_host; dbname=$db_name; charset=utf8";
$opt = [
  PDO::ATTR_EMULATE_PREPARES => false,
  PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
  PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_BOTH,
];
$database = new PDO($nameHostChar, $db_user, $db_pass, $opt);

// PDO error handler function
function pdo_error($query, $error_message) {
    echo "SQL error from <pre>$query</pre><br>$error_message";
    exit();
}

// Fetch the rows
try {
  $query = "SELECT * FROM fruit";
  $statement = $database->query($query);
} catch (PDOException $error) {
  pdo_error($query, $error->getMessage());
}

while ($row = $statement->fetch(PDO::FETCH_NUM)) {
  $f_name = "$row[1]";
  $f_color = "$row[2]";
  $f_locale = "$row[3]";
  $f_market = "$row[4]";
  echo "Name: $f_name Color: $f_color Farm: $f_locale Sold in: $f_market<br>";
}

?>
