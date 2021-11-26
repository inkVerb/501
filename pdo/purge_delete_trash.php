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

  $query = $database->prepare("DELETE FROM pieces WHERE status='dead' AND id=:id");
  $query->bindParam(':id', $p_id);
  $pdo->exec_($query);
  $call1 = $pdo->ok;
  if ($call1) {
    $query = $database->prepare("DELETE FROM publications WHERE status='dead' AND piece_id=:piece_id");
    $query->bindParam(':piece_id', $p_id);
    $pdo->exec_($query);
    $call2 = $pdo->ok;
  }
  if ($call2) {
    $pdo->delete('publication_history', 'piece_id', $p_id);
    $call3 = $pdo->ok;
  }
  if ($call3) {
    exit (header("Location: trash.php"));
  } else {
    echo '<pre>Major database error!</pre>';
  }

} elseif ((isset($_POST['b'])) && (filter_var($_POST['b'], FILTER_VALIDATE_INT))) { // Bulk?
  $bulk =  true;

} else {
  exit (header("Location: blog.php"));
}

?>
