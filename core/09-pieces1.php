<?php
// No <head> yet because we might redirect, which uses header() and might break after the <head> tag

// Include our config (with SQL) up near the top of our PHP file
include ('./in.config.php');

// Include our piece functions
include ('./in.piecefunctions.php');

// Include our login cluster
$head_title = "Pieces"; // Set a <title> name used next
$edit_page_yn = false; // Include JavaScript for TinyMCE?
include ('./in.login_check.php');

// Simple line
echo '<br><hr><br>';

// Start our HTML table
echo '
<table>
  <tbody>
    <tr>
      <th>ID</th>
      <th>Title</th>
      <th>Type</th>
      <th>Status</th>
      <th>Created</th>
    </tr>
';

// Get and display each piece
$query = "SELECT id, type, status, title, date_created FROM pieces";
$call = mysqli_query($database, $query);
// We have many entries, this will iterate one post per each
while ($row = mysqli_fetch_array($call, MYSQLI_NUM)) {
  // Assign the values
  $p_id = "$row[0]";
  $p_type = "$row[1]";
  $p_status = "$row[2]";
  $p_title = "$row[3]";
  $p_date_created = "$row[4]";

  // Display the info in a <table>
  // Start our HTML table
  echo "
    <tr>
      <td>$p_id</td>
      <td>$p_title</td>
      <td>$p_type</td>
      <td>$p_status</td>
      <td>$p_date_created</td>
    </tr>
  ";


}

echo "
  </tbody>
</table>
";

// Simple line
echo '<br><hr><br>';

// Footer
include ('./in.footer.php');
