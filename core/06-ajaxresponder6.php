<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $foo = $_POST['foo'];
  $bar = $_POST['bar'];

  echo '
  '.$foo.'
  <br>
  '.$bar.'
  <br>
  <form id="ajaxForm">
    <p>I am a new form created by AJAX</p>
    <input type="text" value="'.$foo.'" name="foo">
    <input type="text" value="'.$bar.'" name="bar">
    <input type="submit" value="Form AJAX! (made by AJAX)">
  </form>
  ';
}
?>
