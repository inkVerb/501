<?php
// Include our config (with SQL) up near the top of our PHP file
include ('./in.config.php');

// Must be logged in
if (!isset($_SESSION['user_id'])) {
  exit(header("Location: blog.php"));
}

// Include our pieces functions
include ('./in.metaeditfunctions.php');


if ( ($_SERVER['REQUEST_METHOD'] === 'POST') && (isset($_POST['p'])) && (isset($_POST['action'])) ) {

  // Validate & filter
  if (!filter_var($_POST['p'], FILTER_VALIDATE_INT)) {exit();}
  $piece_id = $_POST['p'];
  $action = filter_var($_POST['action'], FILTER_SANITIZE_STRING); // Remove any HTML tags

  // Run the action
  piecesaction($action, $piece_id);

  // Get the new info for the piece & rebuild the table row
  $query = "SELECT id, type, status, pub_yn, title, date_live, date_created FROM pieces WHERE id='$piece_id'";
  $call = mysqli_query($database, $query);
  // Start our row colors
  //$table_row_color = 'renew';
  $row = mysqli_fetch_array($call, MYSQLI_NUM);
    // Assign the values
    $p_id = "$row[0]";
    $p_type = "$row[1]";
    $p_status = "$row[2]";
    $p_pub_yn = $row[3]; // This is boolean (true/false), we want to avoid "quotes" as that implies a string
    $p_title = "$row[4]";
    $p_date_live = "$row[5]";
    $p_date_created = "$row[6]";

    // Determine the published status based on pieces.pup_yn and the publications.pubstatus
    // This does not affect dead pieces that will AJAX back, which would remain dead anyway
    if (($p_pub_yn == true) && ($p_status == 'live')) {
      $query_pub = "SELECT status, pubstatus FROM publications WHERE piece_id='$p_id'";
      $call_pub = mysqli_query($database, $query_pub);
      $row_pub = mysqli_fetch_array($call_pub, MYSQLI_NUM);
        // Update the $p_status
        $p_status = ("$row_pub[0]" == 'live') ? "$row_pub[1]" : "$row_pub[0]";
    } elseif (($p_pub_yn == false) && ($p_status == 'live')) {
      $p_status = 'pre-draft';
    }

    // Dead or live?
    // We want this because we will AJAX changes in the future to allow class="pieces_dead" to show before a page reload
    if ($p_status == 'dead') {
      $status_class = 'pieces_dead';
      $show_status = '<i class="gray">trashed</i>';
    } else {
      $status_class = 'pieces_live';
      $show_status = $p_status;
    } // $status_class will have no effect as of now, but we are keeping it as a workflow placeholder for the future

    // Date
    if ($p_date_live == NULL) {
      $p_date_note = '<span class="date">'."Started: $p_date_created".'</span>';
    } else {
      $p_date_note = '<span class="date">'."Live: $p_date_live".'</span>';
    }

    // Display the info in a <table>
    // Start our HTML table
    echo '<tr class="'."$status_class".'" id="prow_'.$p_id.'">'; // This won't matter, but it is here for reference

    // Title
    echo '<td onmouseover="showViews('.$p_id.')" onmouseout="showViews('.$p_id.')">
    <b><a class="piece_title" href="edit.php?p='.$p_id.'">'.$p_title.'</a></b><br>
    <label for="bulk_'.$p_id.'"><input form="bulk_actions" type="checkbox" id="bulk_'.$p_id.'" name="bulk_'.$p_id.'" value="'.$p_id.'"> '.$p_date_note.'</label>
    <div id="showviews'.$p_id.'" style="display: none;">
    <a style="float: none;" href="edit.php?p='.$p_id.'">edit</a>
    <a style="float: right;" class="orange" href="piece.php?p='.$p_id.'&preview">preview</a>
    </div>
    </td>';

    // Status
    echo '<td onmouseover="showActions('.$p_id.')" onmouseout="showActions('.$p_id.')">'
    .$show_status.' <code class="renew" onclick="clearChanged('.$p_id.')" style="float: right; cursor: pointer;" id="changed_'.$p_id.'">changed</code><br><div id="showaction'.$p_id.'" style="display: none;">';
    if ($p_status == 'dead') { // We want this because we will AJAX changes in the future to allow class="pieces_dead" to show before a page reload, we want this as a logical placeholder, but this actually does nothing
      echo metaeditform('undelete', $p_id).'</div>';
    } elseif ($p_status == 'published') {
      echo metaeditform('unpublish', $p_id).' <a class="purple" href="hist.php?p='.$p_id.'">history</a>&nbsp;&nbsp;<a class="green" href="piece.php?p='.$p_id.'">view</a> '.metaeditform('delete', $p_id).'</div>';
    } elseif ($p_status == 'redrafting') {
      echo metaeditform('republish', $p_id).' <a class="purple" href="hist.php?p='.$p_id.'">history</a> '.metaeditform('delete', $p_id).'</div>';
    } elseif ($p_status == 'pre-draft') {
      echo metaeditform('delete', $p_id).'</div>';
    }

    echo '</td>';

    // Type
    echo '<td onmouseover="showTypify('.$p_id.')" onmouseout="showTypify('.$p_id.')">'
    .$p_type.'<br><div id="showtypify'.$p_id.'" style="display: none;">';
    if ($p_type == 'page') {
      echo metaeditform('make post', $p_id).'</div>';
    } else {
      echo metaeditform('make page', $p_id).'</div>';
    }

    echo '</td>';

    // Finish piece
    echo '</tr>';

}
?>
