<!DOCTYPE html>
<html>
<body>

<?php

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

  if ( (isset($_POST['go'])) && ($_POST['go'] != '') ) {
    $goPost = $_POST['go'];
    echo "Go is set to: $goPost<br>";
  }

  if ( (isset($_POST['h'])) && ($_POST['h'] != '') ) {
    $hPost = $_POST['h'];
    echo "H is set to: $hPost<br>";
  }

  if ( (isset($_POST['time'])) && ($_POST['time'] != '') ) {
    $timePost = $_POST['time'];
    echo "Time is set to: $timePost<br>";
  }
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
