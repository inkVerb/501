<!DOCTYPE html>
<html>
<body>

<?php

echo "<pre>\$_SERVER['REQUEST_METHOD'] = " . $_SERVER['REQUEST_METHOD'] . "</pre>
";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  
  echo "Using post.<br>";

} elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {

  echo "Using get.<br>";
  
}

echo "<hr>";

echo "Post array items:<br>";

echo $_POST['go'].'<br>';

echo $_POST['h'].'<br>';

echo $_POST['time'].'<br>';


echo "<br>Get array items:<br>";

echo $_GET['go'].'<br>';

echo $_GET['h'].'<br>';

echo $_GET['time'].'<br>';

echo "<hr><br><br>";

// Change method= to "get" or "post" to see the difference

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
