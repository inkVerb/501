<?php

// Include our config (with SQL) up near the top of our PHP file
include ('./in.config.php');

// Must be logged in
if (!isset($_SESSION['user_id'])) {
  exit (header("Location: blog.php"));
}

if ((isset($_GET['p'])) && (filter_var($_GET['p'], FILTER_VALIDATE_INT))) {
  // Set $piece_id via sanitize non-numbers
  $piece_id = preg_replace("/[^0-9]/"," ", $_GET['p']);

} else {
  exit (header("Location: blog.php"));
}

$queryd = "UPDATE pieces SET status='dead' WHERE id='$piece_id'";
$calld = mysqli_query($database, $queryd);
$queryr = "UPDATE publications SET status='dead' WHERE piece_id='$piece_id'";
$callr = mysqli_query($database, $queryr);
if (($calld) && ($callr)) {
  exit (header("Location: pieces.php"));
} else {
  echo '<pre>Major database error!</pre>';
}

?>
