<!DOCTYPE html>
<html>
<body>

<?php

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  var_dump($_POST);
}

?>

<form method="post" action="phpvardump.php">
  <input type="text" name="one">
  <input type="text" name="two">
  <input type="text" name="three">
  <input type="text" name="four">
  <input type="text" name="five">
  <input type="submit" name="sbmt" value="Hit me">
</form>



</body>
</html>
