<?php

// Include our config (with SQL) up near the top of our PHP file
include ('./in.config.php');

// Must be logged in
if (!isset($_SESSION['user_id'])) {
  exit(header("Location: blog.php"));
}

if ((isset($_POST['p'])) && (filter_var($_POST['p'], FILTER_VALIDATE_INT))) {
  // Set $piece_id via sanitize non-numbers
  $piece_id = preg_replace("/[^0-9]/"," ", $_POST['p']);

} else {
  exit(header("Location: blog.php"));
}

// Get the IDs for our deleted pieces
$query = "SELECT id FROM pieces WHERE status='dead'";
$call = mysqli_query($database, $query);
// We have many entries, this will iterate one post per each
while ($row = mysqli_fetch_array($call, MYSQLI_NUM)) {
  // Assign the values
  $piece_id = "$row[0]";

  // Delete each item
  $query1 = "DELETE FROM pieces WHERE status='dead' AND id='$piece_id'";
  $call1 = mysqli_query($database, $query1);
  $query2 = "DELETE FROM publications WHERE piece_id='$piece_id'";
  $call2 = mysqli_query($database, $query2);
  $query3 = "DELETE FROM publication_history WHERE piece_id='$piece_id'";
  $call3 = mysqli_query($database, $query3);

  // Group check
  if ((!$call1) || (!$call2) || (!$call3)) {
    echo '<pre>Major database error!</pre>';
    exit();
  }

}

// Mild SQL okay check
if ($call) {
  exit(header("Location: trash.php"));
} else {
  echo '<pre>Major database error!</pre>';
}
