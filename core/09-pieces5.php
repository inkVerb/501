<?php

// Include our config (with SQL) up near the top of our PHP file
include ('./in.config.php');

// Include our post functions
include ('./in.postfunctions.php');

// Include our login cluster
$head_title = "Pieces"; // Set a <title> name used next
$edit_page_yn = false; // Include JavaScript for TinyMCE?
include ('./in.login_check.php');

// Trash link
echo '<a class="red" href="trash.php">View trash</a>';

// Simple line
echo '<br><hr><br>';

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
$query = "SELECT id, type, status, pub_yn, title, date_live, date_created FROM pieces";
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
  $p_status = "$row[2]";
  $p_pub_yn = $row[3]; // This is boolean (true/false), we want to avoid "quotes" as that implies a string
  $p_title = "$row[4]";
  $p_date_live = "$row[5]";
  $p_date_created = "$row[6]";

  // Determine the published status based on pieces.pup_yn and the publications.pubstatus
  // (If pieces.status is 'dead', none of this will happen and $p_status will remain 'dead')
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
  echo '<td><b><a class="piece_title" href="edit.php?p='.$p_id.'">'.$p_title.'</a></b><br>
      '.$p_date_note.'</td>';

  // Status
  echo '<td onmouseover="showActions'.$show_div_count.'()" onmouseout="showActions'.$show_div_count.'()">'
  .$p_status.'<br><div id="showaction'.$show_div_count.'" style="display: none;">';
  if ($p_status == 'dead') {
    echo postform('undelete', $p_id).postform('delete forever', $p_id).'</div>';
  } elseif ($p_status == 'published') {
    echo postform('unpublish', $p_id).postform('delete', $p_id).'</div>';
  } elseif ($p_status == 'redrafting') {
    echo postform('republish', $p_id).postform('delete', $p_id).'</div>';
  } elseif ($p_status == 'pre-draft') {
    echo postform('publish', $p_id).postform('delete', $p_id).'</div>';
  }

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
  echo '<td onmouseover="showTypify'.$show_div_count.'()" onmouseout="showTypify'.$show_div_count.'()">'
  .$p_type.'<br><div id="showtypify'.$show_div_count.'" style="display: none;">';
  if ($p_type == 'page') {
    echo postform('make post', $p_id).'</div>';
  } else {
    echo postform('make page', $p_id).'</div>';
  }

  // JavaScript with unique function name per row, show/hide action links
  ?>
  <script>
  function showTypify<?php echo $show_div_count; ?>() {
    var x = document.getElementById("showtypify<?php echo $show_div_count; ?>");
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
