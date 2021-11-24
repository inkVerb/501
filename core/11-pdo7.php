<?php

class DB {
  private $db_name = 'test_pdo';
  private $db_user = 'pdo_user';
  private $db_pass = 'pdopassword';
  private $db_host = 'localhost';

  public function conn() {
    // Usage $pdo = new DB;
    // $statement = $pdo->conn()->query($query);

    $nameHostChar = "mysql:host=$this->db_host; dbname=$this->db_name; charset=utf8mb4";
    $opt = [
      PDO::ATTR_EMULATE_PREPARES => false,
      PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
      PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
    ];
    $database = new PDO($nameHostChar, $this->db_user, $this->db_pass, $opt);
    return $database;

  } // conn()

} // class DB

// Instantiate
$pdo = new DB;

// Use //

// Build the connection statement
$query = "SELECT * FROM fruit WHERE name='apple'";
$statement = $pdo->conn()->query($query);

// Fetch SQL response object
$val = $statement->fetch();

// Display on webpage
echo $val->color;

?>
