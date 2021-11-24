<?php

// Database connection
$db_name = 'test_pdo';
$db_user = 'pdo_user';
$db_pass = 'pdopassword';
$db_host = 'localhost';
$nameHostChar = "mysql:host=$db_host; dbname=$db_name; charset=utf8mb4";

$opt = [
  PDO::ATTR_EMULATE_PREPARES => false,
  PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
  PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
];
$database = new PDO($nameHostChar, $db_user, $db_pass, $opt);

class DB {

  // Global result properties & methods
  public $change;
  public $lastid;
  // Usage $pdo = new DB;
  // $val = ($pdo->change) ? true : false;
  // $val = $pdo->lastid;

  // Escape method
  static function esc($data) {
    // Usage
    // $val = DB::esc($string);

    $trimmed_data = trim(preg_replace('/\s+/', ' ', $data));
    return $trimmed_data;
  } // esc()

  // PDO error handler
  protected function pdo_error($query, $error_message) {
    echo "SQL error from <pre>$query</pre><br>$error_message";
    exit ();
  } // pdo_error()

  // INSERT method
  public function insert($table, $cols, $vals) {
    // Usage $pdo = new DB;
    // $pdo->insert($table, $columns, $values);

    global $database;

    // Try the query
    $query = "INSERT INTO $table ($cols) VALUES ($vals);";
    try {
      $statement = $database->query($query);
    } catch (PDOException $error) {
      $this->pdo_error($query, $error->getMessage());
    }

    // Uncomment for curiosity
    echo "\$query = <code>$query</code><br>";

    // Success statements
    $this->change = ($statement->rowCount() == 1) ? true : false;
    $this->lastid = $database->lastInsertId();

  } // insert()

  // DELETE method
  public function delete($table, $col, $val) {
    // Usage $pdo = new DB;
    // $pdo->delete($table, $column, $value);

    global $database;

    // Try the query
    $query = "DELETE FROM $table WHERE $col='$val'";
    try {
      $statement = $database->prepare($query);
      $statement->execute();
    } catch (PDOException $error) {
      $this->pdo_error($query, $error->getMessage());
    }

    // Uncomment for curiosity
    echo "\$query = <code>$query</code><br>";

    // Success statement
    $this->change = ($statement->rowCount() >= 1) ? true : false;

  } // delete()

  // SELECT method
  public function select($table, $wcol, $vcol, $cols='*') {
    // Usage $pdo = new DB;
    // $val = $pdo->select($table, $where_col, $where_value, $columns='*');

    global $database;

    // Prepare SQL query
    $query = "SELECT $cols FROM $table WHERE $wcol='$vcol'";

    // Uncomment for curiosity
    echo "\$query = <code>$query</code><br>";

    // Try the query
    try {
      $statement = $database->query($query);
    } catch (PDOException $error) {
      $this->pdo_error($query, $error->getMessage());
    }

    // Return fetched SQL response object
    return $statement->fetch();

  } // select()

  // UPDATE method
  public function update($table, $cols, $vals, $wcol, $vcol) {
    // Usage $pdo = new DB;
    // $val = $pdo->update($table, $columns, $values, $where_col, $where_value);

    global $database;

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

    // Try the query
    try {
      $statement = $database->query($query);
    } catch (PDOException $error) {
      $this->pdo_error($query, $error->getMessage());
    }

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
echo "Name: $val->name Color: $val->color Locale: $val->locale Market: $val->market<br><hr><br>";

// INSERT the row
echo "INSERT<br>";
$pdo->insert('fruit', 'name, color, locale, market', "'banana', 'green', 'Thailad', 'Southeast Asia'");
echo "Last new ID: $pdo->lastid<br>";
echo ($pdo->change) ? "PDO reports rows changed<br><br>" : "No change<br><br>";

// SELECT updated row
echo "<br>After INSERT:<br>";
$val = $pdo->select('fruit', 'name', 'banana');
echo "Name: $val->name Color: $val->color Locale: $val->locale Market: $val->market<br><hr><br>";

// DELETE the row again
echo "DELETE<br>";
$pdo->delete('fruit', 'name', 'banana');
echo ($pdo->change) ? "PDO reports rows changed<br><br>" : "No change<br><br>";

// SELECT updated row again
echo "<br>After DELETE:<br>";
$val = $pdo->select('fruit', 'name', 'banana');
echo "Name: $val->name Color: $val->color Locale: $val->locale Market: $val->market<br><hr><br>";

?>
