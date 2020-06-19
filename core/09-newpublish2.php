<?php
// No <head> yet because we might redirect, which uses header() and might break after the <head> tag

// Include our config (with SQL) up near the top of our PHP file
include ('./in.config.php');
$nologin_allowed = false; // Login requires this page
include ('./in.login_check.php');

// Must be logged in
if (!isset($_SESSION['user_id'])) {
  exit(header("Location: blog.php"));
}

// Publish preparation
if ((isset($_GET['p'])) && (filter_var($_GET['p'], FILTER_VALIDATE_INT))) {
  // Set $piece_id via sanitize non-numbers
  $piece_id = preg_replace("/[^0-9]/"," ", $_GET['p']);

  // Look for a publications piece, regardless of what happens, before anything else happens
  $query = "SELECT id FROM publications WHERE piece_id='$piece_id'";
  $call = mysqli_query($database, $query);
  // Shoule be 1 row
  if (mysqli_num_rows($call) == 1) {
    $editing_published_piece = true;
  }

  // Retrieve existing piece
  $query = "SELECT type, status, title, slug, content, after, date_live FROM pieces WHERE id='$piece_id'";
  $call = mysqli_query($database, $query);
  // Shoule be 1 row
  if (mysqli_num_rows($call) == 1) {
    // Assign the values
    $row = mysqli_fetch_array($call, MYSQLI_NUM);
      $p_type = "$row[0]";
      $p_status = "$row[1]";
      $p_title = "$row[2]";
      $p_slug = "$row[3]";
      $p_content = "$row[4]";
      $p_after = "$row[5]";
      $p_live = "$row[6]";

    // We are editing a piece that has been saved, publication is allowed
    $editing_existing_piece = true;

    } else {
      // ID does not match, redirect to blank editor
      header("Location: edit.php");
      exit();
    }

    // Parse $p_live
    // Test our $p_live when converting it to the epoch
    if ($p_live_epoch = strtotime($p_live)) { // Our accepted timestamp format
      $p_live_schedule = "<input type=\"hidden\" name=\"p_live_schedule\">";
      // Send to it's variables
      $p_live_yr = date("Y", $p_live_epoch);
      $p_live_mo = date("m", $p_live_epoch);
      $p_live_day = date("d", $p_live_epoch);
      $p_live_hr = date("H", $p_live_epoch);
      $p_live_min = date("i", $p_live_epoch);
      $p_live_sec = date("s", $p_live_epoch);

    } else { // Not our format, probably NULL, do not schedule
      $p_live_schedule = false;
    }

    // Prepare a POST to send back so in.editprocess.php understands
    // (These are all the fields of a normal form, but it automatically sends via JavaScript)
    echo "
    <form id=\"jsAutoPOST\" action=\"newpublish.php\" method=\"post\">
      <input type=\"hidden\" name=\"piece_id\" value=\"$piece_id\">
      <input type=\"hidden\" name=\"piece\">
      <input type=\"hidden\" name=\"p_title\" value=\"$p_title\">
      <input type=\"hidden\" name=\"p_slug\" value=\"$p_slug\">
      <input type=\"hidden\" name=\"p_type\" value=\"$p_type\">
      $p_live_schedule
      <input type=\"hidden\" name=\"p_live_yr\" value=\"$p_live_yr\">
      <input type=\"hidden\" name=\"p_live_mo\" value=\"$p_live_mo\">
      <input type=\"hidden\" name=\"p_live_day\" value=\"$p_live_day\">
      <input type=\"hidden\" name=\"p_live_hr\" value=\"$p_live_hr\">
      <input type=\"hidden\" name=\"p_live_min\" value=\"$p_live_min\">
      <input type=\"hidden\" name=\"p_live_sec\" value=\"$p_live_sec\">
      <input type=\"hidden\" name=\"p_content\" value=\"$p_content\">
      <input type=\"hidden\" name=\"p_after\" value=\"$p_after\">
      <input type=\"hidden\" name=\"p_submit\" value=\"Publish\">
    </form>

    <script type=\"text/javascript\">
        document.getElementById('jsAutoPOST').submit();
    </script>";


} elseif (($_SERVER['REQUEST_METHOD'] === 'POST') && (isset($_POST['piece']))) {

  // Include our piece functions
  include ('./in.piecefunctions.php');

  // Run our edit POST processor
  include ('./in.editprocess.php');

  // Redirect to the Pieces page
  exit(header("Location: pieces.php"));

} else {
  exit(header("Location: blog.php"));
}
