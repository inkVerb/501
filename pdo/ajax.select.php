<?php
// Include our config (with SQL) up near the top of our PHP file
include ('./in.config.php');
include ('./in.logincheck.php');

if (($_SERVER['REQUEST_METHOD'] === 'POST') && (isset($_POST['new_series']))) {

  // Series name
  $result = filter_var($_POST['new_series'], FILTER_SANITIZE_STRING); // Remove any HTML tags
  $result = preg_replace('/([0-9]$)+-+([0-9])/','$1–$2',$result); // to en-dash
  $result = str_replace(' -- ',' – ',$result); // to en-dash
  $result = str_replace('---','—',$result); // to em-dash
  $result = str_replace('--','—',$result); // to em-dash
  $result = substr($result, 0, 90); // Limit to 90 characters
  $new_series = $result;
  $new_series_sqlesc = DB::esc($new_series);

  // Series slug
    // Generate the slug
    $regex_replace = "/[^a-zA-Z0-9-]/";
    $result = strtolower(preg_replace($regex_replace,"-", $new_series)); // Lowercase, all non-alnum to hyphen
    $s_slug = substr($result, 0, 90); // Limit to 90 characters


    // Check that the slug isn't already used
    $s_slug_test_sqlesc = DB::esc($s_slug);
    $query = "SELECT id FROM series WHERE slug='$s_slug'";
    $call = mysqli_query($database, $query);
    if (mysqli_num_rows($call) == 1) {
      $add_num = 0;
      $dup = true;
      // If there were no changes
      while ($dup = true) {
        $add_num = $add_num + 1;
        $try_s_slug = $s_slug_test_sqlesc.'-'.$add_num;

        // Check again
        $query = "SELECT id FROM series WHERE slug='$try_s_slug'";
        $call = mysqli_query($database, $query);
        if (mysqli_num_rows($call) == 0) {
          $new_s_slug = $try_s_slug;
          break;
        }
      }
    } else {
      $new_s_slug_sqlesc = $s_slug_test_sqlesc;
    }

  // Add the new Series
  if ((isset($new_series_sqlesc)) && (isset($new_s_slug_sqlesc))) {
    $query = "INSERT INTO series (name, slug) VALUES ('$new_series_sqlesc', '$new_s_slug_sqlesc')";
    $call = 'mysqli_query($database, $query)SQL query would go here, but we are not changing the database in this example';
    if ($call) {
      // Normally, we get the last ID here
    }
  }
  $p_series = 'example'; // This example
  $query_eg = $query; // This example

// Recreate the select input

// Query the Serieses
$query = "SELECT id, name FROM series";
$call = mysqli_query($database, $query);

// Start the select input
// We need the div with our AJAX form inside so the input value is reset on success
echo '
<div id="p_series">
<select form="edit_piece" name="p_series">';

// Iterate each Series
while ($row = mysqli_fetch_array($call, MYSQLI_NUM)) {
  $s_id = "$row[0]";
  $s_name = "$row[1]";
  $selected_yn = ($p_series == $s_id) ? ' selected' : ''; // So 'selected' appears in the Series
  echo '<option value="'.$s_id.'"'.$selected_yn.'>'.$s_name.'</option>';
}
  echo '<option value="'.$p_series.'" selected>'.$new_series.'</option>'; // For our example

// Finish the select input
echo '</select>';

// New Series form
echo '<br><br>
<form id="add_new_series">
  <input type="text" name="new_series" id="new_series_input_text">
  <input type="submit" value="+ Series">
</form>
<pre>This would normally run SQL: '.$query_eg.';</pre>
</div>
';

}
?>
