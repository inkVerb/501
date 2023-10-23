<?php

// Include our config (with SQL) up near the top of our PHP file
include_once ('./in.db.php');

// Include our piece functions
include_once ('./in.piecefunctions.php');

// Require login
if (!isset($_SESSION['user_id'])) {
  exit ();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if  ( (isset($_POST['p_id']))
  &&   (filter_var($_POST['p_id'], FILTER_VALIDATE_INT)) ) {
    $piece_id = preg_replace("/[^0-9]/"," ", $_POST['p_id']);
    $piece_id_sqlesc = escape_sql($piece_id);
  } elseif ( (isset($_POST['edit_piece']))
        &&   (filter_var($_POST['edit_piece'], FILTER_VALIDATE_INT)) ) {
    $piece_id = preg_replace("/[^0-9]/"," ", $_POST['edit_piece']);
    $piece_id_sqlesc = escape_sql($piece_id);
  } else {
    exit ();
  }
} else {
  exit ();
}

// Save
if ( (isset($_POST['edit_piece']))
&&   (filter_var($_POST['edit_piece'], FILTER_VALIDATE_INT)) ) {

  // Pub Status
  if ( ($_POST['p_pubyn'] != 'published') && ($_POST['p_pubyn'] != 'pre-draft') ) {
    echo '<b class="error">impossible error</b>';
    exit ();
  }
  $p_pubyn = ($_POST['p_pubyn'] == 'published') ? $_POST['p_pubyn'] : 'pre-draft';

  // Title now because we will use it in the Slug
  $p_title = checkPiece('p_title',$_POST['p_title']);

  // Apply Title to Slug if empty
  if (empty($_POST['p_slug'])) {
    $p_slug = checkPiece('p_slug',$p_title);
  } else {
    $p_slug = checkPiece('p_slug',$_POST['p_slug']);
  }
  // Check that the slug isn't already used
  $p_slug_test_sqlesc = escape_sql($p_slug);
  $query = "SELECT id FROM publications WHERE slug='$p_slug_test_sqlesc' AND NOT piece_id='$piece_id_sqlesc'";
  $call = mysqli_query($database, $query);
  if (mysqli_num_rows($call) == 1) {
    $add_num = 0;
    $dup = true;
    // If there were no changes
    while ($dup = true) {
      $add_num = $add_num + 1;
      $new_p_slug = $p_slug_test_sqlesc.'-'.$add_num;

      // Check again
      $query = "SELECT id FROM publications WHERE slug='$new_p_slug' AND NOT piece_id='$piece_id_sqlesc'";
      $call = mysqli_query($database, $query);
      if (mysqli_num_rows($call) == 0) {
        $p_slug = $new_p_slug;
        break;
      }
    }
  }

  // Date-time Live
  $p_live_schedule = (isset($_POST['p_live_schedule'])) ? checkPiece('p_live_schedule',$_POST['p_live_schedule']) : false ;
  if ($p_live_schedule == true) {
    $p_live_yr = checkPiece('p_live_yr',$_POST['p_live_yr']);
    $p_live_mo = checkPiece('p_live_mo',$_POST['p_live_mo']);
    $p_live_day = checkPiece('p_live_day',$_POST['p_live_day']);
    $p_live_hr = checkPiece('p_live_hr',$_POST['p_live_hr']);
    $p_live_min = checkPiece('p_live_min',$_POST['p_live_min']);
    $p_live_sec = checkPiece('p_live_sec',$_POST['p_live_sec']);
    $p_live = "$p_live_yr-$p_live_mo-$p_live_day $p_live_hr:$p_live_min:$p_live_sec";
  } else {
    $p_live = date("Y-m-d H:i:s");
  }
  $p_live_sqlesc = escape_sql($p_live);

  // Series
  // Set a default Series, probably from settings table
  $de_series = (isset($_SESSION['de_series'])) ? $_SESSION['de_series'] : 1;
  if (filter_var($_POST['p_series'], FILTER_VALIDATE_INT)) {
    $p_series = $_POST['p_series'];
    $query = "SELECT id FROM series WHERE id='$p_series'";
    $call = mysqli_query($database, $query);
    if (mysqli_num_rows($call) != 1) {
      $p_series = $de_series;
    }
  } else {
    $p_series = $de_series;
  }

  // All other fields
  $p_after = checkPiece('p_after',$_POST['p_after']);
  $p_tags_json = checkPiece('p_tags',$_POST['p_tags']);
  $p_links_json = checkPiece('p_links',$_POST['p_links']);

  // Prepare our database values for entry
  $p_title_sqlesc = escape_sql($p_title);
  $p_slug_sqlesc = escape_sql($p_slug);
  $p_after_sqlesc = escape_sql($p_after);
  $p_tags_sqljson = (json_decode($p_tags_json)) ? $p_tags_json : NULL; // We need JSON as is, no SQL-escape; run an operation, keep value if true, set NULL if false
  $p_links_sqljson = (json_decode($p_links_json)) ? $p_links_json : NULL; // We need JSON as is, no SQL-escape; run an operation, keep value if true, set NULL if false

  // Prepare our response
  $ajax_response = array();
  $ajax_response['title'] = $p_title;

  // Check for differences in the database
  $querys = "SELECT id FROM pieces WHERE id='$piece_id_sqlesc'
  AND BINARY title='$p_title_sqlesc'
  AND BINARY slug='$p_slug_sqlesc'
  AND BINARY after='$p_after_sqlesc'
  AND tags='$p_tags_sqljson'
  AND links='$p_links_sqljson'
  AND BINARY date_live='$p_live_sqlesc'";
  $calls = mysqli_query($database, $querys);
  // If there is no match
  if (mysqli_num_rows($calls) == 0) {

    //  Schedule?
    if ($p_live_schedule == false) { // No empty live date for publishing pieces
      $queryu = "UPDATE pieces SET series=$p_series, title='$p_title_sqlesc', slug='$p_slug_sqlesc', after='$p_after_sqlesc', tags='$p_tags_sqljson', links='$p_links_sqljson', date_updated=NOW() WHERE id='$piece_id_sqlesc'";
    } elseif ($p_live_schedule == true) { // Unscheduled publish goes live now
      $queryu = "UPDATE pieces SET series=$p_series, title='$p_title_sqlesc', slug='$p_slug_sqlesc', after='$p_after_sqlesc', tags='$p_tags_sqljson', links='$p_links_sqljson', date_live='$p_live_sqlesc', date_updated=NOW() WHERE id='$piece_id_sqlesc'";
    }

    // Run our pieces UPDATE
    $callu = mysqli_query($database, $queryu);

        // publications UPDATE?
    if ($p_pubyn == 'pre-draft') {
      $callp = true; // We have a test later
      $ajax_response['message'] = 'pre-draft saved';
    } elseif ($p_pubyn == 'published') {
      $queryp = "UPDATE publications SET pubstatus='published', series=$p_series, title='$p_title_sqlesc', slug='$p_slug_sqlesc', content='$p_content_sqlesc', after='$p_after_sqlesc', tags='$p_tags_sqljson', links='$p_links_sqljson', date_live='$p_live_sqlesc', date_updated=NOW() WHERE piece_id='$piece_id_sqlesc'";
      $callp = mysqli_query($database, $queryp);
      $ajax_response['message'] = 'piece updated';
    }

    // Test the query
    if ( (!$callp) || (!$callu) ) { // SQL error
       $ajax_response['message'] = '<span class="error">serious SQL error</span>';
    }

  } else { // No changes
    $ajax_response['message'] = 'no change';
  }

  $json_response = json_encode($ajax_response, JSON_FORCE_OBJECT);

  // We're done here
  echo $json_response;
  exit ();

// Editing
} elseif (isset($_POST['p_id'])) {

  // Look for a publications piece, regardless of what happens, before anything else happens
  $query = "SELECT id FROM publications WHERE piece_id='$piece_id_sqlesc' AND pubstatus='published'";
  $call = mysqli_query($database, $query);
  // Shoule be 1 row
  if (mysqli_num_rows($call) == 1) {
    $editing_published_piece = true;
  }

  // Retrieve existing piece
  $query = "SELECT pub_yn, series, title, slug, after, tags, links, date_live FROM pieces WHERE id='$piece_id_sqlesc'";
  $call = mysqli_query($database, $query);
  // Shoule be 1 row
  if (mysqli_num_rows($call) == 1) {
    // Assign the values
    $row = mysqli_fetch_array($call, MYSQLI_NUM);
      $p_pubyn = "$row[0]";
      $p_series = "$row[1]";
      $p_title = "$row[2]";
      $p_slug = "$row[3]";
      $p_after = "$row[4]";
      $p_tags_json = "$row[5]";
      $p_links_sqljson = "$row[6]";
      $p_live = "$row[7]";

      // Pub Status
      $p_pubyn = ($p_pubyn == true) ? 'published' : 'pre-draft';

      // Process tags for use in HTML
      $p_tags = implode(', ', json_decode($p_tags_json, true));

      // Process links for use in HTML
      if ($p_links_sqljson != '[""]') {$links_array = json_decode($p_links_sqljson);}
      // Only if we actually have links
      if (!empty($links_array)) {
        $links = ''; // Start the $links set
        foreach ($links_array as $line_item) {
          $link_item = $line_item[0].' ;; '.$line_item[1].' ;; '.$line_item[2];
          $links .= $link_item."\n";
        }
        // Set our final value
        $p_links = $links;
      }

      // Parse $p_live
      // Test our $p_live when converting it to the epoch
      if ($p_live_epoch = strtotime($p_live)) { // Our accepted timestamp format
        $p_live_schedule = true;
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

    } else {
      echo '<b class="error">serious database error!</b>';
    }

  // Our pieceInput globals
  $edit_piece_id = $piece_id;
  $form_id = 'meta_edit_form_';

  // Title & <tr> row
  echo '<tr><td>';
  echo '<b class="piece_title" onclick="metaEditClose('.$piece_id.')" style="cursor: pointer;">'.$p_title.'  &#9998;</b><br><br>';

  // Meta Edit form
  echo '<form action="AJAX" class="metaedit" method="post" id="meta_edit_form_'.$piece_id.'">';
  echo '<input form="meta_edit_form_'.$piece_id.'" type="hidden" name="edit_piece" value="'.$piece_id.'">';
  echo '<input form="meta_edit_form_'.$piece_id.'" type="hidden" name="p_pubyn" value="'.$p_pubyn.'">';
  echo '</form>';

  // Title & Slug
  echo '<label class="metaedit">Title: '.pieceInput('p_title_me', $p_title).'<br><br></label>';
  echo '<label class="metaedit">Slug: '.pieceInput('p_slug_me', $p_slug).'<br><br></label>';

 // Series
  echo '<label class="metaedit">Series:<br></label>';
  // Query the Serieses
  $query = "SELECT id, name FROM series";
  $call = mysqli_query($database, $query);

  // Start the select input
  // We need the div with our AJAX form inside so the input value is reset on success
  echo '
  <div id="p_series'.$piece_id.'">
  <select form="meta_edit_form_'.$piece_id.'" name="p_series">';
    // Iterate each Series
    while ($row = mysqli_fetch_array($call, MYSQLI_NUM)) {
      $s_id = "$row[0]";
      $s_name = "$row[1]";
      $selected_yn = ($p_series == $s_id) ? ' selected="selected"' : ''; // So 'selected="selected"' appears in the current Series
      echo '<option value="'.$s_id.'"'.$selected_yn.'>'.$s_name.'</option>';
    }
  echo '</select>';

  // Next cell
  echo '<br><br>';

  // Tags
  echo '<label class="metaedit">Tags:<br></label>'.pieceInput('p_tags', $p_tags).'<br><br>';

  // Schedule
  // Clickable <label for="CHECKBOX_ID"> doesn't work well with two "onClick" JavaScript functions, so we need extra JavaScript
  echo pieceInput('p_live_schedule', $p_live_schedule).'<label class="metaedit" onclick="showGoLiveOptionsLabel('.$piece_id.')"> Scheduled...</label><br><br>';
  echo '<div id="goLiveOptions'.$piece_id.'" '.($p_live_schedule == true ? 'style="display:block"' : 'style="display:none"').'>';
    echo 'Date live:<br><br>'.
    pieceInput('p_live_yr', $p_live_yr).', '.
    pieceInput('p_live_mo', $p_live_mo).' '.
    pieceInput('p_live_day', $p_live_day).'<br><br>@ '.
    pieceInput('p_live_hr', $p_live_hr).':'.
    pieceInput('p_live_min', $p_live_min).':'.
    pieceInput('p_live_sec', $p_live_sec).'<br><br>';
  echo '
  </div>';

  // Next cell
  echo '</td><td colspan="2">';

  // Buttons
  echo '<input form="meta_edit_form_'.$piece_id.'" type="submit" name="p_submit" value="Update">&nbsp;&nbsp;';
  echo '<button onclick="metaEditClose('.$piece_id.');">Cancel</button>';
  echo '<br><br>';

  // After
  echo '<label class="metaedit">After:<br></label>'.pieceInput('p_after_me', $p_after).'<br><br>';

  // Links
  echo '<label class="metaedit">Links: <code class="gray">https://verb.ink ;; Title ;; Credit<br></code><br></label>'.pieceInput('p_links_me', $p_links).'<br><br>';

  // Finish the row
  echo '</td></tr>';

} else {
  exit ();
}
