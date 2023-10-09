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

$query1 = "DELETE FROM pieces WHERE status='dead' AND id='$piece_id'";
$call1 = mysqli_query($database, $query1);
if ($call1) {
  $query2 = "DELETE FROM publications WHERE status='dead' AND piece_id='$piece_id'";
  $call2 = mysqli_query($database, $query2);
}
if ($call2) {
  $query3 = "DELETE FROM publication_history WHERE piece_id='$piece_id'";
  $call3 = mysqli_query($database, $query3);
}
if ($call3) {
  exit (header("Location: pieces.php"));
} else {
  echo '<pre>Major database error!</pre>';
}
