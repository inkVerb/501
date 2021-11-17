<?php

// Start our $_SESSION
session_start();

// In case you want to show errors
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

// MySQLi config
require_once ('./in.sql.php');

// Database connection
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
  public $rows;
  // Usage $pdo = new DB;
  // $val = ($pdo->change) ? true : false; // insert() delete() update()
  // $val = ($pdo->ok) ? true : false; // all
  // $val = $pdo->lastid; // insert()
  // $val = $pdo->rows; // select()

  // Escape method
  static function esc($data) {
    // Usage
    // $val = esc($string);

    $trimmed_data = trim(preg_replace('/\s+/', ' ', $data));
    return $trimmed_data;
  } // esc()

  // PDO error handler
  protected function pdo_error($query, $error_message) {
    echo "SQL error from <pre>$query</pre><br>$error_message";
    exit();
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
    //echo "\$query = <code>$query</code><br>";

    // Success statements
    $this->change = ($statement->rowCount() == 1) ? true : false;
    $this->lastid = $database->lastInsertId();
    $this->ok = ($statement) ? true : false;

  } // insert()

  // DELETE method
  public function delete($table, $col, $val) {
    // Usage $pdo = new DB;
    // $pdo->delete($table, $column, $value);
    // check: $pdo->delete = true;

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
    //echo "\$query = <code>$query</code><br>";

    // Success statement
    $this->change = ($statement->rowCount() >= 1) ? true : false;
    $this->ok = ($statement) ? true : false;

  } // delete()

  // SELECT method
  public function select($table, $wcol, $vcol, $cols='*') {
    // Usage $pdo = new DB;
    // $val = $pdo->select($table, $where_col, $where_value, $columns='*');
    // Then, column names are objects: $fruit = $val->fruit; $rock = $val->rock;

    global $database;

    // Prepare SQL query
    $query = "SELECT $cols FROM $table WHERE $wcol='$vcol'";

    // Uncomment for curiosity
    //echo "\$query = <code>$query</code><br>";

    // Try the query
    try {
      $statement = $database->query($query);
    } catch (PDOException $error) {
      $this->pdo_error($query, $error->getMessage());
    }

    // Rows
    $this->rows = $statement->rowCount();

    // Return fetched SQL response object
    return $statement->fetch();

  } // select()

  // SELECT WHERE BINARY method for keys
  public function key_select($table, $wcol, $vcol, $cols='*') {
    // Usage $pdo = new DB;
    // $val = $pdo->key_select($table, $where_col, $where_value, $columns='*');
    // Then, column names are objects: $fruit = $val->fruit; $rock = $val->rock;

    global $database;

    // Prepare SQL query
    $query = "SELECT $cols FROM $table WHERE BINARY $wcol='$vcol'";

    // Uncomment for curiosity
    //echo "\$query = <code>$query</code><br>";

    // Try the query
    try {
      $statement = $database->query($query);
    } catch (PDOException $error) {
      $this->pdo_error($query, $error->getMessage());
    }

    // Rows
    $this->rows = $statement->rowCount();

    // Return fetched SQL response object
    return $statement->fetch();

  } // key_select()

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
    //echo "\$query = <code>$query</code><br>";

    // Try the query
    try {
      $statement = $database->query($query);
    } catch (PDOException $error) {
      $this->pdo_error($query, $error->getMessage());
    }

    // Success statement
    $this->change = ($statement->rowCount() > 0) ? true : false;
    $this->ok = ($statement) ? true : false;

    // Return fetched SQL response object
    return $statement->fetch();

  } // update()

  // UPDATE  WHERE BINARY method for keys
  public function key_update($table, $cols, $vals, $wcol, $vcol) {
    // Usage $pdo = new DB;
    // $val = $pdo->key_update($table, $columns, $values, $where_col, $where_value);

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
    $query = "UPDATE $table SET $set_statement WHERE BINARY $wcol='$vcol';";

    // Uncomment for curiosity
    //echo "\$query = <code>$query</code><br>";

    // Try the query
    try {
      $statement = $database->query($query);
    } catch (PDOException $error) {
      $this->pdo_error($query, $error->getMessage());
    }

    // Success statement
    $this->change = ($statement->rowCount() > 0) ? true : false;
    $this->ok = ($statement) ? true : false;

    // Return fetched SQL response object
    return $statement->fetch();

  } // key_update()

  //// "try" methods for complex queries

  // try_insert method for complex queries
  public function try_insert($query) {
    // Usage $pdo = new DB;

    global $database;

    // Try the query
    try {
      $statement = $database->query($query);
    } catch (PDOException $error) {
      $this->pdo_error($query, $error->getMessage());
    }

    // Success statements
    $this->change = ($statement->rowCount() == 1) ? true : false;
    $this->lastid = $database->lastInsertId();
    $this->ok = ($statement) ? true : false;

  } // try_insert()

  // try_delete method for complex queries
  public function try_delete($query) {
    global $database;

    // Try the query
    try {
      $statement = $database->prepare($query);
      $statement->execute();
    } catch (PDOException $error) {
      $this->pdo_error($query, $error->getMessage());
    }

    // Success statement
    $this->change = ($statement->rowCount() >= 1) ? true : false;
    $this->ok = ($statement) ? true : false;

  } // try_delete()

  // try_select method for complex queries
  public function try_select($query) {
    global $database;

    // Try the query
    try {
      $statement = $database->query($query);
    } catch (PDOException $error) {
      $this->pdo_error($query, $error->getMessage());
    }

    // Rows
    $this->rows = $statement->rowCount();

    // Return fetched SQL response object
    return $statement->fetch();

  } // try_select()

  // try_update method for complex queries
  public function try_update($query) {
    global $database;

    // Try the query
    try {
      $statement = $database->query($query);
    } catch (PDOException $error) {
      $this->pdo_error($query, $error->getMessage());
    }

    // Success statement
    $this->change = ($statement->rowCount() > 0) ? true : false;
    $this->ok = ($statement) ? true : false;

    // Return fetched SQL response object
    return $statement->fetch();

  } // try_update()

} // class DB

// Instantiate
$pdo = new DB;
