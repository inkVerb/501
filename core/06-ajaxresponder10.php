<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $foo = $_POST['foo'];
  $bar = $_POST['bar'];
  
  if ((!empty($_POST['ajax_token'])) && ($_POST['ajax_token'] === $_SESSION["ajax_token"])) {
    
    $ajax_legit = true;
    
  } else {
    
    echo "No script kiddies!";
    exit();
  }

  $server_name = $_SERVER['SERVER_NAME']; // automatically retrieve host to confirm origin is from the same web server
  $ajax_sending_page = 'web/ajax.php'; // Set this per AJAX-handler

  // Confirm origin server and page
  if ((!empty($_SERVER['HTTP_REFERER'])) && ($_SERVER['HTTP_REFERER'] === "http://$server_name/$ajax_sending_page")) {
    
    $ajax_referer = true;
    
  }

  echo '
  '.$foo.'
  <br>
  '.$bar.'
  <br>
  $_POST["ajax_token"]: '.$_POST['ajax_token'].'
  <br>';

  echo ($ajax_legit) ? '$_SESSION["ajax_token"] is legit: ' : '$_SESSION["ajax_token"] failed: '.$_POST['ajax_token'].' vs ';
  echo $_SESSION["ajax_token"].'<br>';

  echo ($ajax_referer) ? 'AJAX $_SERVER["HTTP_REFERER"] is legit: ' : 'AJAX $_SERVER["HTTP_REFERER"] not from us: '."http://$server_name/$ajax_sending_page".' vs ';
  echo $_SERVER['HTTP_REFERER']." vs http://$server_name/$ajax_sending_page".'<br>';

  echo '
  <form id="ajaxForm">
    <p>I am a new form created by AJAX</p>
    <input type="text" value="'.$foo.'" name="foo">
    <input type="text" value="'.$bar.'" name="bar">
    <button type="button" onclick="ajaxFormData(\'ajaxForm\', \'ajax_responder.php\', \'ajax_changes\');">Button Form AJAX! (made by AJAX)</button>
    <input type="submit" value="Submit Form non-AJAX! (made by AJAX)">
  </form>
  ';
}
?>
