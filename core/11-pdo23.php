<?php

$db_name = 'test_pdo';
$db_user = 'pdo_user';
$db_pass = 'pdopassword';
$db_host = 'localhost';

$nameHostChar = "mysql:host=$db_host; dbname=$db_name; charset=utf8";
$opt = [
  PDO::ATTR_EMULATE_PREPARES => false,
  PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
  PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
];
$database = new PDO($nameHostChar, $db_user, $db_pass, $opt);

// Use //

// Build the connection statement
$query = "SELECT * FROM fruit WHERE name='apple'";
$statement = $database->query($query);

// Fetch SQL response object
$val = $statement->fetch();

// Display on webpage
echo $val->color;

?>
