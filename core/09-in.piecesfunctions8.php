<?php

// Single actions
function piecesform($name, $p_id) {

  // Validate the $p_id
  if (!filter_var($p_id, FILTER_VALIDATE_INT)) {exit();}

  // Get the page we're going to
  if ($name == 'undelete') {
    $color_class = 'orange';
    $float_ = 'left';
    $slug = 'delete';
  } elseif ($name == 'delete') {
    $color_class = 'red';
    $float_ = 'right';
    $slug = 'delete';
  } elseif ($name == 'restore') {
    $color_class = 'orange';
    $float_ = 'left';
    $slug = 'delete';
  } elseif ($name == 'permanently delete') {
    $color_class = 'red';
    $float_ = 'right';
    $slug = 'delete';
  } elseif ($name == 'unpublish') {
    $color_class = 'orange';
    $float_ = 'left';
    $slug = 'publish';
  } elseif ($name == 'republish') {
    $color_class = 'green';
    $float_ = 'left';
    $slug = 'publish';
  } elseif ($name == 'make post') {
    $color_class = 'blue';
    $float_ = 'left';
    $slug = 'make';
  } elseif ($name == 'make page') {
    $color_class = 'blue';
    $float_ = 'left';
    $slug = 'make';
  }

  $result = '
  <form method="post" id="pa_'.$slug.'_'.$p_id.'" action="act.piecesactions.php" style ="float: '.$float_.';" class="postform inline">
    <input type="hidden" name="p" value="'.$p_id.'">
    <input type="hidden" name="action" value="'.$name.'">
    <input type="submit" class="postform inline link-button '.$color_class.'" value="'.$name.'">
  </form>';


  $result .= '
  <script>
  window.addEventListener( "load", function () {
    function sendData() {
      const AJAX = new XMLHttpRequest();
      const FD = new FormData( form );
      AJAX.addEventListener( "load", function(event) {
        document.getElementById("prow_'.$p_id.'").innerHTML = event.target.responseText;
        document.getElementById("prow_'.$p_id.'").classList.add("renew");
        form = document.getElementById( "pa_'.$slug.'_'.$p_id.'" );
        listenToForm'.$slug.$p_id.'();
      } );
      AJAX.addEventListener( "error", function( event ) {
        document.getElementById("prow_'.$p_id.'").innerHTML =  "<tr class=\"renew\" id=\"prow_'.$p_id.'\" class=\"error\">Error with '.$name.'</tr>";
      } );
      AJAX.open( "POST", "ajax.piecesactions.php" );
      AJAX.send( FD );
    }
    var form = document.getElementById( "pa_'.$slug.'_'.$p_id.'" );
    function listenToForm'.$slug.$p_id.'(){
      form.addEventListener( "submit", function ( event ) {
        event.preventDefault();
        sendData();
      } );
    }
    listenToForm'.$slug.$p_id.'();
  } );
  </script>';

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
    $queryr = "UPDATE publications SET pubstatus='redrafting' WHERE piece_id='$p_id'";
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
    $query = "UPDATE pieces SET status='live' WHERE id='$p_id'";
    $call = mysqli_query($database, $query);
    if ($call) {
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
