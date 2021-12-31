<?php

// Include our config (with SQL) up near the top of our PHP file
include ('./in.db.php');

// Include our login cluster
$head_title = "Aggregated Feeds"; // Set a <title> name used next
$edit_page_yn = false; // Include JavaScript for TinyMCE?
$agg_editor_yn = false; // Series editor
include ('./in.logincheck.php');
include ('./in.head.php');

include ('./in.functions.php');

// POST new feed?
if ((isset($_POST['feed_name']))
&& (isset($_POST['feed_url']))
&& (isset($_POST['p_series']))
&& (filter_var($_POST['feed_url'], FILTER_VALIDATE_URL))
&& (filter_var($_POST['p_series'], FILTER_VALIDATE_INT))) {
  $new_name = checkPost('feed_name', $_POST['feed_name']);
  $new_url = checkPost('feed_url', $_POST['feed_url']);
  $new_series = preg_replace("/[^0-9]/","", $_POST['p_series']);

  // No errors, run INSERT
  if (empty($check_err)) {
    $query = $database->prepare("INSERT INTO aggregation (name, source, series) VALUES (:name, :source, :series)");
    $query->bindParam(':name', $new_name);
    $query->bindParam(':source', $new_url);
    $query->bindParam(':series', $new_series);
    $pdo->exec_($query);
    if ($pdo->ok) {
       echo '<p class="green">Added RSS feed "'.$new_name.'"!</p>';
    } else {
      echo '<p class="error">Errors adding feed.</p>';
      exit ();
    }

  }

}

// JavaScript
?>
<script>
// show/hide action link
function showChangeButton(f_id) {
  var x = document.getElementById("make-edits-"+f_id);
  if (x.style.display === "inline") {
    x.style.display = "none";
  } else {
    x.style.display = "inline";
  }
}

// show/hide action link
// For the Default series, the "Permanently delete series" checkbox and <div> do not exist, so running a JavaScript action for it would break the above line also
// So, we must check to see if the checkbox <div> even exists, only then we run the script action to change it
function showHideEdit(f_id) {
  var y = document.getElementById("e_buttons_"+f_id);
  if (y.style.display === "inline") {
    document.getElementById("change-cancel-"+f_id).innerHTML = 'change';
    y.style.display = "none";
  } else {
    document.getElementById("change-cancel-"+f_id).innerHTML = 'cancel';
    y.style.display = "inline";
  }
  if (elementExists = document.getElementById("delete-checkbox-"+f_id)) {
    var x = document.getElementById("delete-checkbox-"+f_id);
    if (x.style.display === "inline") {
      x.style.display = "none";
    } else {
      x.style.display = "inline";
    }
  }
}

// The editor save
function feedSave(fID) { // These arguments can be anything, same as used in this function

    // Bind a new event listener every time the <form> is changed:
    const FORM = document.getElementById("feed-edit-"+fID);
    const AJAX = new XMLHttpRequest(); // AJAX handler
    const FD = new FormData(FORM); // Bind to-send data to form element

    AJAX.addEventListener( "load", function(event) { // This runs when AJAX responds
      var jsonSeriesEditResponse = JSON.parse(event.target.responseText); // For contents from the form
      if (jsonSeriesEditResponse["change"] == 'change') { // Name and/or slug change
        showHideEdit(fID);// Hide the Save/Cancel buttons
        document.getElementById("input-name-"+fID).value = jsonSeriesEditResponse["name"]; // Update Name
        document.getElementById("input-source-"+fID).value = jsonSeriesEditResponse["source"]; // Update Slug
      } else if (jsonSeriesEditResponse["change"] == 'nochange') { // No change
        showHideEdit(fID); // Hide the Save/Cancel buttons
      }
      // Every scenario is considered, not update <div id="edit-message-ID"> with our AJAX response message
      document.getElementById("edit-message-"+fID).innerHTML = jsonSeriesEditResponse["message"]; // Message
    } );

    AJAX.addEventListener( "error", function(event) { // This runs if AJAX fails
      document.getElementById("edit-message-"+fID).innerHTML =  'Oops! Something went wrong.';
    } );

    AJAX.open("POST", "ajax.editfeed.php");

    AJAX.send(FD); // Data sent is from the form

  } // feedSave() function

</script>
<?php

// Pagination
// Valid the Pagination
if ((isset($_GET['r'])) && (filter_var($_GET['r'], FILTER_VALIDATE_INT, array('min_range' => 1)))) {
 $paged = preg_replace("/[^0-9]/","", $_GET['r']);
} else {
 $paged = 1;
}
// Set pagination variables:
$pageitems = 100;
$itemskip = $pageitems * ($paged - 1);
// We add this to the end of the $query, after DESC
// LIMIT $itemskip,$pageitems

// Pagination navigation: How many items total?
$query = $database->prepare("SELECT id FROM aggregation ORDER BY name");
$rows = $pdo->exec_($query);
$totalrows = $pdo->numrows;

$totalpages = floor($totalrows / $pageitems);
$remainder = $totalrows % $pageitems;
if ($remainder > 0) {
$totalpages = $totalpages + 1;
}
if ($paged > $totalpages) {
$totalpages = 1;
}
$nextpaged = $paged + 1;
$prevpaged = $paged - 1;

// Pagination nav row
if ($totalpages > 1) {
	echo "
	<div class=\"paginate_nav_container\">
		<div class=\"paginate_nav\">
			<table>
				<tr>
					<td>
						<a class=\"paginate";
						if ($paged == 1) {echo " disabled";}
						echo "\" title=\"Page 1\" href=\"$blog_web_base/aggregator.php?r=1\">&laquo;</a>
					</td>
					<td>
						<a class=\"paginate";
            if ($paged == 1) {echo " disabled";}
           echo "\" title=\"Previous\" href=\"$blog_web_base/aggregator.php?r=$prevpaged\">&lsaquo;&nbsp;</a>
					</td>
					<td>
						<a class=\"paginate current\" title=\"Next\" href=\"$blog_web_base/aggregator.php?r=$paged\">Page $paged ($totalpages)</a>
					</td>
					<td>
						<a class=\"paginate";
            if ($paged == $totalpages) {echo " disabled";}
           echo "\" title=\"Next\" href=\"$blog_web_base/aggregator.php?r=$nextpaged\">&nbsp;&rsaquo;</a>
					</td>
					 <td>
						 <a class=\"paginate";
						 if ($paged == $totalpages) {echo " disabled";}
	 					echo "\" title=\"Last Page\" href=\"$blog_web_base/aggregator.php?r=$totalpages\">&raquo;</a>
					 </td>
		 		</tr>
			</table>
		</div>
	</div>";
}

// Nav links
echo '<a class="blue" href="'.$blog_web_base.'/pieces.php"><small>Back to Pieces</small></a>';
echo '<br><br>';

// Simple line
echo '<br><hr><br>';

// New feed form
echo '<form id="new_feed" method="post" action="aggregator.php">

</form>';
echo '<table id="new-feed-table"><tbody><tr>';
echo '<th>Add new feed</th><th colspan="3"></th></tr><tr>';
echo '<td><br><input type="submit" value="Add feed" form="new_feed"><br><br><br></td>';
echo '<td><small>Nickname:</small><br>'.formInput('feed_name', '', $check_err).'<br><br><br></td>';
echo '<td><small>URL:</small><br>'.formInput('feed_url', '', $check_err).'<br><br><br></td>';
echo '<td><small>Import to series:</small><br>';
// Set the values
$p_series = $blog_default_series;
$series_form = 'new_feed'; // 'edit_piece' or 'blog_settings' or 'new_feed'
include ('./in.series.php');
echo '</td>';

echo '</tr></tbody></table>';

// Simple line
echo '<br><hr><br>';

// Iterate the rows
$query = $database->prepare("SELECT * FROM aggregation ORDER BY name LIMIT $itemskip,$pageitems");
$rows = $pdo->exec_($query);
if ($pdo->numrows > 0) {

  // Start our HTML table
  $table_row_color = 'blues';
  echo '
  <table class="contentlib" id="series-table">
    <tbody>
      <tr>
      <th width="25%">Feed</th>
      <th width="15%">Series</th>
      <th width="40%">Options</th>
      <th width="15%">Since</th>
      <div id="series-details-message">'.$detail_message.'</div>
      </th>
      </tr>
  ';

  foreach ($rows as $row) {
    $agg_id = "$row->id";
    $agg_series = "$row->series";
    $agg_source = "$row->source";
    $agg_name = "$row->name";
    $agg_description = "$row->description";
    $agg_import_media = "$row->import_media";
    $agg_update_interval = "$row->update_interval";
    $agg_status = "$row->status";
    $agg_on_delete = "$row->on_delete";
    $agg_last_updated = "$row->last_updated";
    $agg_date_added = "$row->date_added";

    // Fetch Series info
    $rowsc = $pdo->select('series', 'id', $agg_series, 'name, slug');
    // Shoule be 1 row
    if ($pdo->numrows == 1) {
      foreach ($rowsc as $row) {
        // Assign the values
        $f_id = "$agg_series";
        $s_name = "$row->name";
        $s_slug = "$row->slug";
      }
    }

    // View items row (default shown)
    // Contents
    echo '<tr class="pieces '."$table_row_color".'" id="v_row_'.$agg_id.'" onmouseover="showChangeButton('.$agg_id.');" onmouseout="showChangeButton('.$agg_id.');">';

    // Nickname
    echo '<td id="sne-'.$agg_id.'">
    <form id="feed-edit-'.$agg_id.'" enctype="multipart/form-data">
    <input type="hidden" name="u_id" value="'.$user_id.'">
    <input type="hidden" name="f_id" value="'.$agg_id.'">
    </form>
    <small>Nickname:</small><br>
    <input type="text" form="feed-edit-'.$agg_id.'" id="input-name-'.$agg_id.'" name="agg_name" value="'.$agg_name.'">';

    // URL
    echo '<br><br><small>URL:</small><br>
    <input type="text" form="feed-edit-'.$agg_id.'" id="input-source-'.$agg_id.'" name="agg_source" value="'.$agg_source.'">';

    echo '</td>';

    // Series
    echo '<td id="sav-'.$agg_id.'">';
      echo '<select form="feed-edit-'.$agg_id.'" name="agg_series">';
      // Query the Serieses
      $rows = $pdo->exec_($database->prepare("SELECT id, name FROM series"));

      // Iterate each Series
      foreach ($rows as $row) {
        $opt_id = "$row->id";
        $opt_name = "$row->name";
        $selected_yn = ($agg_series == $opt_id) ? ' selected' : ''; // So 'selected' appears in the Series
        echo '<option value="'.$opt_id.'"'.$selected_yn.'>'.$opt_name.'</option>';
      }
      echo '</select>';

    // Delete checkbox
    echo '<div id="delete-checkbox-'.$agg_id.'" style="display:none;">
    <br><br><label for="feed-delete-'.$agg_id.'"><input type="checkbox" form="feed-edit-'.$agg_id.'" id="feed-delete-'.$agg_id.'" name="feed-delete" value="delete"> <i><small>Permanently delete series</small></i></label>
    </div>';

    echo '</td>';

    // Edit links & messages
    echo '<td id="scv-'.$agg_id.'">
          <table>
            <tr>
              <td id="mcv-'.$agg_id.'">
                <div id="make-edits-'.$agg_id.'" style="display:none;">
                  <button id="change-cancel-'.$agg_id.'" type="button" class="postform link-button inline blue" onclick="showHideEdit('.$agg_id.');">change</button>
                  &nbsp;
                  <a id="view-series-'.$agg_id.'" class="green" target="_blank" href="'.$blog_web_base.'/series/'.$s_slug.'">view</a>
                </div>
              </td>
            </tr>
            <tr>
              <td>
                <div id="e_buttons_'.$agg_id.'" style="display:none;">
                  <button type="button" onclick="feedSave('.$agg_id.');">Save</button>&nbsp;
                  <button type="button" onclick="showHideEdit('.$agg_id.');">Cancel</button>
                </div>
                <div id="edit-message-'.$agg_id.'"></div>
              </td>
            </tr>
          </table>
          </td>';

    // Update interval
    echo '<td id="smv-'.$agg_id.'">';

    echo '</td>';
    echo '</tr>';

    // Toggle our row colors
    $table_row_color = ($table_row_color == 'blues') ? 'shady' : 'blues';

  }
  echo '</tbody></table>';

} else { // If no entries in the series table
  echo "<p>That's strange. Nothing here.</p>";
}
