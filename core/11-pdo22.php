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

// 1 SELECT rows (use numbered)
echo "<h4>1 SELECT (use numbered)</h4>";
try {
  $query = "SELECT * FROM fruit";
  $statement = $database->query($query);
} catch (PDOException $error) {
  pdo_error($query, $error->getMessage());
}

echo "auto-index:<br>";
while ($row = $statement->fetch()) {
  $f_name = "$row[1]";
  $f_color = "$row[2]";
  $f_locale = "$row[3]";
  $f_market = "$row[4]";
  echo "Name: $f_name Color: $f_color Farm: $f_locale Sold in: $f_market<br>";
}

// 2 SELECT rows (use associative)
echo "<h4>2 SELECT (use associative)</h4>";
try {
  $query = "SELECT * FROM fruit";
  $statement = $database->query($query);
} catch (PDOException $error) {
  pdo_error($query, $error->getMessage());
}

echo "associative:<br>";
while ($row = $statement->fetch()) {
  $f_name = "$row[name]";
  $f_color = "$row[color]";
  $f_locale = "$row[locale]";
  $f_market = "$row[market]";
  echo "Name: $f_name Color: $f_color Farm: $f_locale Sold in: $f_market<br>";
}

// 3 SELECT rows (use both)
echo "<h4>3 SELECT (use both)</h4>";
try {
  $query = "SELECT * FROM fruit";
  $statement = $database->query($query);
} catch (PDOException $error) {
  pdo_error($query, $error->getMessage());
}

while ($row = $statement->fetch()) {
  echo "again auto-index:<br>";
  $f_name = "$row[1]";
  $f_color = "$row[2]";
  $f_locale = "$row[3]";
  $f_market = "$row[4]";
  echo "Name: $f_name Color: $f_color Farm: $f_locale Sold in: $f_market<br>";
  echo "again associative:<br>";
  $f_name = "$row[name]";
  $f_color = "$row[color]";
  $f_locale = "$row[locale]";
  $f_market = "$row[market]";
  echo "Name: $f_name Color: $f_color Farm: $f_locale Sold in: $f_market<br><br>";
}

// 4 SELECT rows (use object)
echo "<h4>4 SELECT (use object)</h4>";
try {
  $query = "SELECT * FROM fruit";
  $statement = $database->query($query);
} catch (PDOException $error) {
  pdo_error($query, $error->getMessage());
}

echo "object:<br>";
while ($row = $statement->fetch(PDO::FETCH_OBJ)) { // Specified different from our option setting
  echo "Name: $row->name Color: $row->color Farm: $row->locale Sold in: $row->market<br>";
}

?>
