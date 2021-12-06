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

// Clear "changed" status
function clearChanged(p_id) {
  document.getElementById("prow_"+p_id).classList.remove("renew","deleting","undeleting","metaupdate"); // Remove the .renew class from the <tr> added by AJAX
  document.getElementById("changed_"+p_id).style.display = "none"; // Hide the "changed" clickable message added by AJAX
  document.getElementById("showaction"+p_id).style.display = "inline";
  document.getElementById("showtypify"+p_id).style.display = "none";
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

// show/hide Bulk Actions
function showBulkActions() {
  var x = document.getElementById("bulk_actions_div");
  if (x.style.display === "block") {
    x.style.display = "none";
  } else {
    x.style.display = "block";
  }

  // Hide/show all checkboxes by class
  [].forEach.call(document.querySelectorAll(".bulk_checkbox"), function (c) {
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

// Meta Edit
  // Close the <form>
  function metaEditClose(p_id) {
    // innerHTML replace with the original Title content
    document.getElementById("showviews"+p_id).style.display = "none";
    document.getElementById("me"+p_id).innerHTML = "";
    document.getElementById("prow_"+p_id).style.display = "";
  }

  // Initiate AJAX
  function metaEditAjax(p_id) {
    var ajax;
    ajax = new XMLHttpRequest();
    return ajax;

  }

  // AJAX the <form>
  function metaEdit(p_id) {

    // Close box
    var x = document.getElementById("me"+p_id);
    if (x.style.display === "inline") { // Box is open, clicking Title again to close
      document.getElementById("me"+p_id).style.display = "none"; // Hide the box
      document.getElementById("me"+p_id).innerHTML = ""; // Empty the box
      document.getElementById("prow_"+p_id).style.display = ""; // Show the normal <tr> row

    } else { // Box is closed, clicking Title to open

      // AJAX handler
      var ajaxHandler = metaEditAjax(p_id);
      ajaxHandler.onreadystatechange = function() {
        if (ajaxHandler.readyState == 4 && ajaxHandler.status == 200) {

          // Hide the normal <tr> row
          document.getElementById("prow_"+p_id).style.display = "none";

          // Show box
          document.getElementById("me"+p_id).classList.add("metaedit");
          document.getElementById("me"+p_id).style.display = "";

          // Update to see the <form> from AJAX
          document.getElementById("me"+p_id).innerHTML = ajaxHandler.responseText;

          // Capture submit button for AJAX
          listenToMetaEditForm(p_id);
        }
      }
      // POST to the AJAX source and get the actual <form>
      ajaxHandler.open("POST", "ajax.metaedit.php", true);
      ajaxHandler.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
      ajaxHandler.send("p_id="+p_id);

    } // End clicking to open
  } // End editMeta()

  // Listen for a submit
  function sendEditMetaData(p_id) {
    var form = document.getElementById('meta_edit_form_'+p_id);
    var AJAX = new XMLHttpRequest();
    var FD = new FormData(form);
    AJAX.addEventListener( "load", function(event) { // Hear back with AJAX success
      // Parse our response
      var jsonMetaEditResponse = JSON.parse(event.target.responseText); // For "title" and "changed"
      // Make our JavaScript changes
      document.getElementById("showviews"+p_id).style.display = "none"; // Hide the view actions
      document.getElementById("me"+p_id).style.display = "none"; // Hide the box
      document.getElementById("me"+p_id).innerHTML = ""; // Empty the box
      document.getElementById("title_"+p_id).innerHTML = '<b class="piece_title" onclick="metaEdit('+p_id+')" style="cursor: pointer;">'+jsonMetaEditResponse["title"]+' &#9998;</b>';// Change the Title
      document.getElementById("prow_"+p_id).classList.add("metaupdate"); // Note the <tr> row
      document.getElementById("prow_"+p_id).style.display = ""; // Show the normal <tr> row
      document.getElementById("changed_"+p_id).classList.add("metaupdate"); // Note the <tr> row
      document.getElementById("changed_"+p_id).classList.remove("renew","deleting","undeleting");
      document.getElementById("changed_"+p_id).innerHTML = '&nbsp;'+jsonMetaEditResponse["message"]+'&nbsp;'; // Note the <tr> row
      document.getElementById("changed_"+p_id).style.display = "inline"; // Show our "changed indicator"
    } );
    AJAX.addEventListener( "error", function(event) { // Error in AJAX
      document.getElementById("prow_"+p_id).classList.add("deleting"); // Note the <tr> row
      document.getElementById("changed_"+p_id).classList.add("renew"); // Note the <tr> row
      document.getElementById("changed_"+p_id).classList.remove("metaupdate","deleting","undeleting"); // Note the <tr> row
      document.getElementById("changed_"+p_id).innerHTML = "error with Meta Edit"; // Note the <tr> row
      document.getElementById("changed_"+p_id).style.display = "inline"; // Show our "changed indicator"
    } );
    AJAX.open("POST", "ajax.metaedit.php");
    AJAX.send(FD);
  }

  // Capture submit button for AJAX
  function listenToMetaEditForm(p_id){
    var form = document.getElementById('meta_edit_form_'+p_id);
    form.addEventListener( "submit", function(event) {
      event.preventDefault();
      sendEditMetaData(p_id);
    } );
  }

  // Schedule click show/hide
    // Click on the checkbox to show/check
    function showGoLiveOptionsBox(p_id) {
      var x = document.getElementById("goLiveOptions"+p_id);
      if (x.style.display === "block") {
        x.style.display = "none";
      } else {
        x.style.display = "block";
      }
    }
    // Click on the label to show/check
    function showGoLiveOptionsLabel(p_id) {
      // Show the Date Live schedule div
      var x = document.getElementById("goLiveOptions"+p_id);
      if (x.style.display === "block") {
        x.style.display = "none";
      } else {
        x.style.display = "block";
      }
      // Use JavaScript to check the box
      var y = document.getElementById("p_live_schedule_"+p_id);
      if (y.checked === false) {
        y.checked = true;
      } else {
        y.checked = false;
      }
    }
  // End Schedule click show/hide
// End Meta Edit
</script>
<?php

// Trash link
echo '<a class="red" href="trash.php">View trash</a>';

// Simple line
echo '<br><hr><br>';

// Bulk actions
echo '<div onclick="showBulkActions()" style="cursor: pointer; display: inline;"><b>Bulk actions &#9660;</b></div><br>
<div id="bulk_actions_div" style="display: none;">
<form id="bulk_actions" method="post" action="act.bulkpieces.php">
  <table>
    <tr>
      <td><b><input type="submit" class="green" name="bluksubmit" value="republish"></b></td>
      <td><b><input type="submit" class="orange" name="bluksubmit" value="unpublish"></b></td>
      <td><b><input type="submit" class="blue" name="bluksubmit" value="make post"></b></td>
      <td><b><input type="submit" class="blue" name="bluksubmit" value="make page"></b></td>
      <td><b><input type="submit" class="orange" name="bluksubmit" value="undelete"></b></td>
      <td><b><input type="submit" class="red" name="bluksubmit" value="delete"></b></td>
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
    <th width="53%">Title</th>
    <th width="32%">Status</th>
    <th width="15%">Type</th>
    </tr>
';

// Get and display each piece
$query = "SELECT id, type, status, pub_yn, title, date_live, date_created FROM pieces WHERE status='live' ORDER BY date_live DESC";
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
  // This does not affect dead pieces that will AJAX back, which would remain dead anyway
  if (($p_pub_yn == true) && ($p_status == 'live')) {
    $query_pub = "SELECT status, pubstatus FROM publications WHERE status='live' AND piece_id='$p_id'";
    $call_pub = mysqli_query($database, $query_pub);
    $row_pub = mysqli_fetch_array($call_pub, MYSQLI_NUM);
      // Update the $p_status
      $p_status = ("$row_pub[0]" == 'live') ? "$row_pub[1]" : "$row_pub[0]";
  } elseif (($p_pub_yn == false) && ($p_status == 'live')) {
    $p_status = 'pre-draft';
  }

  // Status
  $status_class = 'pieces_live';
  if ($p_status == 'published') {
    $show_status = '&#10004; published';
  } elseif ($p_status == 'redrafting') {
    $show_status = '&#10001; redrafting';
  } elseif ($p_status == 'pre-draft') {
    $show_status = '&#10001; pre-draft';
  }

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
  // Place our AJAX Meta Edit <tr> row
  echo '<tr id="me'.$p_id.'" class="meta_edit_box '."$table_row_color $status_class".'" style="display: hidden;"></tr>';
  // Start our HTML table
  echo '<tr class="pieces '."$table_row_color $status_class".'" id="prow_'.$p_id.'">';

  // Title
  $title_content = '<b class="piece_title" onclick="metaEdit('.$p_id.')" style="cursor: pointer;">'.$p_title.' &#9998;</b>';
  echo '<td onmouseover="showViews('.$p_id.')" onmouseout="showViews('.$p_id.')">
  <div style="display: inline;" id="title_'.$p_id.'">'.$title_content.'</div><br>
  <div class="bulk_checkbox" style="display: none;"><input form="bulk_actions" type="checkbox" id="bulk_'.$p_id.'" name="bulk_'.$p_id.'" value="'.$p_id.'"></div> '.$p_date_note.'
  <div id="showviews'.$p_id.'" style="display: none;">
  <a style="float: none;" href="edit.php?p='.$p_id.'">Edit &rarr;</a>
  <a style="float: right;" class="orange" href="piece.php?p='.$p_id.'&preview">preview</a>
  </div>';

  echo '</td>';

  // Status
  echo '<td onmouseover="showActions('.$p_id.')" onmouseout="showActions('.$p_id.')">
  <span id="pstatus'.$p_id.'">'.$show_status.'</span><i id="pdeleting'.$p_id.'" style="display: none;">&#10008; trashed</i> <code onclick="clearChanged('.$p_id.')" title="dismiss" style="float: right; cursor: pointer; display: none;" id="changed_'.$p_id.'">&nbsp;changed&nbsp;</code><br>
  <div id="showaction'.$p_id.'" style="display: none;">';
  // We want this because we will AJAX changes in the future to allow class="pieces_dead" to show before a page reload, we want this as a logical placeholder, but this actually does nothing
  if ($p_status == 'published') {
    echo '<div id="r_undelete_'.$p_id.'" style="display: none;">'.metaeditform('undelete', $p_id).'</div>
    <div id="r_status_'.$p_id.'" style="display: inherit;">'.metaeditform('unpublish', $p_id).' <a class="purple" href="hist.php?p='.$p_id.'">history</a>&nbsp;&nbsp;<a class="green" href="piece.php?p='.$p_id.'">view</a> </div>
    <div id="r_delete_'.$p_id.'" style="display: inherit;">'.metaeditform('delete', $p_id).'</div></div>';
  } elseif ($p_status == 'redrafting') {
    echo '<div id="r_undelete_'.$p_id.'" style="display: none;">'.metaeditform('undelete', $p_id).'</div>
    <div id="r_status_'.$p_id.'" style="display: inherit;">'.metaeditform('republish', $p_id).' <a class="purple" href="hist.php?p='.$p_id.'">history</a> </div>
    <div id="r_delete_'.$p_id.'" style="display: inherit;">'.metaeditform('delete', $p_id).'</div></div>';
  } elseif ($p_status == 'pre-draft') {
    echo '<div id="r_undelete_'.$p_id.'" style="display: none;">'.metaeditform('undelete', $p_id).'</div>
    <div id="r_delete_'.$p_id.'" style="display: inherit;">'.metaeditform('delete', $p_id).'</div></div>';
  }

  echo '</td>';

  // Type
  echo '<td onmouseover="showTypify('.$p_id.')" onmouseout="showTypify('.$p_id.')">
  <span id="ptype'.$p_id.'">'.$show_type.'</span><br><div id="showtypify'.$p_id.'" style="display: none;">';
  if ($p_type == 'page') {
    echo '<div id="r_make_'.$p_id.'">'.metaeditform('make post', $p_id).'</div></div>';
  } else {
    echo '<div id="r_make_'.$p_id.'">'.metaeditform('make page', $p_id).'</div></div>';
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
