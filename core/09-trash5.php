<?php
// No <head> yet because we might redirect, which uses header() and might break after the <head> tag

// Include our config (with SQL) up near the top of our PHP file
include ('./in.config.php');

// Include our post functions
include ('./in.postfunctions.php');

// Include our login cluster
$head_title = "Trash"; // Set a <title> name used next
$edit_page_yn = false; // Include JavaScript for TinyMCE?
include ('./in.login_check.php');

// Trash link
echo '<a class="blue" href="pieces.php">Back to Pieces</a> | <span class="red" style="cursor: pointer;" onclick="showEmptyAll()">Empty all trash &rarr;</span> <a class="red" id="empty_all_trash" href="empty_all_trash.php" style="display:none"><i>Yes! Empty all trash</i></a>';

// Double-check for "Empty all trash"
?>
<script>
function showEmptyAll() {
  var x = document.getElementById("empty_all_trash");
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
// Start our show_div counter
$show_div_count = 1;
// We have many entries, this will iterate one post per each
while ($row = mysqli_fetch_array($call, MYSQLI_NUM)) {
  // Assign the values
  $p_id = "$row[0]";
  $p_type = "$row[1]";
  $p_title = "$row[2]";
  $p_date_live = "$row[3]";
  $p_date_created = "$row[4]";

  // Date
  if ($p_date_live == NULL) {
    $p_date_note = '<span class="date">'."Started: $p_date_created".'</span>';
  } else {
    $p_date_note = '<span class="date">'."Live: $p_date_live".'</span>';
  }

  // Display the info in a <table>
  // Start our HTML table
  echo "<tr class=\"$table_row_color\">";

  // Title
  echo '<td><b>'.$p_title.'</b><br>
      '.$p_date_note.'</td>';

  // Actions
  echo '<td onmouseover="showActions'.$show_div_count.'()" onmouseout="showActions'.$show_div_count.'()">
    ready to delete<br><div id="showaction'.$show_div_count.'" style="display: none;">'
    .postform('restore', $p_id).postform('permanently delete', $p_id).
  '</div>';

  // JavaScript with unique function name per row, show/hide action links
  ?>
  <script>
  function showActions<?php echo $show_div_count; ?>() {
    var x = document.getElementById("showaction<?php echo $show_div_count; ?>");
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
  // Increment our show div counter
  ++$show_div_count;

}

echo "
  </tbody>
</table>
";

// Simple line
echo '<br><hr><br>';

// Footer
include ('./in.footer.php');
