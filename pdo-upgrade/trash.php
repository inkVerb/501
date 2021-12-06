<?php

// Include our config (with SQL) up near the top of our PHP file
include ('./in.db.php');

// Include our login cluster
$head_title = "Trash"; // Set a <title> name used next
$edit_page_yn = false; // Include JavaScript for TinyMCE?
include ('./in.logincheck.php');
include ('./in.head.php');

// Include our pieces functions
include ('./in.metaeditfunctions.php');

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
$query = $database->prepare("SELECT id FROM pieces WHERE status='dead' ORDER BY CASE WHEN pub_yn=false THEN 0 ELSE 1 END, date_live DESC");
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

// JavaScript
?>
<script>

// Double-check for "Purge all trash"
function showPurgeAll() {
  var x = document.getElementById("purge_all_trash");
  if (x.style.display === "inline") {
    x.style.display = "none";
  } else {
    x.style.display = "inline";
  }
}

// show/hide Bulk Actions
function showBulkActions() {
  var x = document.getElementById("bulk_actions_div");
  if (x.style.display === "block") {
    x.style.display = "none";
  } else {
    x.style.display = "block";
  }

  // Hide/show all checkboxes by class
  [].forEach.call(document.querySelectorAll(".del_checkbox"), function (c) {
    if (c.style.display === "inline") {
      c.style.display = "none";
    } else {
      c.style.display = "inline";
    }
  });
}

// "Select all"
function toggle(source) {
  var checkboxes = document.querySelectorAll('input[type="checkbox"]');
  for (var i = 0; i < checkboxes.length; i++) {
    if (checkboxes[i] != source)
      checkboxes[i].checked = source.checked;
  }
}

// show/hide view links in Title
function showViews(p_id) {
  var x = document.getElementById("showviews"+p_id);
  if (x.style.display === "inline") {
    x.style.display = "none";
  } else {
    x.style.display = "inline";
  }
}

// show/hide action links in Action
function showActions(p_id) {
  var x = document.getElementById("showaction"+p_id);
  if (x.style.display === "inline") {
    x.style.display = "none";
  } else {
    x.style.display = "inline";
  }
}

// Clear "changed" status
function clearChanged(p_id) {
  document.getElementById("prow_"+p_id).classList.remove("renew","deleting","undeleting"); // Remove the .renew class from the <tr> added by AJAX
  document.getElementById("changed_"+p_id).style.display = "none"; // Hide the "changed" clickable message added by AJAX
  document.getElementById("showaction"+p_id).style.display = "inline";
}

// Clear "purged" status
function clearPurged(p_id) {
  document.getElementById("prow_"+p_id).style.display = "none" // Remove the <tr> row
}
</script>
<?php

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
						echo "\" title=\"Page 1\" href=\"$blog_web_base/trash.php?r=1\">&laquo;</a>
					</td>
					<td>
						<a class=\"paginate";
            if ($paged == 1) {echo " disabled";}
           echo "\" title=\"Previous\" href=\"$blog_web_base/trash.php?r=$prevpaged\">&lsaquo;&nbsp;</a>
					</td>
					<td>
						<a class=\"paginate current\" title=\"Next\" href=\"$blog_web_base/trash.php?r=$paged\">Page $paged ($totalpages)</a>
					</td>
					<td>
						<a class=\"paginate";
            if ($paged == $totalpages) {echo " disabled";}
           echo "\" title=\"Next\" href=\"$blog_web_base/trash.php?r=$nextpaged\">&nbsp;&rsaquo;</a>
					</td>
					 <td>
						 <a class=\"paginate";
						 if ($paged == $totalpages) {echo " disabled";}
	 					echo "\" title=\"Last Page\" href=\"$blog_web_base/trash.php?r=$totalpages\">&raquo;</a>
					 </td>
		 		</tr>
			</table>
		</div>
	</div>";
}

// Pieces link
echo '<a class="blue" href="'.$blog_web_base.'/pieces.php">Back to Pieces</a> | <span class="red" style="cursor: pointer;" onclick="showPurgeAll()">Purge all trash &rarr;</span> <a class="red" id="purge_all_trash" href="'.$blog_web_base.'/purge_all_trash.php" style="display:none"><i>Yes! Purge all trash</i></a>';


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
<label><input type="checkbox" onclick="toggle(this);"> <b>Select all</b></label>
</div>';


// Start our HTML table
echo '
<table class="contentlib" id="pieces-table">
  <tbody>
    <tr>
      <th width="40%">Title</th>
      <th width="40%">Action</th>
      <th width="20%">Type</th>
    </tr>
';

// Get and display each piece
$query = $database->prepare("SELECT id, type, title, date_live, date_created FROM pieces WHERE status='dead' ORDER BY CASE WHEN pub_yn=false THEN 0 ELSE 1 END, date_live DESC LIMIT $itemskip,$pageitems");
$rows = $pdo->exec_($query);
// Start our row colors
$table_row_color = 'blues';
// We have many entries, this will iterate one post per each
foreach ($rows as $row) {
    // Assign the values
    $p_id = "$row->id";
    $p_type = "$row->type";
    $p_title = "$row->title";
    $p_date_live = "$row->date_live";
    $p_date_created = "$row->date_created";
  // Dead or live?
  $status_class = 'pieces_dead';

  // Type
  if ($p_type == 'post') {
    $show_type = '&#8267; post';
  } elseif ($p_type == 'page') {
    $show_type = '&#10081; page';
  }

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
  echo '<td onmouseover="showViews('.$p_id.')" onmouseout="showViews('.$p_id.')">
  <b>'.$p_title.'</b><br>
  <div class="del_checkbox" style="display: none;"><input form="bulk_actions" type="checkbox" id="bulk_'.$p_id.'" name="bulk_'.$p_id.'" value="'.$p_id.'"></div> '.$p_date_note.'
  <div id="showviews'.$p_id.'" style="display: none;">
  <a style="float: none;" href="'.$blog_web_base.'/edit.php?p='.$p_id.'">edit</a>
  <a style="float: right;" class="orange" href="'.$blog_web_base.'/piece.php?p='.$p_id.'&preview">preview</a>
  </div></td>';

  // Actions
  echo '<td onmouseover="showActions('.$p_id.')" onmouseout="showActions('.$p_id.')">
    <span id="readydelete'.$p_id.'">&#10008; ready to purge</span>
    <code onclick="clearChanged('.$p_id.')" title="dismiss" style="float: right; cursor: pointer; display: none;" id="changed_'.$p_id.'">&nbsp;changed&nbsp;</code>
    <code onclick="clearPurged('.$p_id.')" title="dismiss" style="float: right; cursor: pointer; display: none;" id="purged_'.$p_id.'">&nbsp;purged&nbsp;</code><br>
    <div id="showaction'.$p_id.'" style="display: none;">
    <div id="r_redelete_'.$p_id.'" style="display: none;">'.metaeditform('redelete', $p_id).'</div>
    <div id="r_restore_'.$p_id.'" style="display: inherit;">'.metaeditform('restore', $p_id).'</div>
    <div id="r_pdelete_'.$p_id.'" style="display: inherit;">'.metaeditform('purge', $p_id).'</div>
    </div>';

  echo '</td>';

  // Type
  echo '<td>'.$show_type.'<br></td>';

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
						echo "\" title=\"Page 1\" href=\"$blog_web_base/trash.php?r=1\">&laquo;</a>
					</td>
					<td>
						<a class=\"paginate";
            if ($paged == 1) {echo " disabled";}
           echo "\" title=\"Previous\" href=\"$blog_web_base/trash.php?r=$prevpaged\">&lsaquo;&nbsp;</a>
					</td>
					<td>
						<a class=\"paginate current\" title=\"Next\" href=\"$blog_web_base/trash.php?r=$paged\">Page $paged ($totalpages)</a>
					</td>
					<td>
						<a class=\"paginate";
            if ($paged == $totalpages) {echo " disabled";}
           echo "\" title=\"Next\" href=\"$blog_web_base/trash.php?r=$nextpaged\">&nbsp;&rsaquo;</a>
					</td>
					 <td>
						 <a class=\"paginate";
						 if ($paged == $totalpages) {echo " disabled";}
	 					echo "\" title=\"Last Page\" href=\"$blog_web_base/trash.php?r=$totalpages\">&raquo;</a>
					 </td>
		 		</tr>
			</table>
		</div>
	</div>";
}

// Footer
include ('./in.footer.php');
