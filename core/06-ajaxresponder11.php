<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $foo = $_POST['foo'];
  $bar = $_POST['bar'];
  
  if ((!empty($_POST['ajax_token'])) && ($_POST['ajax_token'] === $_SESSION["ajax_token"])) {
    
    $ajax_post_legit = true;
    
  } else {
    
    echo "No script kiddies!";
    exit();
  }
    
  if ($_SERVER['HTTP_AJAX_TOKEN'] === $_SESSION["ajax_token"]) {
    
    $ajax_http_legit = true;
    
  } else {
    
    echo "No script kiddies!";
    exit();
  }

  echo '
  '.$foo.'
  <br>
  '.$bar.'
  <br>';

  echo ($ajax_post_legit) ? '$_SESSION["ajax_token"] is legit: ' : '$_SESSION["ajax_token"] failed: '.$_SESSION['ajax_token'].' vs ';
  echo $_POST['ajax_token'].'<br>';

  echo ($ajax_http_legit) ? 'AJAX $_SERVER["HTTP_AJAX_TOKEN"] is legit: ' : 'AJAX $_SERVER["HTTP_AJAX_TOKEN"] not from us: '.$_SESSION['ajax_token'].' vs ';
  echo $_SERVER['HTTP_AJAX_TOKEN'].'<br>';

  echo '<br>
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
