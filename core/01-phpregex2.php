<!DOCTYPE html>
<html>
<body>

<?php

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

  $fullname = ( (isset($_POST['fullname'])) &&
  ($_POST['fullname'] != '') )
  // Sanitize: preg_replace non-accepted characters with nothing
  ? preg_replace("/[^a-zA-Z0-9 ]/","", $_POST['fullname']) : 'Enter a name!';

  $username = ( (isset($_POST['username'])) &&
  ($_POST['username'] != '') )
  // Sanitize: strtolower convert to lowercase
  ? strtolower($_POST['username']) : 'Enter a username!';

  $trimme = ( (isset($_POST['trimme'])) &&
  ($_POST['trimme'] != '') )
  // Sanitize: trim to remove whitespace at start and end
  ? trim(preg_replace('/\s+/', ' ', $_POST['trimme'])) : 'Enter a trimme!';

  echo "<b>Name: $fullname</b><br><b>Username: $username</b><br><b>Trim me: $trimme</b><br><br>";

}

echo '
<form action="phppost.php" method="post">
  Name: <input type="text" name="fullname" placeholder="John Doe..." value="'.$fullname.'"><br><br>
  Username: <input type="text" name="username" placeholder="abc123..." value="'.$username.'"><br><br>
  Trim me: <input type="text" name="trimme" placeholder="  ab  c1  2  3  ..." value="'.$trimme.'"><br><br>
  (For Username and Trim me: Try adding spaces and double spaces at start, middle, and end)<br><br>

  <input type="submit" value="Submit Button">
</form>
';

?>

</body>
</html>
