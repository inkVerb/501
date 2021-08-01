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

    $nameHostChar = "mysql:host=$this->db_host; dbname=$this->db_name; charset=utf8mb4";
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

  // UPDATE method
  public function update($table, $cols, $vals, $wcol, $vcol) {
    // Usage $pdo = new DB;
    // $val = $pdo->update($table, $columns, $values, $where_col, $where_value);

    // Prepare array of $cols=key & $vals=value
    $cols_arr = preg_split('~,\s*~', $cols);
    $vals_arr = preg_split('~,\s*~', $vals);
    $set_array = array_combine($cols_arr, $vals_arr);

    // Prepare SQL SET statement
    $set_statement = "";
    foreach ( $set_array as $k => $v ) {
      $set_statement .= "$k='$v',";
    }
    $set_statement = rtrim($set_statement, ','); // remove last comma

    // Prepare SQL query
    $query = "UPDATE $table SET $set_statement WHERE $wcol='$vcol';";

    // Uncomment for curiosity
    echo "\$query = <code>$query</code><br>";

    // Run the connection statement
    $statement = $this->conn()->query($query);

    // Return fetched SQL response object
    return $statement->fetch();

  } // update()

} // class DB

// Instantiate
$pdo = new DB;

// Use //

// SELECT current row
echo "Before UPDATE:<br>";
$val = $pdo->select('fruit', 'name', 'apple');
echo "Name: $val->name Color: $val->color Locale: $val->locale<br><hr><br>";

// UPDATE the database
echo "UPDATE<br>";
$val = $pdo->update('fruit', 'color', 'red', 'name', 'apple');

// SELECT updated row
echo "<br>After UPDATE:<br>";
$val = $pdo->select('fruit', 'name', 'apple');
echo "Name: $val->name Color: $val->color Locale: $val->locale<br><hr><br>";

// UPDATE the database again
echo "UPDATE<br>";
$val = $pdo->update('fruit', 'color, locale', 'green, Maine', 'name', 'apple');

// SELECT updated row again
echo "<br>After UPDATE:<br>";
$val = $pdo->select('fruit', 'name', 'apple');
echo "Name: $val->name Color: $val->color Locale: $val->locale<br><hr><br>";

?>
