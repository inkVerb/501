<?php

// Include our config (with SQL) up near the top of our PHP file
include ('./in.config.php');

// Include our login cluster
$head_title = "Pieces"; // Set a <title> name used next
$edit_page_yn = false; // Include JavaScript for TinyMCE?
include ('./in.login_check.php');

// Include our pieces functions
include ('./in.piecesfunctions.php');

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
<label><input type="checkbox" onclick="toggle(this);" /> <b>Select all</b></label>
</div>';

// JavaScript to show/hide Bulk Actions
?>
<script>
function showBulkActions() {
  var x = document.getElementById("bulk_actions_div");
  if (x.style.display === "block") {
    x.style.display = "none";
  } else {
    x.style.display = "block";
  }
}
</script>
<?php
// JavaScript to "Select all"
?>
<script>
function toggle(source) {
    var checkboxes = document.querySelectorAll('input[type="checkbox"]');
    for (var i = 0; i < checkboxes.length; i++) {
        if (checkboxes[i] != source)
            checkboxes[i].checked = source.checked;
    }
}
</script>
<?php

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
$query = "SELECT id, type, status, pub_yn, title, date_live, date_created FROM pieces WHERE status='live'";
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
  // Start our HTML table
  echo '<tr class="'."$table_row_color $status_class".'" id="prow_'.$p_id.'">';

  // Title
  $title_content = '<b class="piece_title" onclick="metaEdit'.$p_id.'()" style="cursor: pointer;">'.$p_title.' &#9998;</b>';
  echo '<td onmouseover="showViews'.$p_id.'()" onmouseout="showViews'.$p_id.'()">
  <div style="display: inline;">'.$title_content.'</div><br>
  <div id="me'.$p_id.'" style="display: hidden;"></div>
  <label for="bulk_'.$p_id.'"><input form="bulk_actions" type="checkbox" id="bulk_'.$p_id.'" name="bulk_'.$p_id.'" value="'.$p_id.'"> '.$p_date_note.'</label>

  <div id="showviews'.$p_id.'" style="display: none;">
  <a style="float: none;" href="edit.php?p='.$p_id.'">Editor &rarr;</a>
  <a style="float: right;" class="orange" href="piece.php?p='.$p_id.'&preview">preview draft</a>
  </div>';

  // JavaScript with unique function name per row, show/hide action links
  ?>
  <script>
  function showViews<?php echo $p_id; ?>() {
    var x = document.getElementById("showviews<?php echo $p_id; ?>");
    if (x.style.display === "inline") {
      x.style.display = "none";
    } else {
      x.style.display = "inline";
    }
  }
  </script>
  <?php
  // JavaScript for metaEdit
  ?>
  <script>
  // This will be used inside the <form> AJAX sends to us; declare the function now
  function metaEditClose<?php echo $p_id; ?>() {
    // innerHTML replace with the original Title content
    document.getElementById("me<?php echo $p_id; ?>").innerHTML = "<?php echo "bye" ?>";
  }

  // Initiate AJAX
  function metaEditAjax<?php echo $p_id; ?>() {
    var ajax;
    ajax = new XMLHttpRequest();
    return ajax;

  }

  // AJAX the <form>
  function metaEdit<?php echo $p_id; ?>() {

    var x = document.getElementById("me<?php echo $p_id; ?>");
    if (x.style.display === "inline") { // Box is open, clicking Title again to close
      document.getElementById("me<?php echo $p_id; ?>").style.display = "none"; // Hide the box
      document.getElementById("me<?php echo $p_id; ?>").innerHTML = ""; // Empty the box

    } else { // Box is closed, clicking Title to open

    // AJAX handler
    var ajaxHandler = metaEditAjax<?php echo $p_id; ?>();
    ajaxHandler.onreadystatechange = function() {
      if (ajaxHandler.readyState == 4 && ajaxHandler.status == 200) {

        // Show box
        document.getElementById("me<?php echo $p_id; ?>").classList.add("metaedit");
        document.getElementById("me<?php echo $p_id; ?>").style.display = "inline";

        // Update to see the <form> from AJAX
        document.getElementById("me<?php echo $p_id; ?>").innerHTML = ajaxHandler.responseText;

        // Capture submit button for AJAX
        form = document.getElementById("<?php echo 'meta_edit_form_'.$p_id; ?>");
        listenToMetaEditForm<?php echo $p_id; ?>();
      }
    }
    // POST to the AJAX and get the actual form
    ajaxHandler.open("POST", "ajax.metaedit.php", true);
    ajaxHandler.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    ajaxHandler.send("p_id=<?php echo $p_id; ?>");

  } // End clicking to open
} // End editMeta()

  // Listen for a submit
  function sendEditMetaData<?php echo $p_id; ?>() {
    const AJAX = new XMLHttpRequest();
    const FD = new FormData( form );
    AJAX.addEventListener( "load", function(event) {

      // After we submit this AJAX-loaded <form>
      document.getElementById("me<?php echo $p_id; ?>").style.display = "none"; // Hide the box
      document.getElementById("me<?php echo $p_id; ?>").innerHTML = ""; // Empty the box
      document.getElementById("prow_<?php echo $p_id; ?>").classList.add("renew"); // Note the <tr> row
      document.getElementById("changed_<?php echo $p_id; ?>").innerHTML = ajaxHandler.responseText; // Note the <tr> row

    } );
    AJAX.addEventListener( "error", function(event) {
      document.getElementById("prow_'.$p_id.'").innerHTML =  "<tr class=\"renew\" id=\"prow_<?php echo $p_id; ?>\" class=\"error\">Error with '.$name.'</tr>";
    } );
    AJAX.open("POST", "ajax.metaedit.php", true);
    AJAX.send(FD);
  }

  // Capture submit button for AJAX
  var form = document.getElementById("<?php echo 'meta_edit_form_'.$p_id; ?>");
  function listenToMetaEditForm<?php echo $p_id; ?>(){
    form.addEventListener( "submit", function(event) {
      event.preventDefault();
      sendEditMetaData<?php echo $p_id; ?>();
    } );
  }

  </script>
  <?php

  echo '</td>';

  // Status
  echo '<td onmouseover="showActions'.$p_id.'()" onmouseout="showActions'.$p_id.'()">
  <span id="pstatus'.$p_id.'">'.$show_status.'</span><i id="pdeleting'.$p_id.'" style="display: none;">&#10008; trashed</i> <code onclick="clearChanged'.$p_id.'()" title="dismiss" style="float: right; cursor: pointer; display: none;" id="changed_'.$p_id.'">&nbsp;changed&nbsp;</code><br>
  <div id="showaction'.$p_id.'" style="display: none;">';
  // We want this because we will AJAX changes in the future to allow class="pieces_dead" to show before a page reload, we want this as a logical placeholder, but this actually does nothing
  if ($p_status == 'published') {
    echo '<div id="r_undelete_'.$p_id.'" style="display: none;">'.piecesform('undelete', $p_id).'</div>
    <div id="r_status_'.$p_id.'" style="display: inherit;">'.piecesform('unpublish', $p_id).' <a class="purple" href="hist.php?p='.$p_id.'">history</a>&nbsp;&nbsp;<a class="green" href="piece.php?p='.$p_id.'">view</a> </div>
    <div id="r_delete_'.$p_id.'" style="display: inherit;">'.piecesform('delete', $p_id).'</div></div>';
  } elseif ($p_status == 'redrafting') {
    echo '<div id="r_undelete_'.$p_id.'" style="display: none;">'.piecesform('undelete', $p_id).'</div>
    <div id="r_status_'.$p_id.'" style="display: inherit;">'.piecesform('republish', $p_id).' <a class="purple" href="hist.php?p='.$p_id.'">history</a> </div>
    <div id="r_delete_'.$p_id.'" style="display: inherit;">'.piecesform('delete', $p_id).'</div></div>';
  } elseif ($p_status == 'pre-draft') {
    echo '<div id="r_undelete_'.$p_id.'" style="display: none;">'.piecesform('undelete', $p_id).'</div>
    <div id="r_delete_'.$p_id.'" style="display: inherit;">'.piecesform('delete', $p_id).'</div></div>';
  }

  // JavaScript with unique function name per row, show/hide action links
  ?>
  <script>
  function showActions<?php echo $p_id; ?>() {
    var x = document.getElementById("showaction<?php echo $p_id; ?>");
    if (x.style.display === "inline") {
      x.style.display = "none";
    } else {
      x.style.display = "inline";
    }
  }
  </script>
  <?php
  // JavaScript to clear "changed" status
  ?>
  <script>
  function clearChanged<?php echo $p_id; ?>() {
    document.getElementById("prow_<?php echo $p_id; ?>").classList.remove("renew","deleting","undeleting"); // Remove the .renew class from the <tr> added by AJAX
    document.getElementById("changed_<?php echo $p_id; ?>").style.display = "none"; // Hide the "changed" clickable message added by AJAX
    document.getElementById("showaction<?php echo $p_id; ?>").style.display = "inline";
    document.getElementById("showtypify<?php echo $p_id; ?>").style.display = "none";
  }
  </script>
  <?php

  echo '</td>';

  // Type
  echo '<td onmouseover="showTypify'.$p_id.'()" onmouseout="showTypify'.$p_id.'()">
  <span id="ptype'.$p_id.'">'.$show_type.'</span><br><div id="showtypify'.$p_id.'" style="display: none;">';
  if ($p_type == 'page') {
    echo '<div id="r_make_'.$p_id.'">'.piecesform('make post', $p_id).'</div></div>';
  } else {
    echo '<div id="r_make_'.$p_id.'">'.piecesform('make page', $p_id).'</div></div>';
  }

  // JavaScript with unique function name per row, show/hide action links
  ?>
  <script>
  function showTypify<?php echo $p_id; ?>() {
    var x = document.getElementById("showtypify<?php echo $p_id; ?>");
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

}

echo "
  </tbody>
</table>
";

// Simple line
echo '<br><hr><br>';

// Footer
include ('./in.footer.php');
