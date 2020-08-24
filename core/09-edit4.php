<?php

// Include our config (with SQL) up near the top of our PHP file
include ('./in.config.php');

// Include our piece functions
include ('./in.piecefunctions.php');

// Include our login cluster
$head_title = "Editor"; // Set a <title> name used next
$edit_page_yn = true; // Include JavaScript for TinyMCE?
$nologin_allowed = false; // Login required?
include ('./in.login_check.php');

// Include our POST processor
include ('./in.editprocess.php');

// Our edit form
// New or update?
if (isset($piece_id)) { // Updating piece
  if ((isset($editing_published_piece)) && ($editing_published_piece == true)) {
    echo '<pre><a href="piece.php?p='.$piece_id.'" target="_blank">view on blog</a></pre>';
  } else {
    echo '<pre>(unpublished draft)</pre>';
  }
  echo '<pre><a href="piece.php?p='.$piece_id.'&preview" target="_blank">preview</a></pre>';
  echo '<form action="edit.php?p='.$piece_id.'" method="post" id="edit_piece">';
  echo '<input form="edit_piece" type="hidden" name="piece_id" value="'.$piece_id.'"><br>';
} else { // New piece
  echo '<form action="edit.php" method="post" id="edit_piece">';
}
// Finish the form
echo '</form>';

// Tell in.editprocess.php that this is a "Piece" form
echo '<input form="edit_piece" type="hidden" name="piece">';

// Title & Slug
echo 'Title: '.pieceInput('p_title', $p_title).'<br><br>';
echo 'Slug: '.pieceInput('p_slug', $p_slug).'<br><br>';

// Content
echo 'Content:<br>'.pieceInput('p_content', $p_content).'<br><br>';

// Two submit buttons
echo '<input form="edit_piece" type="submit" name="p_submit" value="Save draft">';
echo '&nbsp;'; // Space between the buttons
// Existing piece? (can't publish without saving once first)
if ((isset($editing_existing_piece)) && ($editing_existing_piece == true)) {
  // Editing a published piece?
  if ((isset($editing_published_piece)) && ($editing_published_piece == true)) {
    echo '<input form="edit_piece" type="submit" name="p_submit" value="Update">';
  } else {
    echo '<input form="edit_piece" type="submit" name="p_submit" value="Publish">';
  }
}
// New line
echo '<br><br>';

// Type
$infomsg = '
<b>Page</b>: hides meta (After, Tags, Links), works in menues, appears as prominent link in "Series lists"<br><br>
<b>Post</b>: appears in blog lists';
echo 'Type:'.infoPop('type_info', $infomsg).'<br>'.pieceInput('p_type', $p_type).'<br><br>';

// Series
$infomsg = 'Exclusive "category" -like label, Pieces of a Series may appear together in some areas';
echo 'Series:'.infoPop('series_info', $infomsg).'<br><br>';

  // Set necessary values
  // Set a default Series, probably from settings table
  $de_series = (isset($_SESSION['de_series'])) ? $_SESSION['de_series'] : 1;

  // Accept any set value
  $p_series = (isset($p_series)) ? $p_series : $de_series;
  include ('./in.series.php');

// Schedule
// Clickable <label for="CHECKBOX_ID"> doesn't work well with two "onClick" JavaScript functions, so we need extra JavaScript
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

// Tags
$infomsg = 'Tags: comma-separated list;<br>only first three tags show in excerpts & blog pages';
echo 'Tags:'.infoPop('tags_info', $infomsg).'<br>'.pieceInput('p_tags', $p_tags).'<br><br>';

// After
$infomsg = 'After: unstyled text, HTML not allowed';
echo 'After:'.infoPop('after_info', $infomsg).'<br>'.pieceInput('p_after', $p_after).'<br><br>';

// Links
$string1 = htmlspecialchars('<a href="https://inkisaverb.com">Ink is a verb.</a>');
$string2 = htmlspecialchars('<a href="https://verb.vip">Get inking. // VIP Linux</a>');
$string3 = htmlspecialchars('<a href="http://poetryiscode.com">Poetry is code. | piC</a>');
$a_tag = htmlspecialchars('<a>');
$infomsg =
"
<big>Links</big><br>
<code>
<b>1. Separate [url] [title] [credit] via ;;</b><br>
- In any order on a line ([title] before [credit])<br>
- Only [url] is required<br>
- If no [credit], Credit can be pulled after a | Pipe from [title]<br>
- All else after | Pipe gets truncated<br><br>
<b>2. Or se an HTML $a_tag tag</b><br>
- Title pulled after last | Pipe or // Doubleslash<br><br>
<b>Examples:</b><br>
https://verb.one<br>
https://verb.red ;;Get inking.<br>
https://verb.ink;; Ink is a verb.;;inkVerb<br>
https://verb.blue;; Inky | Blue Ink<br>
$string1<br>
$string2<br>
$string3<br>
</code>
";
echo 'Links:'.infoPop('links_info', $infomsg).'<br>'.pieceInput('p_links', $p_links).'<br><br>';

// Footer
include ('./in.footer.php');
