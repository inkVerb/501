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
  $p_live_yr = checkPiece('p_live_yr',$_POST['p_live_yr']);
  $p_live_mo = checkPiece('p_live_mo',$_POST['p_live_mo']);
  $p_live_day = checkPiece('p_live_day',$_POST['p_live_day']);
  $p_live_hr = checkPiece('p_live_hr',$_POST['p_live_hr']);
  $p_live_min = checkPiece('p_live_min',$_POST['p_live_min']);
  $p_live_sec = checkPiece('p_live_sec',$_POST['p_live_sec']);

  // All other fields
  $p_type = checkPiece('p_type',$_POST['p_type']);
  $p_content = checkPiece('p_content',$_POST['p_content']);
  $p_after = checkPiece('p_after',$_POST['p_after']);

  // Prepare our database values for entry
  $p_type_sqlesc = escape_sql($p_type);
  $p_title_sqlesc = escape_sql($p_title);
  $p_slug_sqlesc = escape_sql($p_slug);
  $p_content_sqlesc = escape_sql($p_content);
  $p_after_sqlesc = escape_sql($p_after);

  // New or update?
  if (isset($piece_id)) { // Updating piece

   // Make sure there are no duplicates, we don't need a revision history where no changes were made
   $query = "SELECT date_live FROM pieces WHERE BINARY id='$piece_id_sqlesc' AND BINARY type='$p_type_sqlesc' AND BINARY title='$p_title_sqlesc' AND BINARY slug='$p_slug_sqlesc' AND BINARY content='$p_content_sqlesc' AND BINARY after='$p_after_sqlesc'";
   $call = mysqli_query($database, $query);
   // If there were no changes
   if (mysqli_num_rows($call) == 1) {
   // Get the date_live to see if that is the only change
   $row = mysqli_fetch_array($call, MYSQLI_NUM);
      $p_live_found = $row[0];
      // A NULL value can fool some tests, if the date_live is NULL and Scheduled... not set, set $p_live_found as true so it doesn't fool us
      if ((is_null($p_live_found)) && ($p_live_schedule == false)) {
        $p_live_found = 'found';
      }
   }

  // Prepare the query to update the old piece, including the proposed live date to test for changes
  if ($p_live_schedule == false) {
    // It is easier to create two separate queries because "NULL" must not be in quotes when entered as the NULL value into SQL
    $p_live = NULL; // Keep it invalid, we won't use it
    $query = "UPDATE pieces SET type='$p_type_sqlesc', title='$p_title_sqlesc', slug='$p_slug_sqlesc', content='$p_content_sqlesc', after='$p_after_sqlesc', date_live=NULL, date_updated=NOW() WHERE id='$piece_id_sqlesc'";
  } elseif ($p_live_schedule == true) {
    $p_live = "$p_live_yr-$p_live_mo-$p_live_day $p_live_hr:$p_live_min:$p_live_sec";
    $p_live_sqlesc = escape_sql($p_live);
    $query = "UPDATE pieces SET type='$p_type_sqlesc', title='$p_title_sqlesc', slug='$p_slug_sqlesc', content='$p_content_sqlesc', after='$p_after_sqlesc', date_live='$p_live_sqlesc', date_updated=NOW() WHERE id='$piece_id_sqlesc'";
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
         echo '<p class="green">Draft updated.</p>';
       // No change
       } elseif (mysqli_affected_rows($database) == 0) {
         echo '<p class="orange">No change to draft.</p>';
       }
     } else {
       echo '<p class="error">Serious error.</p>';
       exit ();
     }

   // Identical piece found
   } else {
     echo '<p class="orange">No change to draft.</p>';
   }

 } elseif (!isset($_POST['piece_id'])) { // New piece
    // Create our timestamp
    if ($p_live_schedule == false) {
      // It is easier to create two separate queries because "NULL" must not be in quotes when entered as the NULL value into SQL
      $p_live = NULL; // Keep it invalid, we won't use it
      $query = "INSERT INTO pieces (type, title, slug, content, after, date_live) VALUES ('$p_type_sqlesc', '$p_title_sqlesc', '$p_slug_sqlesc', '$p_content_sqlesc', '$p_after_sqlesc', NULL)";
    } elseif ($p_live_schedule == true) {
      $p_live = "$p_live_yr-$p_live_mo-$p_live_day $p_live_hr:$p_live_min:$p_live_sec";
      $p_live_sqlesc = escape_sql($p_live);
      $query = "INSERT INTO pieces (type, title, slug, content, after, date_live) VALUES ('$p_type_sqlesc', '$p_title_sqlesc', '$p_slug_sqlesc', '$p_content_sqlesc', '$p_after_sqlesc', '$p_live_sqlesc')";
    }

    // Run the query
    $call = mysqli_query($database, $query);
    // Test the query
    if ($call) {
      // Change
      if (mysqli_affected_rows($database) == 1) {
        $_SESSION['new_just_saved'] = true;
        // Get the last added ID
        $piece_id = $database->insert_id;
        // Redirect so we have the GET argument in the URL
        header("Location: edit.php?p=$piece_id");
        exit ();
      // No change
      } elseif (mysqli_affected_rows($database) == 0) {
        echo '<p class="orange">Error, not saved.</p>';
        exit ();
      }
    } else {
      echo '<p class="error">Serious error.</p>';
      exit ();
    }

  } // End new/update if


// New piece or editing old piece?
// Check for GET and validate in one if test
} elseif ( (isset($_GET['p'])) && (filter_var($_GET['p'], FILTER_VALIDATE_INT)) ) {
  // Set $piece_id via sanitize non-numbers
  $piece_id = preg_replace("/[^0-9]/"," ", $_GET['p']);
  $piece_id_sqlesc = escape_sql($piece_id);

  // Retrieve existing piece
  $query = "SELECT type, status, title, slug, content, after, date_live FROM pieces WHERE id='$piece_id_sqlesc'";
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

    } else {
      // ID does not match, redirect to blank editor
      header("Location: edit.php");
      exit ();
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
