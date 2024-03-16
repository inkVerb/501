<?php
// Include our config (with SQL) up near the top of our PHP file
include ('./in.db.php');
include ('./in.logincheck.php');

if ( ($_SERVER['REQUEST_METHOD'] === 'POST') && (isset($_POST['new_series'])) ) {

  // Make sure we're not creating an empty Series
  if (preg_replace('/\s+/', '', $_POST['new_series']) != '') {
    // Series name
    $result = strip_tags($_POST['new_series']); // Remove any HTML tags
    $result = preg_replace('/([0-9]$)+-+([0-9])/','$1–$2',$result); // to en-dash
    $result = str_replace('---','—',$result); // to em-dash
    $result = str_replace(' -- ',' – ',$result); // to en-dash
    $result = str_replace('--','—',$result); // to em-dash
    $result = substr($result, 0, 60); // Limit to 60 characters
    $new_series = $result;
    $new_series_trim = DB::trimspace($new_series);

    // Series slug
      // Generate the slug
      $regex_replace = "/[^a-zA-Z0-9-]/";
      $result = strtolower(preg_replace($regex_replace,"-", $new_series)); // Lowercase, all non-alnum to hyphen
      $s_slug = substr($result, 0, 60); // Limit to 60 characters

      // Check that the slug isn't already used
      $s_slug_test_trim = DB::trimspace($s_slug);
      $row = $pdo->select('series', 'slug', $s_slug, 'id');
      if ($pdo->numrows == 1) {
        $add_num = 0;
        $dup = true;
        // If there were no changes
        while ($dup = true) {
          $add_num = $add_num + 1;
          $try_s_slug = $s_slug_test_trim.'-'.$add_num;

          // Check again
          $row = $pdo->select('series', 'slug', $try_s_slug, 'id');
          if ($pdo->numrows == 0) {
            $new_s_slug = $try_s_slug;
            break;
          }
        }
      } else {
        $new_s_slug_trim = $s_slug_test_trim;
      }

    // Add the new Series
    if ((isset($new_series_trim)) && (isset($new_s_slug_trim))) {
      $query = $database->prepare("INSERT INTO series (name, slug) VALUES (:name, :slug)");
      $query->bindParam(':name', $new_series_trim);
      $query->bindParam(':slug', $new_s_slug_trim);

      $pdo->exec_($query);
      if ($pdo->ok) {
        // Get the most recent ID of the last INSERT statement
        $p_series = $pdo->lastid;
      }
    }
  } // End empty check

// Recreate the select input

// Query the Serieses
$rows = $pdo->exec_($database->prepare("SELECT id, name FROM series ORDER BY name"));

// Start the select input
// We need the div with our AJAX form inside so the input value is reset on success
echo '
<div id="p_series">
<select form="edit_piece" name="p_series" onchange="onNavWarn();" onkeyup="onNavWarn();" onclick="onNavWarn();">';

// Iterate each Series
foreach ($rows as $row) {
  $s_id = "$row->id";
  $s_name = "$row->name";
  $selected_yn = ($p_series == $s_id) ? ' selected="selected"' : ''; // So 'selected="selected"' appears in the Series
  echo '<option value="'.$s_id.'"'.$selected_yn.'>'.$s_name.'</option>';
}

// Finish the select input
echo '</select>';

// New Series form
echo '<br><br>
<form id="add_new_series">
  <input form="add_new_series" type="text" name="new_series" id="new_series_input_text">
  <button type="button" title="Create the new series" onclick="newSeries(); offNavWarn();">+ Series</button>
</form>
</div>
';

}
?>
