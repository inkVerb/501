<!DOCTYPE html>
<html>
<body>

<?php

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

  $name = ( (isset($_POST['name'])) &&
  ($_POST['name'] != '') )
  // Sanitize: preg_replace non-accepted characters with nothing
  ? preg_replace("/[^a-zA-Z0-9]/","", $_POST['name']) : 'Not a valid name!';

  $username = ( (isset($_POST['username'])) &&
  ($_POST['name'] != '') )
  // Sanitize: strtolower convert to lowercase
  ? strtolower($_POST['username']) : 'Not a valid username!';

  echo "<b>Name: $name</b><br><b>Username: $username</b><br><br>";

}

echo '
<form action="phppost.php" method="post">
  Name: <input type="text" name="name" placeholder="John Doe..." value="'.$name.'"><br><br>
  Username: <input type="text" name="username" placeholder="abc123..." value="'.$username.'"><br><br>

  <input type="submit" value="Submit Button">
</form>
';

?>

</body>
</html>
