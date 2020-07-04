<?php

// Include our config (with SQL) up near the top of our PHP file
include_once ('./in.config.php');

// Include our sitewide functions
include_once ('./in.functions.php');

// Include our piece functions
include_once ('./in.piecefunctions.php');

// Require login
if (!isset($_SESSION['user_id'])) {
  exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if  ( (isset($_POST['p_id']))
  &&   (filter_var($_POST['p_id'], FILTER_VALIDATE_INT)) ) {
    $piece_id = preg_replace("/[^0-9]/"," ", $_POST['p_id']);
  } elseif ( (isset($_POST['edit_piece']))
        &&   (filter_var($_POST['edit_piece'], FILTER_VALIDATE_INT)) ) {
    $piece_id = preg_replace("/[^0-9]/"," ", $_POST['edit_piece']);
  } else {
    exit();
  }
} else {
  exit();
}

// Save
if (isset($_POST['edit_piece'])) {

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
  if (isset($piece_id)) { // We don't want a dup from for own piece
    $query = "SELECT id FROM publications WHERE slug='$p_slug_test_sqlesc' AND NOT piece_id='$piece_id'";
  } else {
    $query = "SELECT id FROM publications WHERE slug='$p_slug_test_sqlesc'";
  }
  $call = mysqli_query($database, $query);
  if (mysqli_num_rows($call) == 1) {
    $add_num = 0;
    $dup = true;
    // If there were no changes
    while ($dup = true) {
      $add_num = $add_num + 1;
      $new_p_slug = $p_slug_test_sqlesc.'-'.$add_num;

      // Check again
      if (isset($piece_id)) { // We don't want a dup from for own piece
        $query = "SELECT id FROM publications WHERE slug='$new_p_slug' AND NOT piece_id='$piece_id'";
      } else {
        $query = "SELECT id FROM publications WHERE slug='$new_p_slug'";
      }
      $call = mysqli_query($database, $query);
      if (mysqli_num_rows($call) == 0) {
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
  } else {
    $p_live = date("Y-m-d H:i:s");
  }

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

  // Tags & Links
  $p_tags_json = checkPiece('p_tags',$_POST['p_tags']);
  $p_links_json = checkPiece('p_links',$_POST['p_links']);

  // Prepare our database values for entry
  $p_title_sqlesc = escape_sql($p_title);
  $p_slug_sqlesc = escape_sql($p_slug);
  $p_after_sqlesc = escape_sql($p_after);
  $p_tags_sqljson = (json_decode($p_tags_json)) ? $p_tags_json : NULL; // We need JSON as is, no SQL-escape; run an operation, keep value if true, set NULL if false
  $p_links_sqljson = (json_decode($p_links_json)) ? $p_links_json : NULL; // We need JSON as is, no SQL-escape; run an operation, keep value if true, set NULL if false

  if ($p_status == 'draft') { // No empty live date for publishing pieces
    $p_live = ($p_live_schedule == true) ? $p_live_sqlesc = escape_sql("$p_live_yr-$p_live_mo-$p_live_day $p_live_hr:$p_live_min:$p_live_sec") : NULL;
    $queryu = "UPDATE pieces SET series=$p_series, title='$p_title_sqlesc', slug='$p_slug_sqlesc', after='$p_after_sqlesc', tags='$p_tags_sqljson', links='$p_links_sqljson', date_live=NULL, date_updated=NOW() WHERE id='$piece_id'";
  } elseif (($p_status == 'publish') || ($p_status == 'update')) { // Unscheduled publish goes live now
    $p_live = ($p_live_schedule == true) ? "$p_live_yr-$p_live_mo-$p_live_day $p_live_hr:$p_live_min:$p_live_sec" : "$p_live";
    $p_live_schedule = true;
    $p_live_sqlesc = escape_sql($p_live);
    $queryu = "UPDATE pieces SET series=$p_series, title='$p_title_sqlesc', slug='$p_slug_sqlesc', after='$p_after_sqlesc', tags='$p_tags_sqljson', links='$p_links_sqljson', date_live='$p_live_sqlesc', date_updated=NOW() WHERE id='$piece_id'";
  }

  // Update the database
  $querys = "SELECT id FROM publications WHERE BINARY piece_id='$piece_id'
  AND BINARY type='$p_type_sqlesc'
  AND BINARY title='$p_title_sqlesc'
  AND BINARY slug='$p_slug_sqlesc'
  AND BINARY after='$p_after_sqlesc'
  AND tags=CAST('$p_tags_sqljson' AS JSON),
  AND links=CAST('$p_links_sqljson' AS JSON),
  AND BINARY date_live='$p_live_sqlesc'";
  $calls = mysqli_query($database, $querys);
  // If there were no changes
  if (mysqli_num_rows($calls) == 0) {

    // Run our pieces UPDATE
    $callu = mysqli_query($database, $queryu);

    // publications UPDATE?
    if ($pubstatus == 'none') {
      $callp = true; // We have a test later
      $publication_message = 'Pre-draft saved';
    } elseif ( ($pubstatus = 'published') || ($pubstatus = 'redrafting') ) {
      $query = "UPDATE publications SET type='$p_type_sqlesc', pubstatus='published', series=$p_series, title='$p_title_sqlesc', slug='$p_slug_sqlesc', content='$p_content_sqlesc', after='$p_after_sqlesc', tags='$p_tags_sqljson', links='$p_links_sqljson', date_live='$p_live_sqlesc', date_updated=NOW() WHERE piece_id='$piece_id'";
      $callp = mysqli_query($database, $query);

      $publication_message = 'Publication updated';
    }

    // Test the query
    if (($callp) && ($callu)) {
       echo $publication_message;
    } else {
      echo '<b class="error">Serious error</b>';
      exit();
    }
  } else {
    echo 'No change to publication';
  }

// Editing
} elseif (isset($_POST['p_id'])) {


  // Look for a publications piece, regardless of what happens, before anything else happens
  $query = "SELECT id FROM publications WHERE piece_id='$piece_id' AND pubstatus='published'";
  $call = mysqli_query($database, $query);
  // Shoule be 1 row
  if (mysqli_num_rows($call) == 1) {
    $editing_published_piece = true;
  }

  // Retrieve existing piece
  $query = "SELECT status, title, slug, after, tags, links, date_live FROM pieces WHERE id='$piece_id'";
  $call = mysqli_query($database, $query);
  // Shoule be 1 row
  if (mysqli_num_rows($call) == 1) {
    // Assign the values
    $row = mysqli_fetch_array($call, MYSQLI_NUM);
      $p_status = "$row[0]";
      $p_title = "$row[1]";
      $p_slug = "$row[2]";
      $p_after = "$row[3]";
      $p_tags_json = "$row[4]";
      $p_links_sqljson = "$row[5]";
      $p_live = "$row[6]";

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

    } else {
      echo '<b class="error">Serious database error!</b>';
    }
















  // Our Meta Edit form
  echo '<form action="AJAX" method="post" id="meta_edit_form_'.$piece_id.'">';
  echo '<input form="edit_piece" type="hidden" name="piece_id" value="'.$piece_id.'"><br>';

  // Title & Slug
  echo 'Title: '.pieceInput('p_title', $p_title).'<br><br>';
  echo 'Slug: '.pieceInput('p_slug', $p_slug).'<br><br>';

  // Series
  $infomsg = 'Exclusive "category" -like label, Pieces of a Series may appear together in some areas';
  echo 'Series:';

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
  echo 'Tags:<br>'.pieceInput('p_tags', $p_tags).'<br><br>';

  // After
  echo 'After:<br>'.pieceInput('p_after', $p_after).'<br><br>';

  // Links
  echo 'Links: <code class="gray">https://verb.ink ;; Title ;; Credit<br></code><br>'.pieceInput('p_links', $p_links).'<br><br>';


  // Finish the form
  echo '<input form="edit_piece" type="submit" name="p_submit" value="Update">';
  echo '<button onclick="metaEditClose'.$piece_id.'();">Cancel</button>';
  echo '</form>';

}  else {
  exit();
}
