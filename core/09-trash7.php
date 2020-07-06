<?php

// Include our config (with SQL) up near the top of our PHP file
include ('./in.config.php');

// Include our login cluster
$head_title = "Trash"; // Set a <title> name used next
$edit_page_yn = false; // Include JavaScript for TinyMCE?
include ('./in.login_check.php');

// Include our pieces functions
include ('./in.piecesfunctions.php');

// Trash link
echo '<a class="blue" href="pieces.php">Back to Pieces</a> | <span class="red" style="cursor: pointer;" onclick="showPurgeAll()">Purge all trash &rarr;</span> <a class="red" id="purge_all_trash" href="purge_all_trash.php" style="display:none"><i>Yes! Purge all trash</i></a>';

// Double-check for "Purge all trash"
?>
<script>
function showPurgeAll() {
  var x = document.getElementById("purge_all_trash");
  if (x.style.display === "inline") {
    x.style.display = "none";
  } else {
    x.style.display = "inline";
  }
}
</script>
<?php

// Simple line
echo '<br><hr><br>';

// Bulk actions
echo '<div onclick="showBulkActions()" style="cursor: pointer; display: inline;"><b>Bulk actions &#9660;</b></div><br>
<div id="bulk_actions_div" style="display: none;">
<form id="bulk_actions" method="post" action="act.bulkpieces.php">
  <table>
    <tr>
      <td><b><input type="submit" class="orange" name="bluksubmit" value="restore"></b></td>
      <td><b><input type="submit" class="red" name="bluksubmit" value="purge"></b></td>
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
      <th width="40%">Action</th>
      <th width="20%">Type</th>
    </tr>
';

// Get and display each piece
$query = "SELECT id, type, title, date_live, date_created FROM pieces WHERE status='dead'";
$call = mysqli_query($database, $query);
// Start our row colors
$table_row_color = 'blues';
// We have many entries, this will iterate one post per each
while ($row = mysqli_fetch_array($call, MYSQLI_NUM)) {
  // Assign the values
  $p_id = "$row[0]";
  $p_type = "$row[1]";
  $p_title = "$row[2]";
  $p_date_live = "$row[3]";
  $p_date_created = "$row[4]";

  // Dead or live?
  $status_class = 'pieces_dead';

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
  <b>'.$p_title.'</b><br>
  <label for="bulk_'.$p_id.'"><input form="bulk_actions" type="checkbox" id="bulk_'.$p_id.'" name="bulk_'.$p_id.'" value="'.$p_id.'"> '.$p_date_note.'</label>
  <div id="showviews'.$p_id.'" style="display: none;">
  <a style="float: none;" href="edit.php?p='.$p_id.'">edit</a>
  <a style="float: right;" class="orange" href="piece.php?p='.$p_id.'&preview">preview</a>
  </div></td>';

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

  // Actions
  echo '<td onmouseover="showActions'.$p_id.'()" onmouseout="showActions'.$p_id.'()">
    ready to delete<br><div id="showaction'.$p_id.'" style="display: none;">'
    .piecesform('restore', $p_id).piecesform('purge', $p_id).
  '</div>';

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
  echo '<td>'.$p_type.'<br></td>';

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
