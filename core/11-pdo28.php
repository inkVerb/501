<?php

class DB {

  // Database info
  private $db_name = 'test_pdo';
  private $db_user = 'pdo_user';
  private $db_pass = 'pdopassword';
  private $db_host = 'localhost';

  // Global result properties
  public $worked;
  public $change;
  public $lastid;

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

  // Escape method
  static function esc($data) {
    $trimmed_data = trim(preg_replace('/\s+/', ' ', $data));
    return PDO::quote($trimmed_data);
  } // esc()

  // PDO error handler
  protected function pdo_error($query, $error_message) {
    echo "SQL error from <pre>$query</pre><br>$error_message";
    exit();
  } // pdo_error()

  // INSERT method
  public function insert($table, $cols, $vals) {
    // Usage $pdo = new DB;
    // $val = $pdo->insert($table, $columns, $values);

    // Try the query
    $query = "INSERT INTO $table ($cols) VALUES ($vals);";
    try {
      $statement = $this->conn()->query($query);
    } catch (PDOException $error) {
      pdo_error($query, $error->getMessage());
    }

    // Uncomment for curiosity
    echo "\$query = <code>$query</code><br>";

    // Success statements
    $this->worked = ($statement) ? true : false;
    $this->change = ($statement->rowCount() == 1) ? true : false;
    $this->lastid = $this->conn()->lastInsertId();

  } // insert()

  // DELETE method
  public function delete($table, $col, $val) {
    // Usage $pdo = new DB;
    // $val = $pdo->delete($table, $column, $value);

    // Try the query
    $query = "DELETE FROM $table WHERE $col='$val'";
    try {
      $statement = $this->conn()->query($query);
    } catch (PDOException $error) {
      pdo_error($query, $error->getMessage());
    }

    // Uncomment for curiosity
    echo "\$query = <code>$query</code><br>";

    // Success statements
    $this->worked = ($statement) ? true : false;
    $this->change = ($statement->rowCount() >= 1) ? true : false;

  } // delete()

  // SELECT method
  public function select($table, $cols = '*', $wcol = '*', $vcol = '*') {
    // Usage $pdo = new DB;
    // $val = $pdo->select($table, $columns, $where_col, $where_value);

    $query = "SELECT $cols FROM $table";

    // WHERE arguments
    $query .= (($wcol == '*') || ($vcol == '*')) ?
    "" :
    " WHERE $wcol='$vcol'";

    // Uncomment for curiosity
    echo "\$query = <code>$query</code><br>";

    // Try the query
    try {
      $statement = $this->conn()->query($query);
    } catch (PDOException $error) {
      pdo_error($query, $error->getMessage());
    }

    // Success statements
    $this->worked = ($statement) ? true : false;

    // Return fetched SQL response object
    return $statement->fetch();

  } // select()

  // SELECT multiple rows method
  public function selectmulti($table, $cols = '*', $wcol = '*', $vcol = '*') {

    DEV::finish->me;

  } // selectmulti()

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

    $query = "UPDATE $table SET $set_statement WHERE $wcol='$vcol';";

    // Uncomment for curiosity
    echo "\$query = <code>$query</code><br>";

    // Try the query
    try {
      $statement = $this->conn()->query($query);
    } catch (PDOException $error) {
      pdo_error($query, $error->getMessage());
    }

    // Success statements
    $this->worked = ($statement) ? true : false;
    $this->change = ($statement->rowCount() > 0) ? true : false;

    // Return fetched SQL response object
    return $statement->fetch();

  } // update()

} // class DB

// Instantiate
$pdo = new DB;

// Use //

echo "Before UPDATE:<br>";

// SELECT current row
$val = $pdo->select('fruit', '*', 'name', 'apple');
echo "Name: $val->name Color: $val->color Locale: $val->locale<br><hr><br>";

// UPDATE the database
$val = $pdo->update('fruit', 'color', 'red', 'name', 'apple');
echo ($pdo->worked) ? "It worked<br>" : "It failed<br>";

echo "After UPDATE:<br>";

// SELECT updated row
$val = $pdo->select('fruit', '*', 'name', 'apple');
echo "Name: $val->name Color: $val->color Locale: $val->locale<br><hr><br>";

// UPDATE the database again
$val = $pdo->update('fruit', 'color, locale', 'green, Maine', 'name', 'apple');
echo ($pdo->worked) ? "It worked<br>" : "It failed<br>";

echo "After UPDATE:<br>";

// SELECT updated row again
$val = $pdo->select('fruit', '*', 'name', 'apple');
echo "Name: $val->name Color: $val->color Locale: $val->locale<br><hr><br>";

?>
