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

  // Get the new info for the piece & rebuild the table row
  $query = "SELECT id, type, status, pub_yn, title, date_live, date_created FROM pieces WHERE id='$piece_id'";
  $call = mysqli_query($database, $query);
  // Start our row colors
  $table_row_color = 'renew';
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
      $query_pub = "SELECT pubstatus FROM publications WHERE piece_id='$p_id'";
      $call_pub = mysqli_query($database, $query_pub);
      $row_pub = mysqli_fetch_array($call_pub, MYSQLI_NUM);
        // Update the $p_status
        $p_status = "$row_pub[0]";
    } elseif (($p_pub_yn == false) && ($p_status == 'live')) {
      $p_status = 'pre-draft';
    }

    // Dead or live?
    // We want this because we will AJAX changes in the future to allow class="pieces_dead" to show before a page reload
    if ($p_status == 'dead') {
      $status_class = 'pieces_dead';
    } else {
      $status_class = 'pieces_live';
    }

    // Date
    if ($p_date_live == NULL) {
      $p_date_note = '<span class="date">'."Started: $p_date_created".'</span>';
    } else {
      $p_date_note = '<span class="date">'."Live: $p_date_live".'</span>';
    }

    // Display the info in a <table>
    // Start our HTML table
    echo '<tr class="'."$table_row_color $status_class".'" id="prow_'.$p_id.'">';

    // Title
    echo '<td onmouseover="showViews'.$p_id.'()" onmouseout="showViews'.$p_id.'()">
    <b><a class="piece_title" href="edit.php?p='.$p_id.'">'.$p_title.' &#9660;</a></b><br>
    <label for="bulk_'.$p_id.'"><input form="bulk_actions" type="checkbox" id="bulk_'.$p_id.'" name="bulk_'.$p_id.'" value="'.$p_id.'"> '.$p_date_note.'</label>
    <div id="showviews'.$p_id.'" style="display: none;">
    <a style ="float: none;" href="edit.php?p='.$p_id.'">edit</a>';

    // No "view" for non-published pieces
    if (($status_class == 'pieces_live') && ($p_status == 'published')) {
      echo '<a style="float: right;" class="orange" href="piece.php?p='.$p_id.'&preview">preview draft</a> | <a class="green" href="piece.php?p='.$p_id.'">view</a>';
    } else {
      echo '<a style="float: right;" class="orange" href="piece.php?p='.$p_id.'&preview">preview draft</a>';
    }

    echo '</div>';

    // JavaScript with unique function name per row, show/hide action links
    ?>
    <script>
    function showViews<?php echo $p_id; ?>() {
      var x = document.getElementById("showviews<?php echo $p_id; ?>");
      if (x.style.display === "inline") {
        x.style.display = "none";
      } else {
        x.style.display = "inline";
      }
    }
    </script>
    <?php

    echo '</td>';

    // Status
    echo '<td onmouseover="showActions'.$p_id.'()" onmouseout="showActions'.$p_id.'()">'
    .$p_status.' <i class="renew" style ="float: right;">changed</i><br><div id="showaction'.$p_id.'" style="display: none;">';
    if ($p_status == 'dead') { // We want this because we will AJAX changes in the future to allow class="pieces_dead" to show before a page reload, we want this as a logical placeholder, but this actually does nothing
      echo piecesform('undelete', $p_id).'</div>';
    } elseif ($p_status == 'published') {
      echo piecesform('unpublish', $p_id).' <a class="purple" href="hist.php?p='.$p_id.'">history</a> '.piecesform('delete', $p_id).'</div>';
    } elseif ($p_status == 'redrafting') {
      echo piecesform('republish', $p_id).' <a class="purple" href="hist.php?p='.$p_id.'">history</a> '.piecesform('delete', $p_id).'</div>';
    } elseif ($p_status == 'pre-draft') {
      echo piecesform('delete', $p_id).'</div>';
    }

    // JavaScript with unique function name per row, show/hide action links
    ?>
    <script>
    function showActions<?php echo $p_id; ?>() {
      var x = document.getElementById("showaction<?php echo $p_id; ?>");
      if (x.style.display === "inline") {
        x.style.display = "none";
      } else {
        x.style.display = "inline";
      }
    }
    </script>
    <?php

    echo '</td>';

    // Type
    echo '<td onmouseover="showTypify'.$p_id.'()" onmouseout="showTypify'.$p_id.'()">'
    .$p_type.'<br><div id="showtypify'.$p_id.'" style="display: none;">';
    if ($p_type == 'page') {
      echo piecesform('make post', $p_id).'</div>';
    } else {
      echo piecesform('make page', $p_id).'</div>';
    }

    // JavaScript with unique function name per row, show/hide action links
    ?>
    <script>
    function showTypify<?php echo $p_id; ?>() {
      var x = document.getElementById("showtypify<?php echo $p_id; ?>");
      if (x.style.display === "inline") {
        x.style.display = "none";
      } else {
        x.style.display = "inline";
      }
    }
    </script>
    <?php

    echo '</td>';

    // Finish piece
    echo '</tr>';

}
?>
