<?php

// Include our config (with SQL) up near the top of our PHP file
include ('./in.db.php');

// Must be logged in
if (!isset($_SESSION['user_id'])) {
  exit (header("Location: blog.php"));
}

if ((isset($_POST['p'])) && (filter_var($_POST['p'], FILTER_VALIDATE_INT))) {
  // Set $piece_id via sanitize non-numbers
  $piece_id = preg_replace("/[^0-9]/"," ", $_POST['p']);

  $queryd = "UPDATE pieces SET status='dead' WHERE id='$piece_id'";
  $calld = mysqli_query($database, $queryd);
  $queryr = "UPDATE publications SET status='dead' WHERE piece_id='$piece_id'";
  $callr = mysqli_query($database, $queryr);
  if (($calld) && ($callr)) {
    exit (header("Location: pieces.php"));
  } else {
    echo '<pre>Major database error!</pre>';
  }

} elseif ((isset($_POST['b'])) && (filter_var($_POST['b'], FILTER_VALIDATE_INT))) { // Bulk?
  $bulk =  true;

} else {
  exit (header("Location: blog.php"));
}

?>
