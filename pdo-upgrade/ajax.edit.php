<?php

// Include our config (with SQL) up near the top of our PHP file
include ('./in.db.php');
include ('./in.logincheck.php');

// Include our piece functions
include ('./in.piecefunctions.php');

// Proper POST?
if ( ($_SERVER['REQUEST_METHOD'] === 'POST') && (isset($_SESSION['user_id'])) ) {

  // AJAX token check
  if ( $_POST['ajax_token'] !== $_SESSION['ajax_token'] ) {
    exit();
  }

  // Include our POST processor
  include ('./in.editprocess.php');

} else {
  exit ();
}
