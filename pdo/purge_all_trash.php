<?php

// Include our config (with SQL) up near the top of our PHP file
include ('./in.config.php');

// Must be logged in
if (!isset($_SESSION['user_id'])) {
  exit (header("Location: blog.php"));
}

// Get the IDs for our deleted pieces
$rows = $pdo->select_multi('pieces', 'status', 'dead', 'id');
// We have many entries, this will iterate one post per each
foreach ($rows as $row) {
  // Assign the values
  $piece_id = "$row->id";

  // Delete each item
  $query = $database->prepare("DELETE FROM pieces WHERE status='dead' AND id=:id");
  $query->bindParam(':id', $piece_id);
  $pdo->exec_($query);
  $call1 = $pdo->ok;
  $pdo->delete('publications', 'piece_id', $piece_id);
  $call2 = $pdo->ok;
  $pdo->delete('publication_history', 'piece_id', $piece_id);
  $call3 = $pdo->ok;

  // Group check
  if ((!$call1) || (!$call2) || (!$call3)) {
    echo '<pre>Major database error!</pre>';
    exit ();
  }

}

// Mild SQL okay check
if ($call) {
  exit (header("Location: trash.php"));
} else {
  echo '<pre>Major database error!</pre>';
}
