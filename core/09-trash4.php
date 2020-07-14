<?php

// Include our config (with SQL) up near the top of our PHP file
include ('./in.config.php');

// Include our login cluster
$head_title = "Trash"; // Set a <title> name used next
$edit_page_yn = false; // Include JavaScript for TinyMCE?
include ('./in.login_check.php');

// Include our pieces functions
include ('./in.metaeditfunctions.php');

// Trash link
echo '<a class="blue" href="pieces.php">Back to Pieces</a> | <a class="red" href="purge_all_trash.php">Purge all trash</a>';

// Simple line
echo '<br><hr><br>';

// Start our HTML table
echo "
<table>
  <tbody>
    <tr>
      <th>Title</th>
      <th>Action</th>
      <th>Type</th>
    </tr>
";

// Get and display each piece
$query = "SELECT id, type, title, date_live, date_created FROM pieces WHERE status='dead'";
$call = mysqli_query($database, $query);
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
    $p_date_note = "Started: $p_date_created";
  } else {
    $p_date_note = "Live: $p_date_live";
  }

  // Display the info in a <table>
  // Start our HTML table
  echo '<tr>';

  // Title
  echo '<td><b>'.$p_title.'</b><br>
      '.$p_date_note.'</td>';

  // Actions
  echo '<td>'.metaeditform('restore', $p_id).metaeditform('purge', $p_id).'</td>';

  // Type
  echo '<td>'.$p_type.'<br></td>';

  // Finish piece
  echo '</tr>';

}

echo "
  </tbody>
</table>
";

// Simple line
echo '<br><hr><br>';

// Footer
include ('./in.footer.php');
