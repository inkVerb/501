<!DOCTYPE html>
<html>
<body>

<?php

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

echo $_POST['go'].'<br>';

echo $_POST['h'].'<br>';

echo $_POST['time'].'<br>';

echo "<hr><br><br>";

}

$srm = $_SERVER['REQUEST_METHOD'];
echo "<pre>\$_SERVER['REQUEST_METHOD'] = $srm</pre><br>";

echo '
<form action="phppost.php" method="post">
  Go: <input type="text" name="go" placeholder="Go where?"><br><br>
  H: <input type="text" name="h" placeholder="State the H..."><br><br>
  Time: <input type="text" name="time" placeholder="What time is it?"><br><br>
  <input type="submit" value="Submit Button">
</form>
';

?>

</body>
</html>
