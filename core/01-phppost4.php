<!DOCTYPE html>
<html>
<body>

<?php

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

$goPost = $_POST['go'];

$hPost = $_POST['h'];

$timePost = $_POST['time'];

echo "POST test true, that's all.<br><br>";

}

echo '
<form action="phppost.php" method="post">
  Go: <input type="text" name="go" placeholder="Go where?" value="'.$goPost.'"><br><br>
  H: <input type="text" name="h" placeholder="State the H..." value="'.$hPost.'"><br><br>
  Time: <input type="text" name="time" placeholder="What time is it?" value="'.$timePost.'"><br><br>
  <input type="submit" value="Submit Button">
</form>
';

?>

</body>
</html>
