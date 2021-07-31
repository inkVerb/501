<?php

class DB {

  // Database info
  private $db_name = 'test_pdo';
  private $db_user = 'pdo_user';
  private $db_pass = 'pdopassword';
  private $db_host = 'localhost';

  // Global result properties
  public $change;
  public $lastid;
  // Usage $pdo = new DB;
  // $val = ($pdo->change) ? true : false;
  // $val = $pdo->lastid;

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

    // Success statement
    $this->change = ($statement->rowCount() >= 1) ? true : false;

  } // delete()

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

    // Success statement
    $this->change = ($statement->rowCount() > 0) ? true : false;

    // Return fetched SQL response object
    return $statement->fetch();

  } // update()

} // class DB

// Instantiate
$pdo = new DB;

// Use //

// SELECT current row
echo "Before INSERT:<br>";
$val = $pdo->select('fruit', 'name', 'banana');
echo "Name: $val->name Color: $val->color Locale: $val->locale<br><hr><br>";

// INSERT the row
echo "INSERT<br>";
$vals_string = "Southeast Asia"; // Anything with spaces must be passed as a variable so quotes don't end up in the database
$val = $pdo->insert('fruit', 'name, color, locale, market', "banana, green, Thailad, $vals_string");
echo "Last new ID: $pdo->lastid<br>";
echo ($pdo->change) ? "PDO reports rows changed<br><br>" : "No change<br><br>";

// SELECT updated row
echo "<br>After INSERT:<br>";
$val = $pdo->select('fruit', 'name', 'banana');
echo "Name: $val->name Color: $val->color Locale: $val->locale<br><hr><br>";

// DELETE the row
echo "DELETE<br>";
$val = $pdo->delete('fruit', 'name', 'banana');
echo ($pdo->change) ? "PDO reports rows changed<br><br>" : "No change<br><br>";

// SELECT updated row again
echo "<br>After DELETE:<br>";
$val = $pdo->select('fruit', 'name', 'banana');
echo "Name: $val->name Color: $val->color Locale: $val->locale<br><hr><br>";

?>
