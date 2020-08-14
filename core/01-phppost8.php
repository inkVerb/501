<!DOCTYPE html>
<html>
<body>

<?php

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

  $color_variable = $_POST['color'];

  switch ($color_variable) {
    case "red":
      echo 'You chose red';
      break;
    case "green":
      echo 'You chose green';
      break;
    case "blue":
      echo 'You chose blue';
      break;
    default:
      echo 'You chose something completely other than RGB';
  }

}

echo '
<form action="phppost.php" method="post">
  <label for="red">
    <input type="radio" id="red" name="color" value="red">&nbsp;
  Red</label><br>

  <label for="green">
    <input type="radio" id="green" name="color" value="green">&nbsp;
  Green</label><br>

  <label for="blue">
    <input type="radio" id="blue" name="color" value="blue">&nbsp;
  Blue</label><br>

  <br>
  <input type="submit" value="Submit Button">
</form>
';

?>

</body>
</html>
