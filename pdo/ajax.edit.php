<?php

// Include our config (with SQL) up near the top of our PHP file
include ('./in.config.php');
include ('./in.logincheck.php');

// Include our piece functions
include ('./in.piecefunctions.php');

// Proper POST?
if ( ($_SERVER['REQUEST_METHOD'] === 'POST') && (isset($_SESSION['user_id'])) ) {

  // Include our POST processor
  include ('./in.editprocess.php');

} else {
  exit ();
}
