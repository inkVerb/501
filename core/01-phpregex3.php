<html>
<body>

<?php

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

  $website = ( (isset($_POST['website'])) &&
  (filter_var($_POST['website'],FILTER_VALIDATE_URL)) )
  // Replace all non-web address characters with nothing
  ? preg_replace("/[^a-zA-Z0-9-_:\/.]/","", $_POST['website']) : 'Not a website!';

  $email = ( (isset($_POST['email'])) &&
  (filter_var($_POST['email'],FILTER_VALIDATE_EMAIL)) )
  // Replace all non-email characters with nothing
  ? preg_replace("/[^a-zA-Z0-9-_@.]/","", $_POST['email']) : 'Not an email!';

  $number = ( (isset($_POST['number'])) &&
  (filter_var($_POST['variable'], FILTER_VALIDATE_INT, array("options"=>array('min_range'=>0, 'max_range'=>100)))) )
  // Replace all non-numbers with nothing
  ? preg_replace("/[^0-9]/"," ", $_POST['number']) : 'Not a valid number!';

  $name = ( (isset($_POST['name'])) &&
  (preg_match('/^[a-zA-Z]{6,32}$/i', $_POST['name'])) )
  // Replace all non-letters with nothing
  ? preg_replace("/[^a-zA-Z]/","", $_POST['name']) : 'Not a valid name!';

  $username = ( (isset($_POST['username'])) &&
  (preg_match('/[a-zA-Z0-9_]{6,32}$/i', $_POST['username'])) )
  // Convert to lowercase
  ? preg_replace("/[^a-zA-Z_]/","", strtolower($_POST['username'])) : 'Not a valid username!';

  $password = ( (isset($_POST['password'])) &&
  (preg_match('/^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])[0-9a-zA-Z!@&#$%]{6,32}$/', $_POST['password'])) )
  // Replace all non-password characters with nothing
  ? preg_replace("/[^a-zA-Z!@&#$%]/","", $_POST['password']) : 'Not a valid password!';

  echo "Website: <b>$website</b><br>Email: <b>$email</b><br>Favorite number: <b>$number</b><br>Name: <b>$name</b><br>Username: <b>$username</b><br>Password: <b>$password</b><br><br>";

}

echo '
<form action="phppost.php" method="post">
  Website: <input type="text" name="website" placeholder="http://..." value="'.$website.'"><br><br>
  Email: (valid email address) <input type="text" name="email" placeholder="johndoe@verb.vip..." value="'.$email.'"><br><br>
  Favorite number: (between 1 and 100) <input type="text" name="number" placeholder="123456..." value="'.$number.'"><br><br>
  Name: (6-32 characters, letters only) <input type="text" name="name" placeholder="John Doe..." value="'.$name.'"><br><br>
  Username: (6-32 characters, only letters, numbers, and underscore) <input type="text" name="username" placeholder="abc123..." value="'.$username.'"><br><br>
  Password: (6-32 characters, one lowercase letter, one uppercase letter, one number, also allowed: ! @ & # $ %)<br>
  <input type="text" name="password" placeholder="123456..." value="'.$password.'"><br><br>
  <input type="submit" value="Submit Button">
</form>

</body>
</html>
';

?>
