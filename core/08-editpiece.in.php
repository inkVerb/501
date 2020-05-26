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
  $p_live_now = checkPiece('p_live_now',$_POST['p_live_now']);
  $p_live_yr = checkPiece('p_live_yr',$_POST['p_live_yr']);
  $p_live_mo = checkPiece('p_live_mo',$_POST['p_live_mo']);
  $p_live_day = checkPiece('p_live_day',$_POST['p_live_day']);
  $p_live_hr = checkPiece('p_live_hr',$_POST['p_live_hr']);
  $p_live_min = checkPiece('p_live_min',$_POST['p_live_min']);
  $p_live_sec = checkPiece('p_live_sec',$_POST['p_live_sec']);

  // All other fields
  $p_type = checkPiece('p_type',$_POST['p_type']);
  $p_status = checkPiece('p_status',$_POST['p_status']);
  $p_content = checkPiece('p_content',$_POST['p_content']);
  $p_after = checkPiece('p_after',$_POST['p_after']);


XX Pick up here...
- Check non-date fields such as htmlentities
- All is done, but only for INSERT
  - we need a separate file for UPDATE

  // Create our time
  //DEV// This should work:
  $p_live = "$p_live_yr-$p_live_mo-$p_live_day $p_live_hr:$p_live_min:$p_live_sec";
  /* ...if not...
  if ($p_live_now == true) {
    $p_live = date("Y-m-d H:i:s");
  } else {
    $p_live = "$p_live_yr-$p_live_mo-$p_live_day $p_live_hr:$p_live_min:$p_live_sec";
  }
  */

  // Prepare our database values for entry

  $p_type_sqlesc = escape_sql($p_type);
  $p_status_sqlesc = escape_sql($p_status);
  $p_title_sqlesc = escape_sql($p_title);
  $p_slug_sqlesc = escape_sql($p_slug);
  $p_content_sqlesc = escape_sql($p_content);
  $p_after_sqlesc = escape_sql($p_after);
  $p_live_sqlesc = escape_sql($p_live);

  // New piece
  $query = "INSERT INTO pieces (type, status, title, slug, content, after, date_live) VALUES ('$p_type_sqlesc', '$p_status_sqlesc', '$p_title_sqlesc', '$p_slug_sqlesc', '$p_content_sqlesc', '$p_after_sqlesc', '$p_live_sqlesc')";

  // Updating piece
  $query = "UPDATE pieces SET type='$p_type_sqlesc', status='$p_status_sqlesc', title='$p_title_sqlesc', slug='$p_slug_sqlesc', content='$p_content_sqlesc', after='$p_after_sqlesc', date_live='$p_live_sqlesc', date_updated='0' WHERE id='$p_id'";


  // Run the query
  $call = mysqli_query($database, $query);
  // Test the query
  if ($call) {
    // Change
    if (mysqli_affected_rows($database) == 1) {
      echo '<p class="green">Saved!</p>';
    // No change
    } elseif (mysqli_affected_rows($database) == 0) {
      echo '<p class="orange">No change.</p>';
    }
  } else {
    echo '<p class="error">Serious error.</p>';
  }


} // Finish POST if
