<?php
// Include our config (with SQL) up near the top of our PHP file
include ('./in.db.php');
include ('./in.logincheck.php');

// Must be logged in
if (!isset($_SESSION['user_id'])) {
  exit (header("Location: blog.php"));
}

// Include our pieces functions
include ('./in.metaeditfunctions.php');


if ( ($_SERVER['REQUEST_METHOD'] === 'POST') && (isset($_POST['p'])) && (isset($_POST['action'])) ) {

  // Validate & filter
  if (!filter_var($_POST['p'], FILTER_VALIDATE_INT)) {exit ();}
  $piece_id = $_POST['p'];
  $action = filter_var($_POST['action'], FILTER_SANITIZE_STRING); // Remove any HTML tags

  // Run the action
  piecesaction($action, $piece_id);

  // In the SQL query, we will not render, we will only check for proper status
  // Get the new info for the piece & rebuild the table row
  $rows = $pdo->select('pieces', 'id', $piece_id, 'id, type, status, pub_yn');
  foreach ($rows as $row) {
    // Start our row colors
    //$table_row_color = 'renew';
    // Assign the values
    $p_id = "$row->id";
    $p_type = "$row->type";
    $p_status = "$row->status";
    $p_pub_yn = $row->pub_yn; // This is boolean (true/false), we want to avoid "quotes" as that implies a string
  }
  // Determine the published status based on pieces.pup_yn and the publications.pubstatus
  // This does not affect dead pieces that will AJAX back, which would remain dead anyway
  if (($p_pub_yn == true) && ($p_status == 'live')) {
    $rows_pub = $pdo->select('publications', 'piece_id', $p_id, 'status, pubstatus');
      // Update the $p_status
      foreach ($rows_pub as $row_pub) {
        $p_status = ("$row_pub->status" == 'live') ? "$row_pub->pubstatus" : "$row_pub->status";
      }
  } elseif (($p_pub_yn == false) && ($p_status == 'live')) {
    $p_status = 'pre-draft';
  }

  // We check both the $action and the status/type to make sure they match, then render what they would on a normal page load
  if ( (($action == 'republish') || ($action == 'undelete')) && ($p_status == 'published') ) {
    echo metaeditform('unpublish', $p_id).' <a class="purple" href="hist.php?p='.$p_id.'">history</a>&nbsp;&nbsp;<a class="green" href="piece.php?p='.$p_id.'">view</a> ';
  } elseif ( (($action == 'unpublish') || ($action == 'undelete')) && ($p_status == 'redrafting') ) {
    echo metaeditform('republish', $p_id).' <a class="purple" href="hist.php?p='.$p_id.'">history</a> ';
  } elseif (($action == 'make page') && ($p_type == 'page')) {
    echo metaeditform('make post', $p_id);
  } elseif (($action == 'make post') && ($p_type == 'post')) {
    echo metaeditform('make page', $p_id);
  } elseif (($action == 'restore') && ($p_status != 'dead')) {
    echo metaeditform('redelete', $p_id);
  } elseif (($action == 'redelete') && ($p_status == 'dead')) {
    echo metaeditform('restore', $p_id);
  }

}
?>
