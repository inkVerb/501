<!DOCTYPE html>
<html>
<body>

<?php

echo $_POST['go'];

echo '<br>';

echo $_POST['h'];

echo '<br>';

echo $_POST['time'];

echo '<br>';

?>

<form action="phppost.php" method="post">
  Go: <input type="text" name="go"><br><br>
  H: <input type="text" name="h"><br><br>
  Time: <input type="text" name="time"><br><br>
  <input type="submit" value="Submit Button">
</form>

</body>
</html>
