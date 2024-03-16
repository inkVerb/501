<!DOCTYPE html>
<html>
<body>

<?php

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

  $website = ( (isset($_POST['website']))
          &&  ((filter_var($_POST['website'],FILTER_VALIDATE_URL)) && (strlen($_POST['website']) <= 128)) )
  ? $_POST['website'] : 'Not a website!';

  $email = ( (isset($_POST['email']))
        &&  ((filter_var($_POST['email'],FILTER_VALIDATE_EMAIL)) && (strlen($_POST['email']) <= 128)) )
  ? $_POST['email'] : 'Not an email!';

  $number = ( (isset($_POST['number']))
         &&   (preg_match('/^[0-9]{1,32}$/', $_POST['number'])) )
  //(filter_var($_POST['variable'], FILTER_VALIDATE_INT)) )
  //(filter_var($_POST['variable'], FILTER_VALIDATE_INT, array("options"=>array('min_range'=>0, 'max_range'=>100)))) )
  ? $_POST['number'] : 'Not a valid number!';

  $fullname = ( (isset($_POST['fullname']))
           &&   (preg_match('/^[a-zA-Z ]{6,32}$/i', $_POST['fullname'])) )
  ? $_POST['fullname'] : 'Not a valid name!';

  $username = ( (isset($_POST['username']))
           &&   (preg_match('/^[a-zA-Z0-9_]$/i', $_POST['username'])) )
  ? $_POST['username'] : 'Not a valid username!';

  $password = ( (isset($_POST['password']))
           &&   (preg_match('/^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])[0-9a-zA-Z!@&#$%]{6,32}$/', $_POST['password'])) )
  ? $_POST['password'] : 'Not a valid password!';

  echo "Website: <b>$website</b><br>Email: <b>$email</b><br>Favorite number: <b>$number</b><br>Name: <b>$fullname</b><br>Username: <b>$username</b><br>Password: <b>$password</b><br><br>";

}

echo '
<form action="phppost.php" method="post">
  Website: <input type="text" name="website" placeholder="http://..." value="'.$website.'"><br><br>
  Email: (valid email address) <input type="text" name="email" placeholder="johndoe@verb.vip..." value="'.$email.'"><br><br>
  Favorite number: (6-32 characters, numbers only) <input type="text" name="number" placeholder="123456..." value="'.$number.'"><br><br>
  Name: (6-32 characters, letters only) <input type="text" name="fullname" placeholder="John Doe..." value="'.$fullname.'"><br><br>
  Username: (any length, only letters, numbers, and underscore) <input type="text" name="username" placeholder="abc123..." value="'.$username.'"><br><br>
  Password: (6-32 characters, one lowercase letter, one uppercase letter, one number, also allowed: ! @ & # $ %)<br>
  <input type="text" name="password" placeholder="Abcd123..." value="'.$password.'"><br><br>

  <input type="submit" value="Submit Button">
</form>
';

?>

</body>
</html>
