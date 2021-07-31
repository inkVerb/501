<?php

class DB {

  // Database info
  private $db_name = 'test_pdo';
  private $db_user = 'pdo_user';
  private $db_pass = 'pdopassword';
  private $db_host = 'localhost';

  // Database connection
  protected function conn() {
    // Usage (inside other methods of this class)
    // $statement = $this->conn()->query($query);

    $nameHostChar = "mysql:host=$this->db_host; dbname=$this->db_name; charset=utf8";
    $opt = [
      PDO::ATTR_EMULATE_PREPARES => false,
      PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
      PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
    ];
    $database = new PDO($nameHostChar, $this->db_user, $this->db_pass, $opt);
    return $database;

  } // conn()

  // SELECT method
  public function select($table, $wcol, $vcol, $cols='*') {
    // Usage $pdo = new DB;
    // $val = $pdo->select($table, $where_col, $where_value, $columns='*');

    // Prepare SQL query
    $query = "SELECT $cols FROM $table WHERE $wcol='$vcol'";

    // Uncomment for curiosity
    echo "\$query = <code>$query</code><br>";

    // Run the connection statement
    $statement = $this->conn()->query($query);

    // Return fetched SQL response object
    return $statement->fetch();

  } // select()

} // class DB

// Instantiate
$pdo = new DB;

// Use //

// Use the database call
$val = $pdo->select('fruit', 'name', 'apple');

// Display on webpage
echo $val->color;

?>
