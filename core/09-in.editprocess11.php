<?php

if ( ($_SERVER['REQUEST_METHOD'] === 'POST') && (isset($_POST['piece'])) ) {

  // POST checks
    // Only sanitize, no errors

  // Title now because we will use it in the Slug
  if ( (isset($_POST['p_title'])) && ($_POST['p_title'] != '') ) {
    $p_title = checkPiece('p_title',$_POST['p_title']);
  }

  // Apply Title to Slug if empty
  if (empty($_POST['p_slug'])) {
    $p_slug = checkPiece('p_slug',$p_title);
  } else {
    $p_slug = checkPiece('p_slug',$_POST['p_slug']);
  }

  if ( (isset($_POST['piece_id'])) && (filter_var($_POST['piece_id'], FILTER_VALIDATE_INT)) ) { // Updating piece
    $piece_id = preg_replace("/[^0-9]/"," ", $_POST['piece_id']);
    $piece_id_sqlesc = escape_sql($piece_id);
  }

  // Check for existing publication
  $query = "SELECT pubstatus FROM publications WHERE piece_id='$piece_id_sqlesc'";
  $call = mysqli_query($database, $query);
  if (mysqli_num_rows($call) == 1) {
    $row = mysqli_fetch_array($call, MYSQLI_NUM);
      $pubstatus = "$row[0]";
  } else {
    $pubstatus = 'none';
  }

  // Status ("Save draft" = pieces table; "Publish" = both pieces and publications tables)
  if ($_POST['p_submit'] == 'Save draft') {
    $p_status = 'draft';
  } elseif ($_POST['p_submit'] == 'Publish') {
    $p_status = 'publish';
    $p_live_schedule = true; // Once published, the piece remains scheduled; uncheck "Scheduled..." to adjust date_live to NOW
  } elseif ($_POST['p_submit'] == 'Update') {
    $p_status = 'update';
    $p_live_schedule = true; // Once published, the piece remains scheduled; uncheck "Scheduled..." to adjust date_live to NOW
  }

  // Check that the slug isn't already used
  $p_slug_test_sqlesc = escape_sql($p_slug);
  if (isset($piece_id)) { // We don't want a dup from for own piece
    $query = "SELECT id FROM pieces WHERE slug='$p_slug_test_sqlesc' AND NOT id='$piece_id_sqlesc'";
  } else {
    $query = "SELECT id FROM pieces WHERE slug='$p_slug_test_sqlesc'";
  }
  $call = mysqli_query($database, $query);
  if (mysqli_num_rows($call) > 0) {
    $add_num = 0;
    $dup = true;
    // If there were no changes
    while ($dup = true) {
      $add_num = $add_num + 1;
      $new_p_slug = $p_slug.'-'.$add_num;
      $new_p_slug_test_sqlesc = escape_sql($new_p_slug);

      // Check again
      if (isset($piece_id)) { // We don't want a dup from for own piece
        $query = "SELECT id FROM pieces WHERE slug='$new_p_slug_test_sqlesc' AND NOT id='$piece_id_sqlesc'";
      } else {
        $query = "SELECT id FROM pieces WHERE slug='$new_p_slug_test_sqlesc'";
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

  // All other fields
  $p_type = checkPiece('p_type',$_POST['p_type']);
  $p_content = checkPiece('p_content',$_POST['p_content']);
  $p_after = checkPiece('p_after',$_POST['p_after']);
  $p_tags_json = checkPiece('p_tags',$_POST['p_tags']);
  $p_links_json = checkPiece('p_links',$_POST['p_links']);

  // Prepare our database values for entry
  $piece_id_sqlesc = escape_sql($piece_id);
  $p_type_sqlesc = escape_sql($p_type);
  $p_series_sqlesc = escape_sql($p_series);
  $p_title_sqlesc = escape_sql($p_title);
  $p_slug_sqlesc = escape_sql($p_slug);
  $p_content_sqlesc = escape_sql($p_content);
  $p_after_sqlesc = escape_sql($p_after);
  $p_tags_sqljson = (json_decode($p_tags_json)) ? $p_tags_json : NULL; // We need JSON as is, no SQL-escape; run an operation, keep value if true, set NULL if false
  $p_links_sqljson = (json_decode($p_links_json)) ? $p_links_json : NULL; // We need JSON as is, no SQL-escape; run an operation, keep value if true, set NULL if false

  // Process tags for use in HTML
  $p_tags = implode(', ', json_decode($p_tags_json, true));
  //echo "<pre>\$p_tags_sqljson: $p_tags_sqljson</pre>"; // uncomment to see the values
  //echo "<pre>\$p_tags: $p_tags</pre>"; // uncomment to see the

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

  // New or update?
  if (isset($piece_id)) { // Editing piece

    // Make sure there are no duplicates, we don't need a revision history where no changes were made
    $query = "SELECT date_live FROM pieces WHERE BINARY id=$piece_id
    AND BINARY type='$p_type_sqlesc'
    AND BINARY series='$p_series_sqlesc'
    AND BINARY title='$p_title_sqlesc'
    AND BINARY slug='$p_slug_sqlesc'
    AND BINARY content='$p_content_sqlesc'
    AND BINARY after='$p_after_sqlesc'
    AND tags=CAST('$p_tags_sqljson' AS JSON)
    AND links=CAST('$p_links_sqljson' AS JSON)"; // This is how to test if a JSON string matches
    //echo "<pre>\$query: $query</pre>"; // uncomment to see the query, then run it yourself
    $call = mysqli_query($database, $query);
    // If there were no changes
    if (mysqli_num_rows($call) == 1) {
    // Get the date_live to see if that is the only change
    $row = mysqli_fetch_array($call, MYSQLI_NUM);
       $p_live_found = $row[0];
       // A NULL value can fool some tests, if the date_live is NULL and Scheduled... not set, set $p_live_found as a dummy string so it doesn't fool us
       if ((is_null($p_live_found)) && ($p_live_schedule == false)) {
         $p_live_found = 'found';
       }

       // We are editing a piece that has been saved, publication is allowed
       $editing_existing_piece = true;

    // For the Publish button, we at least need to know the piece indeed exists
    } else {
      $query = "SELECT id FROM pieces WHERE id='$piece_id_sqlesc'";
      $call = mysqli_query($database, $query);
      // If there were no changes
      if (mysqli_num_rows($call) == 1) {
        // We are editing a piece that has been saved, publication is allowed
        $editing_existing_piece = true;
      }
    }

   // Prepare the query to update the old piece, including the proposed live date to test for changes
   if ($p_status == 'draft') { // No empty live date for publishing pieces
     $p_live = ($p_live_schedule == true) ? "$p_live_yr-$p_live_mo-$p_live_day $p_live_hr:$p_live_min:$p_live_sec" : NULL;
     $p_live_sqlesc = escape_sql($p_live);
     $query = "UPDATE pieces SET type='$p_type_sqlesc', series=$p_series_sqlesc, title='$p_title_sqlesc', slug='$p_slug_sqlesc', content='$p_content_sqlesc', after='$p_after_sqlesc', tags='$p_tags_sqljson', links='$p_links_sqljson', date_live=NULL, date_updated=NOW() WHERE id='$piece_id_sqlesc'";
   } elseif ( ($p_status == 'publish') || ($p_status == 'update') ) { // Unscheduled publish goes live now
     $p_live = ($p_live_schedule == true) ? "$p_live_yr-$p_live_mo-$p_live_day $p_live_hr:$p_live_min:$p_live_sec" : "$p_live";
     $p_live_schedule = true;
     $p_live_sqlesc = escape_sql($p_live);
     $query = "UPDATE pieces SET type='$p_type_sqlesc', series=$p_series_sqlesc, title='$p_title_sqlesc', slug='$p_slug_sqlesc', content='$p_content_sqlesc', after='$p_after_sqlesc', tags='$p_tags_sqljson', links='$p_links_sqljson', date_live='$p_live_sqlesc', date_updated=NOW() WHERE id='$piece_id_sqlesc'";
   }

    // Run the query only if the live date is not a duplicate
    if ( (!isset($p_live_found)) || ( ($p_live_found != 'found') && ($p_live_found != $p_live) ) ) {
      // Run the pieces query
      $call = mysqli_query($database, $query);
      // Test the query
      if ($call) {

        // Change
        if (mysqli_affected_rows($database) == 1) {
          // No redirect because our variables are already set
          $response = 'Draft saved.';
          $r_class = 'green';
        // No change
        } elseif (mysqli_affected_rows($database) == 0) {
          $response = 'No change to draft.';
          $r_class = 'orange';
        }
      } else {
        $response = 'Serious error. '.$query;
        $r_class = 'error';
      }

    // Identical piece found
    } else {
      $response = 'No change to draft.';
      $r_class = 'orange';
    }

    // AJAX: Only send ajax_response if saving draft
    if ($p_status == 'draft') {
      // Prepare our response
      $ajax_response = array();
      $ajax_response['slug'] = $p_slug;
      $ajax_response['message'] = '<span class="'.$r_class.' notehide">'.$response.'</span>';
      $json_response = json_encode($ajax_response, JSON_FORCE_OBJECT);

      // We're done here
      echo $json_response;

    // Reload save
    } else {
      echo '<p class="'.$r_class.'">'.$response.'</p>';
    }

    // Publishing?
    if ( ($p_status == 'publish') || ($p_status == 'update') ) {
      // Make sure there are no duplicates, we don't need a revision history where no changes were made
      $query = "SELECT id FROM publications WHERE BINARY piece_id='$piece_id_sqlesc'
      AND BINARY type='$p_type_sqlesc'
      AND BINARY series='$p_series_sqlesc'
      AND BINARY title='$p_title_sqlesc'
      AND BINARY slug='$p_slug_sqlesc'
      AND BINARY content='$p_content_sqlesc'
      AND BINARY after='$p_after_sqlesc'
      AND tags=CAST('$p_tags_sqljson' AS JSON)
      AND links=CAST('$p_links_sqljson' AS JSON)
      AND BINARY date_live='$p_live_sqlesc'";
      $call = mysqli_query($database, $query);
      // If there were no changes
      if (mysqli_num_rows($call) == 0) {
        // Update or first publish?
        if ( ($p_status == 'publish') && ($pubstatus == 'none') ) {
          $query = "INSERT INTO publications (piece_id, type, series, title, slug, content, after, tags, links, date_live, date_updated) VALUES ('$piece_id_sqlesc', '$p_type_sqlesc', $p_series_sqlesc, '$p_title_sqlesc', '$p_slug_sqlesc', '$p_content_sqlesc', '$p_after_sqlesc', '$p_tags_sqljson', '$p_links_sqljson', '$p_live_sqlesc', NOW())";
          $callp = mysqli_query($database, $query);
          $query = "INSERT INTO publication_history (piece_id, type, series, title, slug, content, after, tags, links, date_live, date_updated) VALUES ('$piece_id_sqlesc', '$p_type_sqlesc', $p_series_sqlesc, '$p_title_sqlesc', '$p_slug_sqlesc', '$p_content_sqlesc', '$p_after_sqlesc', '$p_tags_sqljson', '$p_links_sqljson', '$p_live_sqlesc', NOW())";
          $callh = mysqli_query($database, $query);
          $query = "UPDATE pieces SET pub_yn=true WHERE id='$piece_id_sqlesc'";
          $callu = mysqli_query($database, $query);
          $publication_message = 'Piece published!';
        } elseif ( ($p_status == 'update') || ($pubstatus = 'published') || ($pubstatus = 'redrafting') ) {
          $query = "UPDATE publications SET type='$p_type_sqlesc', pubstatus='published', series=$p_series_sqlesc, title='$p_title_sqlesc', slug='$p_slug_sqlesc', content='$p_content_sqlesc', after='$p_after_sqlesc', tags='$p_tags_sqljson', links='$p_links_sqljson', date_live='$p_live_sqlesc', date_updated=NOW() WHERE piece_id='$piece_id_sqlesc'";
          $callp = mysqli_query($database, $query);
          $query = "INSERT INTO publication_history (piece_id, type, series, title, slug, content, after, tags, links, date_live, date_updated) VALUES ('$piece_id_sqlesc', '$p_type_sqlesc', $p_series_sqlesc, '$p_title_sqlesc', '$p_slug_sqlesc', '$p_content_sqlesc', '$p_after_sqlesc', '$p_tags_sqljson', '$p_links_sqljson', '$p_live_sqlesc', NOW())";
          $callh = mysqli_query($database, $query);
          $query = "UPDATE pieces SET pub_yn=true WHERE id='$piece_id_sqlesc'";
          $callu = mysqli_query($database, $query);
          $publication_message = 'Publication updated!';
        }

        // Test the query
        if (($callp) && ($callh) && ($callu)) {
           echo '<p class="green">'.$publication_message.'</p>';
        } else {
          echo '<p class="error">Serious error.</p>';
          exit();
        }
      } else {
        echo '<p class="orange">No change to publication.</p>';
      }
    }

 } elseif (!isset($_POST['piece_id'])) { // New piece
    // Create our timestamp
    if ($p_live_schedule == false){
      // It is easier to create two separate queries because "NULL" must not be in quotes when entered as the NULL value into SQL
      $p_live = NULL; // Keep it invalid, we won't use it
      $query = "INSERT INTO pieces (type, pub_yn, series, title, slug, content, after, tags, links, date_live) VALUES ('$p_type_sqlesc', false, $p_series_sqlesc, '$p_title_sqlesc', '$p_slug_sqlesc', '$p_content_sqlesc', '$p_after_sqlesc', '$p_tags_sqljson', '$p_links_sqljson', NULL)";
    } elseif ($p_live_schedule == true) {
      $p_live = "$p_live_yr-$p_live_mo-$p_live_day $p_live_hr:$p_live_min:$p_live_sec";
      $p_live_sqlesc = escape_sql($p_live);
      $query = "INSERT INTO pieces (type, pub_yn, series, title, slug, content, after, tags, links, date_live) VALUES ('$p_type_sqlesc', false, $p_series_sqlesc, '$p_title_sqlesc', '$p_slug_sqlesc', '$p_content_sqlesc', '$p_after_sqlesc','$p_tags_sqljson', '$p_links_sqljson', '$p_live_sqlesc')";
    }

    // Run the query
    $call = mysqli_query($database, $query);
    // Test the query
    if ($call) {
      // Change
      if (mysqli_affected_rows($database) == 1) {
        $_SESSION['new_just_saved'] = true;
        // Get the last added ID
        $piece_id  = $database->insert_id;
        // Redirect so we have the GET argument in the URL
        header("Location: edit.php?p=$piece_id");
        exit();
      // No change
      } elseif (mysqli_affected_rows($database) == 0) {
        echo '<p class="orange">Error, not saved.</p>';
        exit();
      }
    } else {
      echo '<p class="error">Serious error.</p>';
      exit();
    }

  } // End new/update if

  // Look for a publications piece by that ID, regardless of what happened, after everything happened
  $query = "SELECT id FROM publications WHERE piece_id='$piece_id_sqlesc' AND pubstatus='published'";
  $call = mysqli_query($database, $query);
  // Shoule be 1 row
  if (mysqli_num_rows($call) == 1) {
    $editing_published_piece = true;
  }

// Opening old piece to edit
// Check for GET or SESSION and validate in one if test
} elseif ( ( (isset($_GET['p'])) && (filter_var($_GET['p'], FILTER_VALIDATE_INT)) )
  || ( (isset($_GET['h'])) && (filter_var($_GET['h'], FILTER_VALIDATE_INT)) ) ) {

  // Deal with a SESSION argument from publication_history
  if (isset($_GET['h'])) {

    // Assign piece ID
    $revert_id = preg_replace("/[^0-9]/"," ", $_GET['h']);

    // Retrieve existing piece from history
    $query = "SELECT piece_id, type, series, title, slug, content, after, tags, links, date_live FROM publication_history WHERE id='$revert_id'";
    $call = mysqli_query($database, $query);
    // Shoule be 1 row
    if (mysqli_num_rows($call) == 1) {
      // Assign the values
      $row = mysqli_fetch_array($call, MYSQLI_NUM);
        $piece_id = "$row[0]";
        $p_type = "$row[1]";
        $p_series = "$row[2]";
        $p_title = "$row[3]";
        $p_slug = "$row[4]";
        $p_content = "$row[5]";
        $p_after = "$row[6]";
        $p_tags_json = "$row[7]";
        $p_links_sqljson = "$row[8]";
        $p_live = "$row[9]";
        $editing_published_piece = true;

        // Indicate a historical edit
        echo '<h2><code class="orange">Reverting to: </code><code class="gray">'.$p_live.'</code></h2>';

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
        } else {
          $p_links = '';
        }

        $query = "SELECT status FROM pieces WHERE id='$piece_id'";
        $call = mysqli_query($database, $query);
        // Shoule be 1 row
        if (mysqli_num_rows($call) == 1) {
          // Assign the values
          $row = mysqli_fetch_array($call, MYSQLI_NUM);
            $p_status = "$row[0]";
          } else {
            echo '<p class="error">Impossible error.</p>';
            exit();
          }

      // We are editing a piece that has been saved, publication is allowed
      $editing_existing_piece = true;

      } else {
        // ID does not match, redirect to blank editor
        header("Location: edit.php");
        exit();
      }

  // ID is not from SESSION, it is from GET
  } else {

    // Assign piece ID
    $piece_id = preg_replace("/[^0-9]/"," ", $_GET['p']);
    $piece_id_sqlesc = escape_sql($piece_id);

    // Look for a publications piece, regardless of what happens, before anything else happens
    $query = "SELECT id FROM publications WHERE piece_id='$piece_id_sqlesc' AND pubstatus='published'";
    $call = mysqli_query($database, $query);
    // Shoule be 1 row
    if (mysqli_num_rows($call) == 1) {
      $editing_published_piece = true;
    }

    // Retrieve existing piece
    $query = "SELECT type, status, series, title, slug, content, after, tags, links, date_live FROM pieces WHERE id='$piece_id_sqlesc'";
    $call = mysqli_query($database, $query);
    // Shoule be 1 row
    if (mysqli_num_rows($call) == 1) {
      // Assign the values
      $row = mysqli_fetch_array($call, MYSQLI_NUM);
        $p_type = "$row[0]";
        $p_status = "$row[1]";
        $p_series = "$row[2]";
        $p_title = "$row[3]";
        $p_slug = "$row[4]";
        $p_content = "$row[5]";
        $p_after = "$row[6]";
        $p_tags_json = "$row[7]";
        $p_links_sqljson = "$row[8]";
        $p_live = "$row[9]";

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

      // We are editing a piece that has been saved, publication is allowed
      $editing_existing_piece = true;

      } else {
        // ID does not match, redirect to blank editor
        header("Location: edit.php");
        exit();
      }
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

  // New just saved?
  if ( (isset($_SESSION['new_just_saved'])) && ($_SESSION['new_just_saved'] == true) ) {
    unset($_SESSION['new_just_saved']);
    echo '<p class="green">Saved!</p>';
  }


// New piece message
} else {
  echo '<p class="blue">New piece</p>';

} // Finish POST/GET/new if
