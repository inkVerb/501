<?php

// Start the _SESSION so we can have access to the $_SESSION["token"]
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $foo = $_POST['foo'];
  $bar = $_POST['bar'];
  
  if ($_SERVER['HTTP_AJAX_TOKEN'] === $_SESSION["ajax_token"]) {
    
    $ajax_legit = true;
    
  } else {
    
    echo "No script kiddies!";
    exit();
  }

  echo '
  '.$foo.'
  <br>
  '.$bar.'
  <br>';

  echo ($ajax_legit) ? 'AJAX token is legit: ' : 'AJAX token failed: '.$_SERVER['HTTP_AJAX_TOKEN'].' vs ';
  echo $_SESSION["ajax_token"].'<br>';

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
