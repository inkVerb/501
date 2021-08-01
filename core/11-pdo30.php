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
    // $val = $pdo->delete($table, $column, $value);

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

  // SELECT multiple rows method
  public function selectmulti($table, $cols = '*', $wcol = '*', $vcol = '*') {
    // Usage $pdo = new DB;
    // $val = $pdo->selectmulti($table, $columns='*', $where_col='*', $where_value='*');
    // foreach ($val as $one) { echo "Some Col: $one->some_col<br>"; }

    global $database;

    // Prepare SQL query
    $query = "SELECT $cols FROM $table";
    // WHERE arguments
    $query .= (($wcol == '*') || ($vcol == '*')) ?
    "" :
    " WHERE $wcol='$vcol'";

    // Try the query
    try {
      $statement = $database->prepare($query);
      $statement->execute();
    } catch (PDOException $error) {
      $this->pdo_error($query, $error->getMessage());
    }

    // Uncomment for curiosity
    echo "\$query = <code>$query</code><br>";

    // Return fetched SQL response object
    return $statement->fetchAll();

  } // selectmulti()

  // SELECT complex multiple rows method
  public function selectcomplex($table, $wcols, $vcols, $cols = '*') {
    // Usage $pdo = new DB;
    // $val = $pdo->selectcomplex($table, $where_col_list, $where_value_list, $columns='*');
    // foreach ($val as $one) { echo "Some Col: $one->some_col<br>"; }

    global $database;

    // Prepare array of $cols=key & $vals=value
    $wcols_arr = preg_split('~,\s*~', $wcols);
    $vcols_arr = preg_split('~,\s*~', $vcols);
    $where_array = array_combine($wcols_arr, $vcols_arr);

    // Prepare SQL SET statement
    $where_statement = "";
    foreach ( $where_array as $k => $v ) {
      $where_statement .= "$k='$v' AND ";
    }
    $where_statement = rtrim($where_statement, ' AND '); // remove last AND

    // Prepare SQL query
    $query = "SELECT $cols FROM $table WHERE $where_statement";

    // Try the query
    try {
      $statement = $database->prepare($query);
      $statement->execute();
    } catch (PDOException $error) {
      $this->pdo_error($query, $error->getMessage());
    }

    // Uncomment for curiosity
    echo "\$query = <code>$query</code><br>";

    // Return fetched SQL response object
    return $statement->fetchAll();

  } // selectcomplex()

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

// SELECT multiple current rows
echo "Before INSERT: selectmulti('fruit')<br>";
$val = $pdo->selectmulti('fruit');
foreach ($val as $one) {
  echo "Name: $one->name Color: $one->color Locale: $one->locale<br>";
}
echo "<hr><br>";

// INSERT one row
echo "INSERT<br>";
$val = $pdo->insert('fruit', 'name, color, locale, market', "'apple', 'yellow', 'Japan', 'Asia'");
echo "Last new ID: $pdo->lastid<br>";
echo ($pdo->change) ? "PDO reports rows changed<br><br>" : "No change<br><br>";

// SELECT multiple updated rows
echo "<br>After INSERT: selectmulti('fruit')<br>";
$val = $pdo->selectmulti('fruit');
foreach ($val as $one) {
  echo "Name: $one->name Color: $one->color Locale: $one->locale<br>";
}
echo "<hr><br>";

// INSERT another row
echo "INSERT<br>";
$val = $pdo->insert('fruit', 'name, color, locale, market', "'apple', 'yellow', 'Korea', 'Pacifica'");
echo "Last new ID: $pdo->lastid<br>";
echo ($pdo->change) ? "PDO reports rows changed<br><br>" : "No change<br><br>";

// SELECT multiple updated rows
echo "<br>After INSERT: selectmulti('fruit')<br>";
$val = $pdo->selectmulti('fruit');
foreach ($val as $one) {
  echo "Name: $one->name Color: $one->color Locale: $one->locale<br>";
}
echo "<hr><br>";


// SELECT complex multiple rows
echo "<br>SELECT complex multiple: selectcomplex('fruit', 'name', 'apple')<br>";
$val = $pdo->selectcomplex('fruit', 'name', 'apple');
foreach ($val as $one) {
  echo "Name: $one->name Color: $one->color Locale: $one->locale<br>";
}
echo "<hr><br>";

// SELECT complex multiple rows again
echo "<br>SELECT complex multiple again: selectcomplex('fruit', 'name, color', 'apple, yellow')<br>";
$val = $pdo->selectcomplex('fruit', 'name, color', 'apple, yellow');
foreach ($val as $one) {
  echo "Name: $one->name Color: $one->color Locale: $one->locale<br>";
}
echo "<hr><br>";

// DELETE the row again
echo "DELETE<br>";
$val = $pdo->delete('fruit', 'color', 'yellow');
echo ($pdo->change) ? "PDO reports rows changed<br><br>" : "No change<br><br>";

// SELECT multiple rows again
echo "<br>After DELETE: selectmulti('fruit')<br>";
$val = $pdo->selectmulti('fruit');
foreach ($val as $one) {
  echo "Name: $one->name Color: $one->color Locale: $one->locale<br>";
}
echo "<hr><br>";
?>
