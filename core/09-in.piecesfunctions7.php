<?php

// Single actions
function piecesform($name, $p_id) {

  // Validate the $p_id
  if (!filter_var($p_id, FILTER_VALIDATE_INT)) {exit();}

  // Get the page we're going to
  if ($name == 'undelete') {
    $post_to = 'undelete.php';
    $color_class = 'orange';
    $float_ = 'left';
  } elseif ($name == 'unpublish') {
    $post_to = 'unpublish.php';
    $color_class = 'orange';
    $float_ = 'left';
  } elseif ($name == 'republish') {
    $post_to = 'republish.php';
    $color_class = 'green';
    $float_ = 'left';
  } elseif ($name == 'delete') {
    $post_to = 'delete.php';
    $color_class = 'red';
    $float_ = 'right';
  } elseif ($name == 'restore') {
    $post_to = 'undelete_trash.php';
    $color_class = 'orange';
    $float_ = 'left';
  } elseif ($name == 'permanently delete') {
    $post_to = 'empty_delete_trash.php';
    $color_class = 'red';
    $float_ = 'right';
  } elseif ($name == 'make post') {
    $post_to = 'postify.php';
    $color_class = 'blue';
    $float_ = 'left';
  } elseif ($name == 'make page') {
    $post_to = 'pagify.php';
    $color_class = 'blue';
    $float_ = 'left';
  }

  $result = '
<form method="post" action="'.$post_to.'" style ="float: '.$float_.';" class="postform inline">
  <input type="hidden" name="p" value="'.$p_id.'">
  <button type="submit" class="postform inline link-button '.$color_class.'">'.$name.'</button>
</form>';

  return $result;
} // Finish function

// Bulk actions
function piecesaction($action, $p_id) {
  // We need our $database inside this function
  global $database;

  // Validate the $p_id
  if (!filter_var($p_id, FILTER_VALIDATE_INT)) {exit();}

  // Choose the action
  if ($action == 'unpublish') {
    $query = "UPDATE publications SET pubstatus='redrafting' WHERE id='$p_id'";
    $call = mysqli_query($database, $query);
    if ($call) {
      $piecesactionsuccess = true;
    } else {
      unset($piecesactionsuccess);
      echo '<pre>Major database error!</pre>';
      exit();
    }

  } elseif ($action == 'republish') {
    $query = "UPDATE publications SET pubstatus='published' WHERE id='$p_id'";
    $call = mysqli_query($database, $query);
    if ($call) {
      $piecesactionsuccess = true;
    } else {
      unset($piecesactionsuccess);
      echo '<pre>Major database error!</pre>';
      exit();
    }

  } elseif ($action == 'delete') {
    $queryd = "UPDATE pieces SET status='dead' WHERE id='$p_id'";
    $calld = mysqli_query($database, $queryd);
    $queryr = "UPDATE publications SET status='dead' WHERE piece_id='$p_id'";
    $callr = mysqli_query($database, $queryr);
    if (($calld) && ($callr)) {
      $piecesactionsuccess = true;
    } else {
      unset($piecesactionsuccess);
      echo '<pre>Major database error!</pre>';
      exit();
    }

  } elseif (($action == 'restore')
        ||  ($action == 'undelete')) {
    $queryd = "UPDATE pieces SET status='live' WHERE id='$piece_id'";
    $calld = mysqli_query($database, $queryd);
    $queryr = "UPDATE publications SET status='live' WHERE piece_id='$piece_id'";
    $callr = mysqli_query($database, $queryr);
    if (($calld) && ($callr)) {
      $piecesactionsuccess = true;
    } else {
      unset($piecesactionsuccess);
      echo '<pre>Major database error!</pre>';
      exit();
    }

  } elseif ($action == 'permanently delete') {
    $query1 = "DELETE FROM pieces WHERE status='dead' AND id='$p_id'";
    $call1 = mysqli_query($database, $query1);
    $query2 = "DELETE FROM publications WHERE piece_id='$p_id'";
    $call2 = mysqli_query($database, $query2);
    $query3 = "DELETE FROM publication_history WHERE piece_id='$p_id'";
    $call3 = mysqli_query($database, $query3);
    if (($call1) && ($call2) && ($call3)) {
      $piecesactionsuccess = true;
    } else {
      unset($piecesactionsuccess);
      echo '<pre>Major database error!</pre>';
      exit();
    }

  } elseif ($action == 'make post') {
    $query1 = "UPDATE publications SET type='post' WHERE piece_id='$p_id'";
    $call1 = mysqli_query($database, $query1);
    $query2 = "UPDATE pieces SET type='post' WHERE id='$p_id'";
    $call2 = mysqli_query($database, $query2);
    if (($call1) && ($call2)) {
      $piecesactionsuccess = true;
    } else {
      unset($piecesactionsuccess);
      echo '<pre>Major database error!</pre>';
      exit();
    }

  } elseif ($action == 'make page') {
    $query1 = "UPDATE publications SET type='page' WHERE piece_id='$p_id'";
    $call1 = mysqli_query($database, $query1);
    $query2 = "UPDATE pieces SET type='page' WHERE id='$p_id'";
    $call2 = mysqli_query($database, $query2);
    if (($call1) && ($call2)) {
      $piecesactionsuccess = true;
    } else {
      unset($piecesactionsuccess);
      echo '<pre>Major database error!</pre>';
      exit();
    }

  }

} // Finish function
