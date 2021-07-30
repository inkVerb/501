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

  // INSERT method
  public function insert($table, $cols, $vals) {
    // Usage $pdo = new DB;
    // $val = $pdo->insert($table, $columns, $values);

    // Run the query
    $query = "INSERT INTO $table ($cols) VALUES ($vals);";
    $statement = $this->conn()->query($query);

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

    // Run the query
    $query = "DELETE FROM $table WHERE $col='$val'";
    $statement = $this->conn()->query($query);

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

    // Build the connection statement
    $statement = $this->conn()->query($query);

    // Success statements
    $this->worked = ($statement) ? true : false;

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

    $query = "UPDATE $table SET $set_statement WHERE $wcol='$vcol';";

    // Uncomment for curiosity
    echo "\$query = <code>$query</code><br>";

    // Build the connection statement
    $statement = $this->conn()->query($query);

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

echo "Before INSERT:<br>";

// SELECT current row
$val = $pdo->select('fruit', '*', 'name', 'banana');
echo ($pdo->worked) ? "Query worked<br>" : "Query failed<br>";
echo "Name: $val->name Color: $val->color Locale: $val->locale<br><hr><br>";

// INSERT  the database
$val = $pdo->insert('fruit', 'name, color, locale, market', "'banana', 'green', 'Thailad', 'Southeast Asia'");
echo ($pdo->worked) ? "Query worked with " . $pdo->lastid . "<br>" : "Query failed<br>";
echo ($pdo->changed) ? "Stuff changed<br><br>" : "No change<br><br>";

echo "After INSERT:<br>";

// SELECT updated row
$val = $pdo->select('fruit', '*', 'name', 'banana');
echo ($pdo->worked) ? "Query worked<br>" : "Query failed<br>";
echo "Name: $val->name Color: $val->color Locale: $val->locale<br><hr><br>";

// DELETE the database again
$val = $pdo->delete('fruit', 'name', 'banana');
echo ($pdo->worked) ? "Query worked<br>" : "Query failed<br>";
echo ($pdo->changed) ? "Stuff changed<br><br>" : "No change<br><br>";

echo "After DELETE:<br>";

// SELECT updated row again
$val = $pdo->select('fruit', '*', 'name', 'banana');
echo ($pdo->worked) ? "Query worked<br>" : "Query failed<br>";
echo "Name: $val->name Color: $val->color Locale: $val->locale<br><hr><br>";

?>
