<?php

// Include our config (with SQL) up near the top of our PHP file
include ('./in.config.php');

// Must be logged in
if (!isset($_SESSION['user_id'])) {
  exit (header("Location: blog.php"));
}

if ((isset($_POST['p'])) && (filter_var($_POST['p'], FILTER_VALIDATE_INT))) {
  // Set $piece_id via sanitize non-numbers
  $piece_id = preg_replace("/[^0-9]/"," ", $_POST['p']);

} else {
  exit (header("Location: blog.php"));
}

$query1 = "UPDATE publications SET type='post' WHERE piece_id='$piece_id'";
$call1 = mysqli_query($database, $query1);
$query2 = "UPDATE pieces SET type='post' WHERE id='$piece_id'";
$call2 = mysqli_query($database, $query2);
if (($call1) && ($call2)) {
  exit (header("Location: pieces.php"));
} else {
  echo '<pre>Major database error!</pre>';
}

?>
