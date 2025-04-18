<?php

// Single actions
function metaeditform($name, $p_id) {

  // Validate the $p_id
  if (!filter_var($p_id, FILTER_VALIDATE_INT)) {exit ();}

  // Get the page we're going to
  switch ($name) {
    case 'undelete':
      $color_class = 'orange';
      $float_ = 'left';
      $slug = 'undelete';
      break;
    case 'restore':
      $color_class = 'orange';
      $float_ = 'left';
      $slug = 'undelete';
      break;
    case 'delete':
      $color_class = 'red';
      $float_ = 'right';
      $slug = 'delete';
      break;
    case 'redelete':
      $color_class = 'red';
      $float_ = 'left';
      $slug = 'delete';
      break;
    case 'purge':
      $color_class = 'red';
      $float_ = 'right';
      $slug = 'pdelete';
      break;
    case 'unpublish':
      $color_class = 'orange';
      $float_ = 'left';
      $slug = 'status';
      break;
    case 'republish':
      $color_class = 'green';
      $float_ = 'left';
      $slug = 'status';
      break;
    case 'make post':
      $color_class = 'blue';
      $float_ = 'left';
      $slug = 'make';
      break;
    case 'make page':
      $color_class = 'blue';
      $float_ = 'left';
      $slug = 'make';
      break;
  }

  $result = '
  <form method="post" id="pa_'.$slug.'_'.$p_id.'" action="act.piecesactions.php" style="float: '.$float_.';" class="postform inline">
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
        document.getElementById("changed_'.$p_id.'").style.display = "inline";';

// Delete actions should hide other options
if ($name == 'delete') { // Pieces
  $result .= 'document.getElementById("r_undelete_'.$p_id.'").style.display = "inherit";
              document.getElementById("r_delete_'.$p_id.'").style.display = "none";
              document.getElementById("r_status_'.$p_id.'").style.display = "none";
              document.getElementById("pdeleting'.$p_id.'").style.display = "inline";
              document.getElementById("pstatus'.$p_id.'").style.display = "none";
              document.getElementById("prow_'.$p_id.'").classList.add("deleting");
              document.getElementById("prow_'.$p_id.'").classList.remove("undeleting","renew");
              document.getElementById("changed_'.$p_id.'").classList.add("deleting");
              document.getElementById("changed_'.$p_id.'").classList.remove("undeleting","renew");
              document.getElementById("changed_'.$p_id.'").innerHTML = "&nbsp;deleting&nbsp;";';
} elseif ($name == 'undelete') { // Pieces
  $result .= 'document.getElementById("r_undelete_'.$p_id.'").style.display = "none";
              document.getElementById("r_delete_'.$p_id.'").style.display = "inherit";
              document.getElementById("r_status_'.$p_id.'").style.display = "inherit";
              document.getElementById("pdeleting'.$p_id.'").style.display = "none";
              document.getElementById("pstatus'.$p_id.'").style.display = "inline";
              document.getElementById("prow_'.$p_id.'").classList.add("undeleting");
              document.getElementById("prow_'.$p_id.'").classList.remove("deleting","renew");
              document.getElementById("changed_'.$p_id.'").classList.add("undeleting");
              document.getElementById("changed_'.$p_id.'").classList.remove("deleting","renew");
              document.getElementById("changed_'.$p_id.'").innerHTML = "&nbsp;undeleted&nbsp;";';
} elseif ($name == 'redelete') { // Trash
  $result .= 'document.getElementById("r_redelete_'.$p_id.'").style.display = "none";
              document.getElementById("r_restore_'.$p_id.'").style.display = "inherit";
              document.getElementById("r_pdelete_'.$p_id.'").style.display = "inherit";
              document.getElementById("prow_'.$p_id.'").classList.add("deleting");
              document.getElementById("prow_'.$p_id.'").classList.remove("undeleting");
              document.getElementById("changed_'.$p_id.'").classList.add("deleting");
              document.getElementById("changed_'.$p_id.'").classList.remove("undeleting");
              document.getElementById("changed_'.$p_id.'").innerHTML = "&nbsp;deleted&nbsp;";
              document.getElementById("readydelete'.$p_id.'").innerHTML = "ready to purge";';
} elseif ($name == 'restore') { // Trash
  $result .= 'document.getElementById("r_redelete_'.$p_id.'").style.display = "inherit";
              document.getElementById("r_restore_'.$p_id.'").style.display = "none";
              document.getElementById("r_pdelete_'.$p_id.'").style.display = "none";
              document.getElementById("prow_'.$p_id.'").classList.add("undeleting");
              document.getElementById("prow_'.$p_id.'").classList.remove("deleting");
              document.getElementById("changed_'.$p_id.'").classList.add("undeleting");
              document.getElementById("changed_'.$p_id.'").classList.remove("deleting");
              document.getElementById("changed_'.$p_id.'").innerHTML = "&nbsp;undeleted&nbsp;";
              document.getElementById("readydelete'.$p_id.'").innerHTML = "restored";';
} elseif ($name == 'purge') { // Trash
  $result .= 'document.getElementById("prow_'.$p_id.'").classList.add("deleting");
              document.getElementById("prow_'.$p_id.'").classList.remove("undeleting");
              document.getElementById("purged_'.$p_id.'").style.display = "inline";
              document.getElementById("purged_'.$p_id.'").classList.remove("undeleting");
              document.getElementById("purged_'.$p_id.'").classList.add("deleting");
              document.getElementById("changed_'.$p_id.'").style.display = "none";
              document.getElementById("showviews'.$p_id.'").remove();
              document.getElementById("showaction'.$p_id.'").remove();
              document.getElementById("readydelete'.$p_id.'").innerHTML = "purged forever";';
} elseif ($slug == 'make') { // Pieces type
  $result .= 'document.getElementById("r_make_'.$p_id.'").innerHTML = event.target.responseText;
              document.getElementById("showtypify'.$p_id.'").style.display = "none";
              document.getElementById("prow_'.$p_id.'").classList.add("renew");
              document.getElementById("prow_'.$p_id.'").classList.remove("deleting","undeleting");
              document.getElementById("changed_'.$p_id.'").classList.add("renew");
              document.getElementById("changed_'.$p_id.'").classList.remove("deleting","undeleting");
              document.getElementById("changed_'.$p_id.'").innerHTML = "&nbsp;changed type&nbsp;";
              var x = document.getElementById("ptype'.$p_id.'");
              if (x.innerHTML === "post") {
                x.innerHTML = "page";
              } else {
                x.innerHTML = "post";
              }';
} else { // Pieces status
  $result .= 'document.getElementById("r_status_'.$p_id.'").innerHTML = event.target.responseText;
              document.getElementById("showaction'.$p_id.'").style.display = "none";
              document.getElementById("prow_'.$p_id.'").classList.add("renew");
              document.getElementById("prow_'.$p_id.'").classList.remove("deleting","undeleting");
              document.getElementById("changed_'.$p_id.'").classList.add("renew");
              document.getElementById("changed_'.$p_id.'").classList.remove("deleting","undeleting");
              document.getElementById("changed_'.$p_id.'").innerHTML = "&nbsp;changed status&nbsp;";
              var x = document.getElementById("pstatus'.$p_id.'");
              if (x.innerHTML === "published") {
                x.innerHTML = "redrafting";
              } else {
                x.innerHTML = "published";
              }';
} // End action if

  $result .= '
        form = document.getElementById("pa_'.$slug.'_'.$p_id.'");
        listenToForm'.$slug.$p_id.'();
      } );
      AJAX.addEventListener( "error", function(event) {
        document.getElementById("prow_'.$p_id.'").classList.add("deleting");
        document.getElementById("changed_'.$p_id.'").classList.add("renew");
        document.getElementById("changed_'.$p_id.'").classList.remove("deleting","undeleting");
        document.getElementById("changed_'.$p_id.'").innerHTML = "'.$name.' error";
        document.getElementById("changed_'.$p_id.'").style.display = "inline";
      } );
      AJAX.open("POST", "ajax.piecesactions.php");
      AJAX.send(FD);
    }
    var form = document.getElementById("pa_'.$slug.'_'.$p_id.'");
    function listenToForm'.$slug.$p_id.'(){
      form.addEventListener( "submit", function(event) {
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
  if (!filter_var($p_id, FILTER_VALIDATE_INT)) {exit ();}

  // Choose the action
  switch ($action) {
    case 'unpublish':
      $query = "UPDATE publications SET pubstatus='redrafting' WHERE piece_id='$p_id'";
      $call = mysqli_query($database, $query);
      if ($call) {
        $piecesactionsuccess = true;
      } else {
        unset($piecesactionsuccess);
        echo '<pre>Major database error!</pre>';
        exit ();
      }

      break;
    case 'republish':
      $query = "UPDATE publications SET pubstatus='published' WHERE piece_id='$p_id'";
      $call = mysqli_query($database, $query);
      if ($call) {
        $piecesactionsuccess = true;
      } else {
        unset($piecesactionsuccess);
        echo '<pre>Major database error!</pre>';
        exit ();
      }

      break;
    case 'delete':
    case 'redelete':
      $queryd = "UPDATE pieces SET status='dead' WHERE id='$p_id'";
      $calld = mysqli_query($database, $queryd);
      $queryr = "UPDATE publications SET status='dead' WHERE piece_id='$p_id'";
      $callr = mysqli_query($database, $queryr);
      if (($calld) && ($callr)) {
        $piecesactionsuccess = true;
      } else {
        unset($piecesactionsuccess);
        echo '<pre>Major database error!</pre>';
        exit ();
      }

      break;
    case 'restore':
    case 'undelete':
      $queryd = "UPDATE pieces SET status='live' WHERE id='$p_id'";
      $calld = mysqli_query($database, $queryd);
      $queryr = "UPDATE publications SET status='live' WHERE piece_id='$p_id'";
      $callr = mysqli_query($database, $queryr);
      if (($calld) && ($callr)) {
        $piecesactionsuccess = true;
      } else {
        unset($piecesactionsuccess);
        echo '<pre>Major database error!</pre>';
        exit ();
      }

      break;
    case 'purge':
      $query1 = "DELETE FROM pieces WHERE status='dead' AND id='$p_id'";
      $call1 = mysqli_query($database, $query1);
      if ($call1) {
        $query2 = "DELETE FROM publications WHERE status='dead' AND piece_id='$p_id'";
        $call2 = mysqli_query($database, $query2);
      }
      if ($call2) {
        $query3 = "DELETE FROM publication_history WHERE piece_id='$p_id'";
        $call3 = mysqli_query($database, $query3);
      }
      if ($call3) {
        $piecesactionsuccess = true;
      } else {
        unset($piecesactionsuccess);
        echo '<pre>Major database error!</pre>';
        exit ();
      }

      break;
    case 'make post':
      $query1 = "UPDATE publications SET type='post' WHERE piece_id='$p_id'";
      $call1 = mysqli_query($database, $query1);
      $query2 = "UPDATE pieces SET type='post' WHERE id='$p_id'";
      $call2 = mysqli_query($database, $query2);
      if (($call1) && ($call2)) {
        $piecesactionsuccess = true;
      } else {
        unset($piecesactionsuccess);
        echo '<pre>Major database error!</pre>';
        exit ();
      }

      break;
    case 'make page':
      $query1 = "UPDATE publications SET type='page' WHERE piece_id='$p_id'";
      $call1 = mysqli_query($database, $query1);
      $query2 = "UPDATE pieces SET type='page' WHERE id='$p_id'";
      $call2 = mysqli_query($database, $query2);
      if (($call1) && ($call2)) {
        $piecesactionsuccess = true;
      } else {
        unset($piecesactionsuccess);
        echo '<pre>Major database error!</pre>';
        exit ();
      }

      break;
  }

} // Finish function
