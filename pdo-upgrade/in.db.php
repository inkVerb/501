<?php

// Start our $_SESSION
session_start();

// In case you want to show errors
// ini_set('display_errors', '1');
// ini_set('display_startup_errors', '1');
// error_reporting(E_ALL);

// MySQLi config
require_once ('./in.conf.php');

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
  public $ok;
  public $numrows;
  // Usage $pdo = new DB;
  // $val = ($pdo->change) ? true : false; // delete() update()
  // $val = ($pdo->ok) ? true : false; // all
  // $val = $pdo->lastid; // Any exec_() query with INSERT
  // $val = $pdo->numrows; // select()

  // trimspace method (not sufficient to prevent SQL injection, nor escape all SQL data)
  static function trimspace($data) {
    // Usage $pdo = new DB;
    // $val = DB::trimspace($string);

    $trimmed_data = trim(preg_replace('/\s+/', ' ', $data));
    return $trimmed_data;
  } // trimspace()

  // PDO error handler
  protected function pdo_error($query, $error_message) {
    echo "SQL error from <pre></pre><br>$error_message";
    exit ();
  } // pdo_error()

  // DELETE method
  public function delete($table, $col, $val) {
    // Usage $pdo = new DB;
    // $pdo->delete($table, $column, $value);

    global $database;

    // Sanitize
    $cols = preg_replace("/[^0-9a-zA-Z_]/", "", $col);
    $table = preg_replace("/[^0-9a-zA-Z_]/", "", $table);

    // Try the query
    $query = $database->prepare("DELETE FROM $table WHERE $col=:val");
    $query->bindParam(':val', $val);

    // Try the query
    try {
      $query->execute();
    } catch (PDOException $error) {
      $this->pdo_error($query, $error->getMessage());
    }

    // Success statement
    $this->change = ($statement->rowCount() >= 1) ? true : false;
    $this->ok = ($statement) ? true : false;

  } // delete()

  // SELECT method
  public function select($table, $wcol, $vcol, $cols='*') {
    // Usage $pdo = new DB;
    // $rows = $pdo->select($table, $where_col, $where_value, $columns='*');
    // foreach ($rows as $row) ...
    // Then, column names are objects: $fruit = $row->fruit; $rock = $row->rock;

    global $database;

    // Sanitize
    $cols = preg_replace("/[^0-9a-zA-Z_, ]/", "", $cols);
    $table = preg_replace("/[^0-9a-zA-Z_]/", "", $table);
    $wcol = preg_replace("/[^0-9a-zA-Z_]/", "", $wcol);

    // Prepare SQL query
    $query = $database->prepare("SELECT $cols FROM $table WHERE $wcol=:vcol");
    $query->bindParam(':vcol', $vcol);

    // Try the query
    try {
      $query->execute();
    } catch (PDOException $error) {
      $this->pdo_error($query, $error->getMessage());
    }

    // Response info
    $this->numrows = $query->rowCount();
    $this->ok = ($query) ? true : false;

    // Return fetched SQL response object
    return $query->fetchAll();

  } // select()

  // SELECT WHERE BINARY method for keys
  public function key_select($table, $wcol, $vcol, $cols='*') {
    // Usage $pdo = new DB;
    // $val = $pdo->key_select($table, $where_col, $where_value, $columns='*');
    // Then, column names are objects: $fruit = $val->fruit; $rock = $val->rock;

    global $database;

    // Sanitize
    $cols = preg_replace("/[^0-9a-zA-Z_, ]/", "", $cols);
    $table = preg_replace("/[^0-9a-zA-Z_]/", "", $table);
    $wcol = preg_replace("/[^0-9a-zA-Z_]/", "", $wcol);

    // Prepare SQL query
    $query = $database->prepare("SELECT $cols FROM $table WHERE BINARY $wcol=:vcol");
    $query->bindParam(':vcol', $vcol);

    // Try the query
    try {
      $query->execute();
    } catch (PDOException $error) {
      $this->pdo_error($query, $error->getMessage());
    }

    // Response info
    $this->numrows = $query->rowCount();
    $this->ok = ($query) ? true : false;

    // Return fetched SQL response object
    return $query->fetchAll();

  } // key_select()

  // UPDATE method
  public function update($table, $cols, $vals, $wcol, $vcol) {
    // Usage $pdo = new DB;
    // $val = $pdo->update($table, $columns, $values, $where_col, $where_value);

    global $database;

    // Sanitize
    $cols = preg_replace("/[^0-9a-zA-Z_, ]/", "", $cols);
    $table = preg_replace("/[^0-9a-zA-Z_]/", "", $table);

    // Prepare array of $cols=key & $vals=value
    $cols_arr = preg_split('~,\s*~', $cols);
    $vals_arr = preg_split('~,\s*~', $vals);
    $set_array = array_combine($cols_arr, $vals_arr);
    $bind_array = array();

    // Prepare SQL SET statement
    $set_statement = "";
    foreach ( $set_array as $k => $v ) {
      $set_statement .= "$k=:$k,";
    }
    $set_statement = rtrim($set_statement, ','); // remove last comma

    // Prepare SQL query
    $query = $database->prepare("UPDATE $table SET $set_statement WHERE $wcol='$vcol'");

    // Bind values
    foreach ( $set_array as $k => $v ) {
      $query->bindParam(":$k", $v);
    }

    // Try the query
    try {
      $query->execute();
    } catch (PDOException $error) {
      $this->pdo_error($query, $error->getMessage());
    }

    // Success statement
    $this->change = ($statement->rowCount() > 0) ? true : false;
    $this->ok = ($statement) ? true : false;

    // Return fetched SQL response object
    return $query->fetchAll();

  } // update()

  // UPDATE  WHERE BINARY method for keys
  public function key_update($table, $cols, $vals, $wcol, $vcol) {
    // Usage $pdo = new DB;
    // $val = $pdo->key_update($table, $columns, $values, $where_col, $where_value);

    global $database;

    // Sanitize
    $cols = preg_replace("/[^0-9a-zA-Z_, ]/", "", $cols);
    $table = preg_replace("/[^0-9a-zA-Z_]/", "", $table);

    // Prepare array of $cols=key & $vals=value
    $cols_arr = preg_split('~,\s*~', $cols);
    $vals_arr = preg_split('~,\s*~', $vals);
    $set_array = array_combine($cols_arr, $vals_arr);
    $bind_array = array();

    // Prepare SQL SET statement
    $set_statement = "";
    foreach ( $set_array as $k => $v ) {
      $set_statement .= "$k=:$k,";
    }
    $set_statement = rtrim($set_statement, ','); // remove last comma

    // Prepare SQL query
    $query = $database->prepare("UPDATE $table SET $set_statement WHERE BINARY $wcol='$vcol'");

    // Bind values
    foreach ( $set_array as $k => $v ) {
      $query->bindParam(":$k", $v);
    }

    // Try the query
    try {
      $query->execute();
    } catch (PDOException $error) {
      $this->pdo_error($query, $error->getMessage());
    }

    // Success statement
    $this->change = ($query->rowCount() > 0) ? true : false;
    $this->ok = ($query) ? true : false;

    // Return fetched SQL response object
    return $query->fetchAll();

  } // key_update()

  // exec_ static method to pass properties through a try test
  public function exec_($query) {
    // Usage $pdo = new DB;
    // $query = $database->prepare($sql_statement);
    // $query->bindParam(...);
    // $rows = $pdo->exec_($query);
    // if ($pdo->numrows) { foreach ($rows as $row) {$p_type = "$row->type";} }
    // if ($pdo->$numrows > 0) {do something}

    global $database;

    // Try the query
    try {
      $query->execute();
    } catch (PDOException $error) {
      $this->pdo_error($query, $error->getMessage());
    }

    // Response info
    $this->numrows = $query->rowCount();
    $this->change = ($query->rowCount() > 0) ? true : false;
    $this->lastid = $database->lastInsertId();
    $this->ok = ($query) ? true : false;

    // Return fetched SQL response object
    return $query->fetchAll();

  } // exec_()

} // class DB

// Instantiate
$pdo = new DB;

// Retrieve Blog Settings
$query = $database->prepare("SELECT public, title, tagline, description, keywords, summary_words, piece_items, feed_items, crawler_index FROM blog_settings");
$rows = $pdo->exec_($query);
foreach ($rows as $row) {
  $blog_public = "$row->public";
  $blog_title = "$row->title";
  $blog_tagline = "$row->tagline";
  $blog_description = "$row->description";
  $blog_keywords = "$row->keywords";
  $blog_summary_words = "$row->summary_words";
  $blog_piece_items = "$row->piece_items";
  $blog_feed_items = "$row->feed_items";
  $blog_crawler_index = "$row->crawler_index";
}

// Set a default Series from the blog_settings table
$query = $database->prepare("SELECT default_series FROM blog_settings");
$rows = $pdo->exec_($query);
if ($pdo->numrows == 1) {
  foreach ($rows as $row) {
    $blog_default_series = $row->default_series;
  }
}
