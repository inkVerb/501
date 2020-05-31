<?php

if (($_SERVER['REQUEST_METHOD'] === 'POST') && (isset($_POST['piece']))) {

  // POST checks
    // Only sanitize, no errors

  // Title now because we will use it in the Slug
  if ((isset($_POST['p_title'])) && ($_POST['p_title'] != '')) {
    $p_title = checkPiece('p_title',$_POST['p_title']);
  }

  // Apply Title to Slug if empty
  if (empty($_POST['p_slug'])) {
    $p_slug = checkPiece('p_slug',$p_title);
  } else {
    $p_slug = checkPiece('p_slug',$_POST['p_slug']);
  }

  // Date-time Live
  $p_live_schedule = checkPiece('p_live_schedule',$_POST['p_live_schedule']);
  $p_live_yr = checkPiece('p_live_yr',$_POST['p_live_yr']);
  $p_live_mo = checkPiece('p_live_mo',$_POST['p_live_mo']);
  $p_live_day = checkPiece('p_live_day',$_POST['p_live_day']);
  $p_live_hr = checkPiece('p_live_hr',$_POST['p_live_hr']);
  $p_live_min = checkPiece('p_live_min',$_POST['p_live_min']);
  $p_live_sec = checkPiece('p_live_sec',$_POST['p_live_sec']);

  // Status ("Save draft" = INSERT/UPDATE with status='draft' only)
  if ($_POST['p_submit'] == 'Save draft') {
    $p_status = 'draft';
  } elseif ($_POST['p_submit'] == 'Publish') {
    $p_status = checkPiece('p_status',$_POST['p_status']);
  }

  // All other fields
  $p_type = checkPiece('p_type',$_POST['p_type']);
  $p_content = checkPiece('p_content',$_POST['p_content']);
  $p_after = checkPiece('p_after',$_POST['p_after']);

  // Prepare our database values for entry
  $p_type_sqlesc = escape_sql($p_type);
  $p_status_sqlesc = escape_sql($p_status);
  $p_title_sqlesc = escape_sql($p_title);
  $p_slug_sqlesc = escape_sql($p_slug);
  $p_content_sqlesc = escape_sql($p_content);
  $p_after_sqlesc = escape_sql($p_after);

  // New piece
  // Create our timestamp
  if ($p_live_schedule == true) { // Keep it unset
    $p_live = NULL;
    $query = "INSERT INTO pieces (type, status, title, slug, content, after, date_live) VALUES ('$p_type_sqlesc', '$p_status_sqlesc', '$p_title_sqlesc', '$p_slug_sqlesc', '$p_content_sqlesc', '$p_after_sqlesc')";
  } elseif ($p_live_schedule == false) {
    $p_live = "$p_live_yr-$p_live_mo-$p_live_day $p_live_hr:$p_live_min:$p_live_sec";
    $p_live_sqlesc = escape_sql($p_live);
    $query = "INSERT INTO pieces (type, status, title, slug, content, after, date_live) VALUES ('$p_type_sqlesc', '$p_status_sqlesc', '$p_title_sqlesc', '$p_slug_sqlesc', '$p_content_sqlesc', '$p_after_sqlesc', '$p_live_sqlesc')";
  }
  // Updating piece
  //$query = "UPDATE pieces SET type='$p_type_sqlesc', status='$p_status_sqlesc', title='$p_title_sqlesc', slug='$p_slug_sqlesc', content='$p_content_sqlesc', after='$p_after_sqlesc', date_live='$p_live_sqlesc', date_updated='0' WHERE id='$p_id'";

  // Run the query
  $call = mysqli_query($database, $query);
  // Test the query
  if ($call) {
    // Change
    if (mysqli_affected_rows($database) == 1) {
      echo '<p class="green">Saved!</p>';
    // No change
    } elseif (mysqli_affected_rows($database) == 0) {
      echo '<p class="orange">Error, not saved.</p>';
    }
  } else {
    echo '<p class="error">Serious error.</p>';
  }


} // Finish POST if
