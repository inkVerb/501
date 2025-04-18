<?php
// No <head> yet because we might redirect, which uses header() and might break after the <head> tag

// Include our config (with SQL) up near the top of our PHP file
include ('./in.config.php');

// Include our piece functions
include ('./in.piecefunctions.php');

// Include our login cluster
$head_title = "Editor"; // Set a <title> name used next
include ('./in.logincheck.php');

// Include our POST processor
include ('./in.editprocess.php');

// Title the page so we know where we are

echo '<h1>Editor</h1>';

// Our edit form
// New or update?
if (isset($piece_id)) { // Updating piece
  echo '<form action="edit.php?p='.$piece_id.'" method="post">';
  echo '<input type="hidden" name="piece_id" value="'.$piece_id.'"><br>';
} else { // New piece
  echo '<form action="edit.php" method="post">';
}

// Tell in.editprocess.php that this is a "Piece" form
echo '<input type="hidden" name="piece"><br>';

// Create the fields
echo 'Title: '.pieceInput('p_title', $p_title).'<br><br>';
echo 'Slug: '.pieceInput('p_slug', $p_slug).'<br><br>';
echo 'Type:<br>'.pieceInput('p_type', $p_type).'<br><br>';
// Tip: Clickable <label for="CHECKBOX_ID"> doesn't work well with two "onClick" JavaScript functions, so we need extra JavaScript
echo pieceInput('p_live_schedule', $p_live_schedule).'<label onclick="showGoLiveOptionsLabel()"> Scheduled...</label><br><br>';
echo '<div id="goLiveOptions" '.($p_live_schedule == true ? 'style="display:block"' : 'style="display:none"').'>';
  echo 'Date live: '.
  pieceInput('p_live_yr', $p_live_yr).', '.
  pieceInput('p_live_mo', $p_live_mo).' '.
  pieceInput('p_live_day', $p_live_day).' @ '.
  pieceInput('p_live_hr', $p_live_hr).':'.
  pieceInput('p_live_min', $p_live_min).':'.
  pieceInput('p_live_sec', $p_live_sec).'<br><br>';
echo '
</div>
  <script>
  // Check/uncheck the box = hide/show the Date Live schedule (p_live_schedule) <div>
  function showGoLiveOptionsBox() {
    var x = document.getElementById("goLiveOptions");
    if (x.style.display === "block") {
      x.style.display = "none";
    } else {
      x.style.display = "block";
    }
  }
  // JavaScript does not allow onClick action for both the label and the checkbox
  // So, we make the label open the Date Live schedule div AND check the box...
  function showGoLiveOptionsLabel() {
    // Show the Date Live schedule div
    var x = document.getElementById("goLiveOptions");
    if (x.style.display === "block") {
      x.style.display = "none";
    } else {
      x.style.display = "block";
    }
    // Use JavaScript to check the box
    var y = document.getElementById("p_live_schedule");
    if (y.checked === false) {
      y.checked = true;
    } else {
      y.checked = false;
    }
  }
  </script>';

echo 'Content:<br>'.pieceInput('p_content', $p_content).'<br><br>';
echo 'After:<br>'.pieceInput('p_after', $p_after).'<br><br>';

// Two submit buttons
echo '<input type="submit" name="p_submit" value="Save">';
echo '</form>';

?>

</body>
</html>
