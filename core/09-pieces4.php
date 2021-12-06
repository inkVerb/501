<?php

// Include our config (with SQL) up near the top of our PHP file
include ('./in.config.php');

// Include our login cluster
$head_title = "Pieces"; // Set a <title> name used next
$edit_page_yn = false; // Include JavaScript for TinyMCE?
include ('./in.logincheck.php');
include ('./in.head.php');

// Include our pieces functions
include ('./in.metaeditfunctions.php');

// Trash link
echo '<a class="red" href="trash.php">View trash</a>';

// Simple line
echo '<br><hr><br>';

// Start our HTML table
echo '
<table>
  <tbody>
    <tr>
      <th>Title</th>
      <th>Status</th>
      <th>Type</th>
    </tr>
';

// Get and display each piece
$query = "SELECT id, type, status, pub_yn, title, date_live, date_created FROM pieces ORDER BY date_live DESC";
$call = mysqli_query($database, $query);
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
    $p_date_note = "Started: $p_date_created";
  } else {
    $p_date_note = "Live: $p_date_live";
  }

  // Display the info in a <table>
  // Start our HTML table
  echo '<tr class="'.$status_class.'">';

  // Title
  echo '<td><b><a href="edit.php?p='.$p_id.'">'.$p_title.'</a></b><br>
      '.$p_date_note.'</td>';

  // Status
  echo '<td>'.$p_status.'<br>';
  if ($p_status == 'dead') {
    echo metaeditform('undelete', $p_id).metaeditform('delete forever', $p_id).'</td>';
  } elseif ($p_status == 'published') {
    echo metaeditform('unpublish', $p_id).metaeditform('delete', $p_id).'</td>';
  } elseif ($p_status == 'redrafting') {
    echo metaeditform('republish', $p_id).metaeditform('delete', $p_id).'</td>';
  } elseif ($p_status == 'pre-draft') {
    echo metaeditform('delete', $p_id).'</td>';
  }

  // Type
  echo '<td>'.$p_type.'<br>';
  if ($p_type == 'page') {
    echo metaeditform('make post', $p_id).'</td>';
  } else {
    echo metaeditform('make page', $p_id).'</td>';
  }

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
