<?php

// Include our config (with SQL) up near the top of our PHP file
include ('./in.config.php');

// Include our login cluster
$head_title = "Pieces"; // Set a <title> name used next
$edit_page_yn = false; // Include JavaScript for TinyMCE?
include ('./in.login_check.php');

// Include our pieces functions
include ('./in.metaeditfunctions.php');

// JavaScript
?>
<script>

// show/hide view links in Title
function showViews(p_id) {
  var x = document.getElementById("showviews"+p_id);
  if (x.style.display === "inline") {
    x.style.display = "none";
  } else {
    x.style.display = "inline";
  }
}

// show/hide action links in Status
function showActions(p_id) {
  var x = document.getElementById("showaction"+p_id);
  if (x.style.display === "inline") {
    x.style.display = "none";
  } else {
    x.style.display = "inline";
  }
}

// show/hide action link in Type
function showTypify(p_id) {
  var x = document.getElementById("showtypify"+p_id);
  if (x.style.display === "inline") {
    x.style.display = "none";
  } else {
    x.style.display = "inline";
  }
}
</script>
<?php

// Trash link
echo '<a class="red" href="trash.php">View trash</a>';

// Simple line
echo '<br><hr><br>';

// Start our HTML table
echo '
<table class="contentlib" id="pieces-table">
  <tbody>
    <tr>
    <th width="53%">Title</th>
    <th width="32%">Status</th>
    <th width="15%">Type</th>
    </tr>
';

// Get and display each piece
$query = "SELECT id, type, status, pub_yn, title, date_live, date_created FROM pieces";
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
  // (If pieces.status is 'dead', none of this will happen and $p_status will remain 'dead')
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
  echo '<tr class="'."$table_row_color $status_class".'">';

  // Title
  echo '<td onmouseover="showViews('.$p_id.')" onmouseout="showViews('.$p_id.')">
  <b><a class="piece_title" href="edit.php?p='.$p_id.'">'.$p_title.'</a></b><br>'.$p_date_note.'
  <div id="showviews'.$p_id.'" style="display: none;">
  <a class="orange" style="float: none;" href="edit.php?p='.$p_id.'">edit</a>';

  echo '</td>';

  // Status
  echo '<td onmouseover="showActions('.$p_id.')" onmouseout="showActions('.$p_id.')">'
  .$p_status.'<br><div id="showaction'.$p_id.'" style="display: none;">';
  if ($p_status == 'dead') {
    echo metaeditform('undelete', $p_id).metaeditform('delete forever', $p_id).'</div>';
  } elseif ($p_status == 'published') {
    echo metaeditform('unpublish', $p_id).'&nbsp;&nbsp;<a class="green" href="piece.php?p='.$p_id.'">view</a>'.metaeditform('delete', $p_id).'</div>';
  } elseif ($p_status == 'redrafting') {
    echo metaeditform('republish', $p_id).metaeditform('delete', $p_id).'</div>';
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
