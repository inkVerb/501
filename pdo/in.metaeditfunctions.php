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
  <form method="post" id="pa_'.$slug.'_'.$p_id.'" action="ajax.piecesactions.php" style="float: '.$float_.';" class="postform inline">
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
              document.getElementById("prow_'.$p_id.'").classList.remove("metaupdate","undeleting","renew");
              document.getElementById("changed_'.$p_id.'").classList.add("deleting");
              document.getElementById("changed_'.$p_id.'").classList.remove("metaupdate","undeleting","renew");
              document.getElementById("changed_'.$p_id.'").innerHTML = "&nbsp;deleting&nbsp;";';
} elseif ($name == 'undelete') { // Pieces
  $result .= 'document.getElementById("r_undelete_'.$p_id.'").style.display = "none";
              document.getElementById("r_delete_'.$p_id.'").style.display = "inherit";
              document.getElementById("r_status_'.$p_id.'").style.display = "inherit";
              document.getElementById("pdeleting'.$p_id.'").style.display = "none";
              document.getElementById("pstatus'.$p_id.'").style.display = "inline";
              document.getElementById("prow_'.$p_id.'").classList.add("undeleting");
              document.getElementById("prow_'.$p_id.'").classList.remove("metaupdate","deleting","renew");
              document.getElementById("changed_'.$p_id.'").classList.add("undeleting");
              document.getElementById("changed_'.$p_id.'").classList.remove("metaupdate","deleting","renew");
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
              document.getElementById("readydelete'.$p_id.'").innerHTML = "&#10008; ready to purge";';
} elseif ($name == 'restore') { // Trash
  $result .= 'document.getElementById("r_redelete_'.$p_id.'").style.display = "inherit";
              document.getElementById("r_restore_'.$p_id.'").style.display = "none";
              document.getElementById("r_pdelete_'.$p_id.'").style.display = "none";
              document.getElementById("prow_'.$p_id.'").classList.add("undeleting");
              document.getElementById("prow_'.$p_id.'").classList.remove("deleting");
              document.getElementById("changed_'.$p_id.'").classList.add("undeleting");
              document.getElementById("changed_'.$p_id.'").classList.remove("deleting");
              document.getElementById("changed_'.$p_id.'").innerHTML = "&nbsp;undeleted&nbsp;";
              document.getElementById("readydelete'.$p_id.'").innerHTML = "&#9851; restored";';
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
              document.getElementById("prow_'.$p_id.'").classList.remove("metaupdate","deleting","undeleting");
              document.getElementById("changed_'.$p_id.'").classList.add("renew");
              document.getElementById("changed_'.$p_id.'").classList.remove("metaupdate","deleting","undeleting");
              document.getElementById("changed_'.$p_id.'").innerHTML = "&nbsp;changed type&nbsp;";
              var x = document.getElementById("ptype'.$p_id.'");
              if (x.innerHTML.includes("post")) {
                x.innerHTML = "&#10081; page";
              } else {
                x.innerHTML = "&#8267; post";
              }';
} else { // Pieces status
  $result .= 'document.getElementById("r_status_'.$p_id.'").innerHTML = event.target.responseText;
              document.getElementById("showaction'.$p_id.'").style.display = "none";
              document.getElementById("prow_'.$p_id.'").classList.add("renew");
              document.getElementById("prow_'.$p_id.'").classList.remove("metaupdate","deleting","undeleting");
              document.getElementById("changed_'.$p_id.'").classList.add("renew");
              document.getElementById("changed_'.$p_id.'").classList.remove("metaupdate","deleting","undeleting");
              document.getElementById("changed_'.$p_id.'").innerHTML = "&nbsp;changed status&nbsp;";
              var x = document.getElementById("pstatus'.$p_id.'");
              if (x.innerHTML.includes("published")) {
                x.innerHTML = "&#10001; redrafting";
              } else {
                x.innerHTML = "&#10004; published";
              }';
} // End action if

  $result .= '
        form = document.getElementById("pa_'.$slug.'_'.$p_id.'");
        listenToForm'.$slug.$p_id.'();
      } );
      AJAX.addEventListener( "error", function(event) {
        document.getElementById("prow_'.$p_id.'").classList.add("deleting");
        document.getElementById("changed_'.$p_id.'").classList.add("renew");
        document.getElementById("changed_'.$p_id.'").classList.remove("metaupdate","deleting","undeleting");
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
  global $pdo;

  // Validate the $p_id
  if (!filter_var($p_id, FILTER_VALIDATE_INT)) {exit ();}

  // Choose the action
  switch ($action) {
    case 'unpublish':
      $query = $database->prepare("UPDATE publications SET pubstatus='redrafting' WHERE id=:id");
      $query->bindParam(':id', $p_id);
      $pdo->exec_($query);
      $call = $pdo->ok;
      if ($call) {
        $piecesactionsuccess = true;
      } else {
        unset($piecesactionsuccess);
        echo '<pre>Major database error!</pre>';
        exit ();
      }

      break;
    case 'republish':
      $query = $database->prepare("UPDATE publications SET pubstatus='published' WHERE id=:id");
      $query->bindParam(':id', $p_id);
      $pdo->exec_($query);
      $call = $pdo->ok;
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
      $query = $database->prepare("UPDATE pieces SET status='dead' WHERE id=:id");
      $query->bindParam(':id', $p_id);
      $pdo->exec_($query);
      $calld = $pdo->ok;
      $query = $database->prepare("UPDATE publications SET status='dead' WHERE piece_id=:piece_id");
      $query->bindParam(':piece_id', $p_id);
      $pdo->exec_($query);
      $callr = $pdo->ok;
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
      $query = $database->prepare("UPDATE pieces SET status='live' WHERE id=:id");
      $query->bindParam(':id', $p_id);
      $pdo->exec_($query);
      $calld = $pdo->ok;
      $query = $database->prepare("UPDATE publications SET status='live' WHERE piece_id=:piece_id");
      $query->bindParam(':piece_id', $p_id);
      $pdo->exec_($query);
      $callr = $pdo->ok;
      if (($calld) && ($callr)) {
        $piecesactionsuccess = true;
      } else {
        unset($piecesactionsuccess);
        echo '<pre>Major database error!</pre>';
        exit ();
      }

      break;
    case 'purge':
      $query = $database->prepare("DELETE FROM pieces WHERE status='dead' AND id=:id");
      $query->bindParam(':id', $p_id);
      $pdo->exec_($query);
      $call1 = $pdo->ok;
      if ($call1) {
        $query = $database->prepare("DELETE FROM publications WHERE status='dead' AND piece_id=:piece_id");
        $query->bindParam(':piece_id', $p_id);
        $pdo->exec_($query);
        $call2 = $pdo->ok;
      }
      if ($call2) {
        $query = $database->prepare("DELETE FROM publication_history WHERE piece_id=:piece_id");
        $query->bindParam(':piece_id', $p_id);
        $pdo->exec_($query);
        $call3 = $pdo->ok;
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
      $query = $database->prepare("UPDATE publications SET type='post' WHERE piece_id=:piece_id");
      $query->bindParam(':piece_id', $p_id);
      $pdo->exec_($query);
      $call1 = $pdo->ok;
      $query = $database->prepare("UPDATE pieces SET type='post' WHERE id=:id");
      $query->bindParam(':id', $p_id);
      $pdo->exec_($query);
      $call2 = $pdo->ok;
      if (($call1) && ($call2)) {
        $piecesactionsuccess = true;
      } else {
        unset($piecesactionsuccess);
        echo '<pre>Major database error!</pre>';
        exit ();
      }

      break;
    case 'make page':
      $query = $database->prepare("UPDATE publications SET type='page' WHERE piece_id=:piece_id");
      $query->bindParam(':piece_id', $p_id);
      $pdo->exec_($query);
      $call1 = $pdo->ok;
      $query = $database->prepare("UPDATE pieces SET type='page' WHERE id=:id");
      $query->bindParam(':id', $p_id);
      $pdo->exec_($query);
      $call2 = $pdo->ok;
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
