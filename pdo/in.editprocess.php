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
    $piece_id_trim = DB::trimspace($piece_id);
  }

  // Check for existing publication
  $query = "SELECT pubstatus FROM publications WHERE piece_id='$piece_id_trim'";
  $row = $pdo->try_select($query);
  if ($pdo->numrows == 1) {
    $pubstatus = "$row->pubstatus";
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
  $p_slug_test_trim = DB::trimspace($p_slug);
  if (isset($piece_id)) { // We don't want a dup from for own piece
    $query = "SELECT id FROM pieces WHERE slug='$p_slug_test_trim' AND NOT id='$piece_id_trim'";
  } else {
    $query = "SELECT id FROM pieces WHERE slug='$p_slug_test_trim'";
  }
  $row = $pdo->try_select($query);
  if ($pdo->numrows > 0) {
    $add_num = 0;
    $dup = true;
    // If there were no changes
    while ($dup = true) {
      $add_num = $add_num + 1;
      $new_p_slug = $p_slug.'-'.$add_num;
      $new_p_slug_test_trim = DB::trimspace($new_p_slug);

      // Check again
      if (isset($piece_id)) { // We don't want a dup from for own piece
        $query = "SELECT id FROM pieces WHERE slug='$new_p_slug_test_trim' AND NOT id='$piece_id_trim'";
      } else {
        $query = "SELECT id FROM pieces WHERE slug='$new_p_slug_test_trim'";
      }
      $row = $pdo->try_select($query);
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
  } else {
    $p_live = date("Y-m-d H:i:s");
  }

  // Series
  // Set a default Series, probably from settings table
  $de_series = (isset($_SESSION['de_series'])) ? $_SESSION['de_series'] : 1;
  if (filter_var($_POST['p_series'], FILTER_VALIDATE_INT)) {
    $p_series = $_POST['p_series'];
    $query = "SELECT id FROM series WHERE id='$p_series'";
    $row = $pdo->try_select($query);
    if ($pdo->numrows != 1) {
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
  $piece_id_trim = DB::trimspace($piece_id);
  $p_type_trim = DB::trimspace($p_type);
  $p_series_trim = DB::trimspace($p_series);
  $p_title_trim = DB::trimspace($p_title);
  $p_slug_trim = DB::trimspace($p_slug);
  $p_content_trim = DB::trimspace($p_content);
  $p_after_trim = DB::trimspace($p_after);
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

    // Prepare the query to update the old piece, adjusting for the proposed live date
    if ( ($p_status == 'draft') && ($p_live_schedule != true) ) { // No empty live date for publishing pieces
      $p_live = NULL;
      $p_live_trim = DB::trimspace($p_live); // Needs to be set
      $queryu = "UPDATE pieces SET type='$p_type_trim', series=$p_series_trim, title='$p_title_trim', slug='$p_slug_trim', content='$p_content_trim', after='$p_after_trim', tags='$p_tags_sqljson', links='$p_links_sqljson', date_live=NULL, date_updated=NOW() WHERE id='$piece_id_trim'";
    } elseif ( (($p_status == 'publish') || ($p_status == 'update') || ($p_status == 'draft')) && ($p_live_schedule == true) ) { // Unscheduled publish goes live now
      $p_live = "$p_live_yr-$p_live_mo-$p_live_day $p_live_hr:$p_live_min:$p_live_sec";
      $p_live_trim = DB::trimspace($p_live);
      $queryu = "UPDATE pieces SET type='$p_type_trim', series=$p_series_trim, title='$p_title_trim', slug='$p_slug_trim', content='$p_content_trim', after='$p_after_trim', tags='$p_tags_sqljson', links='$p_links_sqljson', date_live='$p_live_trim', date_updated=NOW() WHERE id='$piece_id_trim'";
    } elseif ($p_live_schedule != true) { // Not scheduled, but not a draft save either, so will be scheduled for now with this query
      $p_live_schedule = 'waiting'; // Set this for later
      $queryu = "UPDATE pieces SET type='$p_type_trim', series=$p_series_trim, title='$p_title_trim', slug='$p_slug_trim', content='$p_content_trim', after='$p_after_trim', tags='$p_tags_sqljson', links='$p_links_sqljson', date_live=NOW(), date_updated=NOW() WHERE id='$piece_id_trim'";
    }

    // Make sure there are no duplicates, we don't need a revision history where no changes were made
    $query = "SELECT date_live FROM pieces WHERE BINARY id=$piece_id
    AND BINARY type='$p_type_trim'
    AND BINARY series='$p_series_trim'
    AND BINARY title='$p_title_trim'
    AND BINARY slug='$p_slug_trim'
    AND BINARY content='$p_content_trim'
    AND BINARY after='$p_after_trim'
    AND BINARY date_live='$p_live_trim'
    AND tags='$p_tags_sqljson'
    AND links='$p_links_sqljson'"; // This is how to test if a JSON string matches
    $row = $pdo->try_select($query);
    // If there were no changes
    if ($pdo->numrows == 1) {
      // Get the date_live to see if that is the only change
       $p_live_found = $row->date_live;
       // A NULL value can fool some tests, if the date_live is NULL and Scheduled... not set, set $p_live_found as a dummy string so it doesn't fool us
       if ((is_null($p_live_found)) && ($p_live_schedule == false)) {
         $p_live_found = 'found';
       }

       // We are editing a piece that has been saved, publication is allowed
       $editing_existing_piece = true;

    // For the Publish button, we at least need to know the piece indeed exists
    } else {
      $query = "SELECT id FROM pieces WHERE id='$piece_id_trim'";
      $row = $pdo->try_select($query);
      // If there were no changes
      if ($pdo->numrows == 1) {
        // We are editing a piece that has been saved, publication is allowed
        $editing_existing_piece = true;
      }
    }

    // Run the query only if the live date is not a duplicate
    if ( (!isset($p_live_found)) || ( ($p_live_found != 'found') && ($p_live_found != $p_live) ) ) {
      // Run the pieces query
      $callu = $pdo->try_update($queryu);
      // Test the query
      if ($pdo->ok) {

        // Change
        if ($pdo->change) {
          // No redirect because our variables are already set
          $response = 'Draft saved.';
          $r_class = 'green';

          // Do we need a new scheduled time?
          if ($p_live_schedule == 'waiting') {
            $querysc = "SELECT date_live FROM pieces WHERE id='$piece_id_trim'";
            $rowsc = $pdo->select('pieces', 'id', $piece_id_trim, 'date_live');
            if ($pdo->numrows == 1) {
              $p_live = $rowsc->date_live;
              $p_live_schedule = true; // Set this for the rest of our form
            } else {
              echo '<p class="error">Serious error.</p>';
            }
          }
        // No change
      } elseif (!$pdo->change) {
          $response = 'No change to draft.';
          $r_class = 'orange';
        }
      } else {
        $response = 'Serious error.';
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
      $query = "SELECT id FROM publications WHERE BINARY piece_id='$piece_id_trim'
      AND BINARY type='$p_type_trim'
      AND BINARY series='$p_series_trim'
      AND BINARY title='$p_title_trim'
      AND BINARY slug='$p_slug_trim'
      AND BINARY content='$p_content_trim'
      AND BINARY after='$p_after_trim'
      AND BINARY date_live='$p_live_trim'
      AND tags='$p_tags_sqljson'
      AND links='$p_links_sqljson'";
      $row = $pdo->try_select($query);
      // If there were no duplicates
      if ($pdo->numrows == 0) {
        // Update or first publish?
        if ( ($p_status == 'publish') && ($pubstatus == 'none') ) {
          $query = "INSERT INTO publications (piece_id, type, series, title, slug, content, after, tags, links, date_live, date_updated) SELECT id, type, series, title, slug, content, after, tags, links, date_live, date_updated FROM pieces WHERE id='$piece_id_trim';";
          $pdo->try_insert($query);
          $callp = $pdo->ok;
          $query = "INSERT INTO publication_history (piece_id, type, series, title, slug, content, after, tags, links, date_live, date_updated) SELECT id, type, series, title, slug, content, after, tags, links, date_live, date_updated FROM pieces WHERE id='$piece_id_trim';";
          $pdo->try_insert($query);
          $callh = $pdo->ok;
          $query = "UPDATE pieces SET pub_yn=true WHERE id='$piece_id_trim'";
          $pdo->try_insert($query);
          $callu = $pdo->ok;
          $publication_message = 'Piece published!';
        } elseif ( ($p_status == 'update') || ($pubstatus = 'published') || ($pubstatus = 'redrafting') ) {
          $query = "UPDATE publications PUB, pieces PCE
          SET PUB.type=PCE.type,
              PUB.pubstatus='published',
              PUB.series=PCE.series,
              PUB.title=PCE.title,
              PUB.slug=PCE.slug,
              PUB.content=PCE.content,
              PUB.after=PCE.after,
              PUB.tags=PCE.tags,
              PUB.links=PCE.links,
              PUB.date_live=PCE.date_live,
              PUB.date_updated=PCE.date_updated
          WHERE PUB.piece_id='$piece_id_trim' AND PCE.id='$piece_id_trim'";
          $pdo->try_update($query);
          $callp = $pdo->ok;
          $query = "INSERT INTO publication_history (piece_id, type, series, title, slug, content, after, tags, links, date_live, date_updated) SELECT id, type, series, title, slug, content, after, tags, links, date_live, date_updated FROM pieces WHERE id='$piece_id_trim';";
          $pdo->try_insert($query);
          $callh = $pdo->ok;
          $query = "UPDATE pieces SET pub_yn=true WHERE id='$piece_id_trim'";
          $pdo->try_update($query);
          $callu = $pdo->ok;
          $publication_message = 'Publication updated!';
        }

        // Test the query
        if (($callp) && ($callh) && ($callu)) {
           echo '<p class="green">'.$publication_message.'</p>';
        } else {
          echo '<p class="error">Serious error.</p>';
          exit ();
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
      $query = "INSERT INTO pieces (type, pub_yn, series, title, slug, content, after, tags, links, date_live) VALUES ('$p_type_trim', false, $p_series_trim, '$p_title_trim', '$p_slug_trim', '$p_content_trim', '$p_after_trim', '$p_tags_sqljson', '$p_links_sqljson', NULL)";
    } elseif ($p_live_schedule == true) {
      $p_live = "$p_live_yr-$p_live_mo-$p_live_day $p_live_hr:$p_live_min:$p_live_sec";
      $p_live_trim = DB::trimspace($p_live);
      $query = "INSERT INTO pieces (type, pub_yn, series, title, slug, content, after, tags, links, date_live) VALUES ('$p_type_trim', false, $p_series_trim, '$p_title_trim', '$p_slug_trim', '$p_content_trim', '$p_after_trim','$p_tags_sqljson', '$p_links_sqljson', '$p_live_trim')";
    }

    // Run the query
    $pdo->try_insert($query);
    // Test the query
    if ($pdo->ok) {
      // Change
      if ($pdo->change) {
        $_SESSION['new_just_saved'] = true;
        // Get the last added ID
        $piece_id  = $pdo->lastid;
        // Redirect so we have the GET argument in the URL
        header("Location: edit.php?p=$piece_id");
        exit ();
      // No change
    } elseif (!$pdo->change) {
        echo '<p class="orange">Error, not saved.</p>';
        exit ();
      }
    } else {
      echo '<p class="error">Serious error.</p>';
      exit ();
    }

  } // End new/update if

  // Look for a publications piece by that ID, regardless of what happened, after everything happened
  $query = "SELECT id FROM publications WHERE piece_id='$piece_id_trim' AND pubstatus='published'";
  $row = $pdo->try_select($query);
  // Shoule be 1 row
  if ($pdo->numrows == 1) {
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
    $row = $pdo->try_select($query);
    // Shoule be 1 row
    if ($pdo->numrows == 1) {
      // Assign the values
      $piece_id = "$row->piece_id";
      $p_type = "$row->type";
      $p_series = "$row->series";
      $p_title = "$row->title";
      $p_slug = "$row->slug";
      $p_content = "$row->content";
      $p_after = "$row->after";
      $p_tags_json = "$row->tags";
      $p_links_sqljson = "$row->links";
      $p_live = "$row->date_live";
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
      $row = $pdo->try_select($query);
      // Shoule be 1 row
      if ($pdo->numrows == 1) {
        // Assign the values
        $p_status = "$row->status";
      } else {
        echo '<p class="error">Impossible error.</p>';
        exit ();
      }

      // We are editing a piece that has been saved, publication is allowed
      $editing_existing_piece = true;

      } else {
        // ID does not match, redirect to blank editor
        header("Location: edit.php");
        exit ();
      }

  // ID is not from SESSION, it is from GET
  } else {

    // Assign piece ID
    $piece_id = preg_replace("/[^0-9]/"," ", $_GET['p']);
    $piece_id_trim = DB::trimspace($piece_id);

    // Look for a publications piece, regardless of what happens, before anything else happens
    $query = "SELECT id FROM publications WHERE piece_id='$piece_id_trim' AND pubstatus='published'";
    $row = $pdo->try_select($query);
    // Shoule be 1 row
    if ($pdo->numrows == 1) {
      $editing_published_piece = true;
    }

    // Retrieve existing piece
    $query = $database->prepare("SELECT type, status, series, title, slug, content, after, tags, links, date_live FROM pieces WHERE id=:id");
    $query->bindParam(':id', $piece_id_trim);
    $rows = DB::exec_select($query);

    // Shoule be 1 row
    if (DB::$rowcount > 0) {
        foreach ($rows as $row) {
        // Assign the values
        $p_type = "$row->type";
        $p_status = "$row->status";
        $p_series = "$row->series";
        $p_title = "$row->title";
        $p_slug = "$row->slug";
        $p_content = "$row->content";
        $p_after = "$row->after";
        $p_tags_json = "$row->tags";
        $p_links_sqljson = "$row->links";
        $p_live = "$row->date_live";

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

        }
      } else {
         // ID does not match, redirect to blank editor
         header("Location: edit.php");
         exit ();
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
