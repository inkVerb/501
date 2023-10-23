<?php

// Include our config (with SQL) up near the top of our PHP file
include ('./in.config.php');

// Must be logged in
if (!isset($_SESSION['user_id'])) {
  exit (header("Location: blog.php"));
}

// Include our pieces functions
include ('./in.metaeditfunctions.php');

// republish
if ($_POST['bluksubmit'] == 'republish') {
  unset($_POST['bluksubmit']);
  foreach ($_POST as $piece_id) {
    // Validate
    if (!filter_var($piece_id, FILTER_VALIDATE_INT)) {
      continue;
    }
    // Run the action
    piecesaction('republish', $piece_id);
  }

  // Done, go home
  exit (header("Location: pieces.php"));
}

// unpublish
if ($_POST['bluksubmit'] == 'unpublish') {
  unset($_POST['bluksubmit']);
  foreach ($_POST as $piece_id) {
    // Validate
    if (!filter_var($piece_id, FILTER_VALIDATE_INT)) {
      continue;
    }
    // Run the action
    piecesaction('unpublish', $piece_id);
  }

  // Done, go home
  exit (header("Location: pieces.php"));
}

// make post
if ($_POST['bluksubmit'] == 'make post') {
  unset($_POST['bluksubmit']);
  foreach ($_POST as $piece_id) {
    // Validate
    if (!filter_var($piece_id, FILTER_VALIDATE_INT)) {
      continue;
    }
    // Run the action
    piecesaction('make post', $piece_id);
  }

  // Done, go home
  exit (header("Location: pieces.php"));
}

// make page
if ($_POST['bluksubmit'] == 'make page') {
  unset($_POST['bluksubmit']);
  foreach ($_POST as $piece_id) {
    // Validate
    if (!filter_var($piece_id, FILTER_VALIDATE_INT)) {
      continue;
    }
    // Run the action
    piecesaction('make page', $piece_id);
  }

  // Done, go home
  exit (header("Location: pieces.php"));
}

// undelete
if ($_POST['bluksubmit'] == 'undelete') {
  unset($_POST['bluksubmit']);
  foreach ($_POST as $piece_id) {
    // Validate
    if (!filter_var($piece_id, FILTER_VALIDATE_INT)) {
      continue;
    }
    // Run the action
    piecesaction('undelete', $piece_id);
  }

  // Done, go home
  exit (header("Location: pieces.php"));
}

// delete
if ($_POST['bluksubmit'] == 'delete') {
  unset($_POST['bluksubmit']);
  foreach ($_POST as $piece_id) {
    // Validate
    if (!filter_var($piece_id, FILTER_VALIDATE_INT)) {
      continue;
    }
    // Run the action
    piecesaction('delete', $piece_id);
  }

  // Done, go home
  exit (header("Location: pieces.php"));
}

// restore (trash)
if ($_POST['bluksubmit'] == 'restore') {
  unset($_POST['bluksubmit']);
  foreach ($_POST as $piece_id) {
    // Validate
    if (!filter_var($piece_id, FILTER_VALIDATE_INT)) {
      continue;
    }
    // Run the action
    piecesaction('restore', $piece_id);
  }

  // Done, go home
  exit (header("Location: trash.php"));
}

// delete forever (trash)
if ($_POST['bluksubmit'] == 'delete forever') {
  unset($_POST['bluksubmit']);
  foreach ($_POST as $piece_id) {
    // Validate
    if (!filter_var($piece_id, FILTER_VALIDATE_INT)) {
      continue;
    }
    // Run the action
    piecesaction('delete forever', $piece_id);
  }

  // Done, go home
  exit (header("Location: trash.php"));
}


?>
