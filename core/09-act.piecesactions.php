<?php
// Include our config (with SQL) up near the top of our PHP file
include ('./in.config.php');

// Must be logged in
if (!isset($_SESSION['user_id'])) {
  exit(header("Location: blog.php"));
}

// Include our pieces functions
include ('./in.piecesfunctions.php');


if ( ($_SERVER['REQUEST_METHOD'] === 'POST') && (isset($_POST['p'])) && (isset($_POST['action'])) ) {

  // Validate & filter
  if (!filter_var($_POST['p'], FILTER_VALIDATE_INT)) {exit();}
  $piece_id = $_POST['p'];
  $action = filter_var($_POST['action'], FILTER_SANITIZE_STRING); // Remove any HTML tags

  // Run the action
  piecesaction($action, $piece_id);

  // Done, go home
  exit(header("Location: pieces.php"));

}
?>
