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

  $query = "UPDATE publications SET pubstatus='redrafting' WHERE piece_id='$piece_id'";
  $call = mysqli_query($database, $query);
  if ($call) {
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
