<?php
// No <head> yet because we might redirect, which uses header() and might break after the <head> tag

// Include our config (with SQL) up near the top of our PHP file
include ('./in.config.php');

// Must be logged in
if (!isset($_SESSION['user_id'])) {
  exit(header("Location: blog.php"));
}

if ((isset($_GET['p'])) && (filter_var($_GET['p'], FILTER_VALIDATE_INT))) {
  // Set $piece_id via sanitize non-numbers
  $piece_id = preg_replace("/[^0-9]/"," ", $_GET['p']);
  
} else {
  exit(header("Location: blog.php"));
}

$query = "UPDATE pieces SET status='dead' WHERE id='$piece_id'";
$call = mysqli_query($database, $query);
if ($call) {
  exit(header("Location: pieces.php"));
} else {
  echo '<pre>Major database error!</pre>';
}
