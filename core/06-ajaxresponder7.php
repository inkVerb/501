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
  <button type="button" onclick="ajaxFormData(\'ajaxForm\', \'ajax_responder.php\', \'ajax_changes\');">Button Form AJAX! (made by AJAX)</button>
  <input type="submit" value="Submit Form non-AJAX! (made by AJAX)">
</form>
';
}
?>
