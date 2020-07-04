<?php

// Include our config (with SQL) up near the top of our PHP file
include ('./in.config.php');

// Include our login cluster
$head_title = "Pieces"; // Set a <title> name used next
$edit_page_yn = false; // Include JavaScript for TinyMCE?
include ('./in.login_check.php');

// Include our pieces functions
include ('./in.piecesfunctions.php');

// Trash link
echo '<a class="red" href="trash.php">View trash</a>';

// Simple line
echo '<br><hr><br>';

// Bulk actions
echo '<div onclick="showBulkActions()" style="cursor: pointer; display: inline;"><b>Bulk actions &#9660;</b></div><br>
<div id="bulk_actions_div" style="display: none;">
<form id="bulk_actions" method="post" action="act.bulkpieces.php">
  <table>
    <tr>
      <td><b><input type="submit" class="green" name="bluksubmit" value="republish"></b></td>
      <td><b><input type="submit" class="orange" name="bluksubmit" value="unpublish"></b></td>
      <td><b><input type="submit" class="blue" name="bluksubmit" value="make post"></b></td>
      <td><b><input type="submit" class="blue" name="bluksubmit" value="make page"></b></td>
      <td><b><input type="submit" class="orange" name="bluksubmit" value="undelete"></b></td>
      <td><b><input type="submit" class="red" name="bluksubmit" value="delete"></b></td>
    </tr>
  </table>
</form>
<label><input type="checkbox" onclick="toggle(this);" /> <b>Select all</b></label>
</div>';

// JavaScript to show/hide Bulk Actions
?>
<script>
function showBulkActions() {
  var x = document.getElementById("bulk_actions_div");
  if (x.style.display === "block") {
    x.style.display = "none";
  } else {
    x.style.display = "block";
  }
}
</script>
<?php
// JavaScript to "Select all"
?>
<script>
function toggle(source) {
    var checkboxes = document.querySelectorAll('input[type="checkbox"]');
    for (var i = 0; i < checkboxes.length; i++) {
        if (checkboxes[i] != source)
            checkboxes[i].checked = source.checked;
    }
}
</script>
<?php

// Start our HTML table
echo '
<table class="contentlib">
  <tbody>
    <tr>
    <th width="40%">Title</th>
    <th width="40%">Status</th>
    <th width="20%">Type</th>
    </tr>
';

// Get and display each piece
$query = "SELECT id, type, status, pub_yn, title, date_live, date_created FROM pieces WHERE status='live'";
$call = mysqli_query($database, $query);
// Start our row colors
$table_row_color = 'blues';
// We have many entries, this will iterate one post per each
while ($row = mysqli_fetch_array($call, MYSQLI_NUM)) {
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
    $query_pub = "SELECT status, pubstatus FROM publications WHERE status='live' AND piece_id='$p_id'";
    $call_pub = mysqli_query($database, $query_pub);
    $row_pub = mysqli_fetch_array($call_pub, MYSQLI_NUM);
      // Update the $p_status
      $p_status = ("$row_pub[0]" == 'live') ? "$row_pub[1]" : "$row_pub[0]";
  } elseif (($p_pub_yn == false) && ($p_status == 'live')) {
    $p_status = 'pre-draft';
  }

  // Status
  $status_class = 'pieces_live';

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
  <b><a class="piece_title" href="edit.php?p='.$p_id.'">'.$p_title.'</a></b><br>
  <label for="bulk_'.$p_id.'"><input form="bulk_actions" type="checkbox" id="bulk_'.$p_id.'" name="bulk_'.$p_id.'" value="'.$p_id.'"> '.$p_date_note.'</label>
  <div id="showviews'.$p_id.'" style="display: none;">
  <a style="float: none;" href="edit.php?p='.$p_id.'">edit</a>
  <a style="float: right;" class="orange" href="piece.php?p='.$p_id.'&preview">preview draft</a>
  </div>';

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
  .$show_status.'<br><div id="showaction'.$p_id.'" style="display: none;">';
  if ($p_status == 'dead') { // We want this because we will AJAX changes in the future to allow class="pieces_dead" to show before a page reload, we want this as a logical placeholder, but this actually does nothing
    echo piecesform('undelete', $p_id).'</div>';
  } elseif ($p_status == 'published') {
    echo piecesform('unpublish', $p_id).' <a class="purple" href="hist.php?p='.$p_id.'">history</a>&nbsp;&nbsp;<a class="green" href="piece.php?p='.$p_id.'">view</a> '.piecesform('delete', $p_id).'</div>';
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
  // JavaScript to clear "changed" status
  ?>
  <script>
  function clearChanged<?php echo $p_id; ?>() {
    document.getElementById("prow_<?php echo $p_id; ?>").classList.remove("renew"); // Remove the .renew class from the <tr> added by AJAX
    document.getElementById("changed_<?php echo $p_id; ?>").remove(); // Remove the "changed" clickable message added by AJAX
    showActions<?php echo $p_id; ?>(); // We need our toggles right
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

  // Toggle our row colors
  $table_row_color = ($table_row_color == 'blues') ? 'shady' : 'blues';

}

echo "
  </tbody>
</table>
";

// Simple line
echo '<br><hr><br>';

// Footer
include ('./in.footer.php');
