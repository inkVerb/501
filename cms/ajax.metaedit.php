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
    $piece_id_trim = DB::trimspace($piece_id);
  } elseif ( (isset($_POST['edit_piece']))
        &&   (filter_var($_POST['edit_piece'], FILTER_VALIDATE_INT)) ) {
    $piece_id = preg_replace("/[^0-9]/"," ", $_POST['edit_piece']);
    $piece_id_trim = DB::trimspace($piece_id);
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
  $p_slug_test_trim = DB::trimspace($p_slug);
  $query = $database->prepare("SELECT id FROM publications WHERE slug=:slug AND NOT piece_id=:piece_id");
  $query->bindParam(':slug', $p_slug_test_trim);
  $query->bindParam(':piece_id', $piece_id_trim);
  $pdo->exec_($query);
  if ($pdo->numrows == 1) {
    $add_num = 0;
    $dup = true;
    // If there were no changes
    while ($dup = true) {
      $add_num = $add_num + 1;
      $new_p_slug = $p_slug_test_trim.'-'.$add_num;

      // Check again
      $query = $database->prepare("SELECT id FROM publications WHERE slug=:slug AND NOT piece_id=:piece_id");
      $query->bindParam(':slug', $new_p_slug);
      $query->bindParam(':piece_id', $piece_id_trim);
      $pdo->exec_($query);
      if ($pdo->numrows == 0) {
        $p_slug = $new_p_slug;
        break;
      }
    }
  }

  // Date-time Live
  $p_live_schedule = checkPiece('p_live_schedule',$_POST['p_live_schedule']);
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
  $p_live_trim = DB::trimspace($p_live);

  // Series
  // Set a default Series, probably from settings table
  $de_series = (isset($_SESSION['de_series'])) ? $_SESSION['de_series'] : 1;
  if (filter_var($_POST['p_series'], FILTER_VALIDATE_INT)) {
    $p_series = $_POST['p_series'];
    $query = $database->prepare("SELECT id FROM series WHERE id='$p_series'");
    $pdo->select('series', 'id', $p_series, 'id');
    if ($pdo->numrows != 1) {
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
  $p_title_trim = DB::trimspace($p_title);
  $p_slug_trim = DB::trimspace($p_slug);
  $p_after_trim = DB::trimspace($p_after);
  $p_tags_sqljson = (json_decode($p_tags_json)) ? $p_tags_json : NULL; // We need JSON as is, no SQL-escape; run an operation, keep value if true, set NULL if false
  $p_links_sqljson = (json_decode($p_links_json)) ? $p_links_json : NULL; // We need JSON as is, no SQL-escape; run an operation, keep value if true, set NULL if false

  // Prepare our response
  $ajax_response = array();
  $ajax_response['title'] = $p_title;

  // Check for differences in the database
  $querys = $database->prepare("SELECT id FROM pieces WHERE id=:id
  AND BINARY title=:title
  AND BINARY slug=:slug
  AND BINARY after=:after
  AND tags=:tags
  AND links=:links
  AND BINARY date_live=:date_live");
  $querys->bindParam(':id', $piece_id_trim);
  $querys->bindParam(':title', $p_title_trim);
  $querys->bindParam(':slug', $p_slug_trim);
  $querys->bindParam(':after', $p_after_trim);
  $querys->bindParam(':tags', $p_tags_sqljson);
  $querys->bindParam(':links', $p_links_sqljson);
  $querys->bindParam(':date_live', $p_live_trim);
  $pdo->exec_($querys);
  // If there is no match
  if ($pdo->numrows == 0) {

    //  Schedule?
    if ($p_live_schedule == false) { // No empty live date for publishing pieces
      $queryu = $database->prepare("UPDATE pieces SET series=:series, title=:title, slug=:slug, after=:after, tags=:tags, links=:links, date_updated=NOW() WHERE id=:id");
      $queryu->bindParam(':series', $p_series);
      $queryu->bindParam(':title', $p_title_trim);
      $queryu->bindParam(':slug', $p_slug_trim);
      $queryu->bindParam(':after', $p_after_trim);
      $queryu->bindParam(':tags', $p_tags_sqljson);
      $queryu->bindParam(':links', $p_links_sqljson);
      $queryu->bindParam(':id', $piece_id_trim);
    } elseif ($p_live_schedule == true) { // Unscheduled publish goes live now
      $queryu = $database->prepare("UPDATE pieces SET series=:, title=:title, slug=:slug, after=:after, tags=:tags, links=:links, date_live=:date_live, date_updated=NOW() WHERE id=:id");
      $queryu->bindParam(':series', $p_series);
      $queryu->bindParam(':title', $p_title_trim);
      $queryu->bindParam(':slug', $p_slug_trim);
      $queryu->bindParam(':after', $p_after_trim);
      $queryu->bindParam(':tags', $p_tags_sqljson);
      $queryu->bindParam(':links', $p_links_sqljson);
      $queryu->bindParam(':date_live', $p_live_trim);
      $queryu->bindParam(':id', $piece_id_trim);
    }

    // Run our pieces UPDATE
    $pdo->exec_($queryu);
    $callu = $pdo->ok;

        // publications UPDATE?
    if ($p_pubyn == 'pre-draft') {
      $callp = true; // We have a test later
      $ajax_response['message'] = 'pre-draft saved';
    } elseif ($p_pubyn == 'published') {
      $queryp = $database->prepare("UPDATE publications SET pubstatus='published', series=:series, title=:title, slug=:slug, content=:content, after=:after, tags=:tags, links=:links, date_live=:date_live, date_updated=NOW() WHERE piece_id=:piece_id");
      $queryp->bindParam(':series', $p_series);
      $queryp->bindParam(':title', $p_title_trim);
      $queryp->bindParam(':slug', $p_slug_trim);
      $queryp->bindParam(':content', $p_content_trim);
      $queryp->bindParam(':after', $p_after_trim);
      $queryp->bindParam(':tags', $p_tags_sqljson);
      $queryp->bindParam(':links', $p_links_sqljson);
      $queryp->bindParam(':date_live', $p_live_trim);
      $queryp->bindParam(':piece_id', $piece_id_trim);
      $pdo->exec_($queryp);
      $callp = $pdo->ok;
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
  $query = $database->prepare("SELECT id FROM publications WHERE piece_id=:piece_id AND pubstatus='published'");
  $query->bindParam(':piece_id', $piece_id_trim);
  $pdo->exec_($query);
  // Shoule be 1 row
  if ($pdo->numrows == 1) {
    $editing_published_piece = true;
  }

  // Retrieve existing piece
  $rows = $pdo->select('pieces', 'id', $piece_id_trim, 'pub_yn, series, title, slug, after, tags, links, date_live');
  // Shoule be 1 row
  if ($pdo->numrows == 1) {
    foreach ($rows as $row){
      // Assign the values
      $p_pubyn = "$row->pub_yn";
      $p_series = "$row->series";
      $p_title = "$row->title";
      $p_slug = "$row->slug";
      $p_after = "$row->after";
      $p_tags_json = "$row->tags";
      $p_links_sqljson = "$row->links";
      $p_live = "$row->date_live";
    }
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
  $rows = $pdo->exec_($database->prepare("SELECT id, name FROM series"));

  // Start the select input
  // We need the div with our AJAX form inside so the input value is reset on success
  echo '
  <div id="p_series'.$piece_id.'">
  <select form="meta_edit_form_'.$piece_id.'" name="p_series">';
    // Iterate each Series
    foreach ($rows as $row) {
      $s_id = "$row->id";
      $s_name = "$row->name";
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
