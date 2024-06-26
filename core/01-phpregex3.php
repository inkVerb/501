<!DOCTYPE html>
<html>
<body>

<?php

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

  $fullname = ( (isset($_POST['fullname']))
  // Validate: preg_match RegEx
           &&   (preg_match('/^[a-zA-Z ]{6,32}$/i', $_POST['fullname'])) )
  // preg_replace non-accepted characters with nothing
  ? preg_replace("/[^a-zA-Z ]/","", $_POST['fullname']) : 'Not a valid name!';

  $username = ( (isset($_POST['username']))
  // Validate: preg_match RegEx
           &&   (preg_match('/^[a-zA-Z0-9_]{6,32}$/i', $_POST['username'])) )
  // Sanitize: preg_replace & strtolower
  ? preg_replace("/[^a-zA-Z0-9_]/","", strtolower($_POST['username'])) : 'Not a valid username!';

  $password = ( (isset($_POST['password']))
  // Validate: preg_match RegEx
           &&   (preg_match('/^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])[0-9a-zA-Z!@&#$%]{6,32}$/', $_POST['password'])) )
  // Sanitize: preg_replace
  ? preg_replace("/[^a-zA-Z0-9!@&#$%]/","", $_POST['password']) : 'Not a valid password!';

  $number = ( (isset($_POST['number']))
  // Validate: filter_var matches a native PHP integer check: FILTER_VALIDATE_INT
         &&   (filter_var($_POST['number'], FILTER_VALIDATE_INT, array("options"=>array('min_range'=>0, 'max_range'=>100)))) )
  // Sanitize: preg_replace
  ? preg_replace("/[^0-9]/"," ", $_POST['number']) : 'Not a valid number!';

  $website = ( (isset($_POST['website']))
  // Validate:
    // filter_var FILTER_VALIDATE_URL
    // strlen truncate after 128 characters
          &&  ((filter_var($_POST['website'],FILTER_VALIDATE_URL)) && (strlen($value) <= 128)) )
  // Sanitize:
    // preg_replace all non-web address characters with nothing
    // substr truncate length at 128 characters
  ? substr(preg_replace("/[^a-zA-Z0-9-_:\/.]/","", $_POST['website']),0,128) : 'Not a website!';

  $email = ( (isset($_POST['email']))
  // Validate: filter_var & strlen
        &&  ((filter_var($_POST['email'],FILTER_VALIDATE_EMAIL)) && (strlen($value) <= 128)) )
  // Sanitize: preg_replace & substr
  ? substr(preg_replace("/[^a-zA-Z0-9-_@.]/","", $_POST['email']),0,128) : 'Not an email!';

  echo "Website: <b>$website</b><br>Email: <b>$email</b><br>Favorite number: <b>$number</b><br>Name: <b>$fullname</b><br>Username: <b>$username</b><br>Password: <b>$password</b><br><br>";

}

echo '
<form action="phppost.php" method="post">
  Website: <input type="text" name="website" placeholder="http://..." value="'.$website.'"><br><br>
  Email: (valid email address) <input type="text" name="email" placeholder="johndoe@verb.vip..." value="'.$email.'"><br><br>
  Favorite number: (between 1 and 100) <input type="text" name="number" placeholder="12..." value="'.$number.'"><br><br>
  Name: (6-32 characters, letters only) <input type="text" name="fullname" placeholder="John Doe..." value="'.$fullname.'"><br><br>
  Username: (6-32 characters, only letters, numbers, and underscore) <input type="text" name="username" placeholder="abc123..." value="'.$username.'"><br><br>
  Password: (6-32 characters, one lowercase letter, one uppercase letter, one number, also allowed: ! @ & # $ %)<br>
  <input type="text" name="password" placeholder="Abcd123..." value="'.$password.'"><br><br>
  <input type="submit" value="Submit Button">
</form>
';

?>

</body>
</html>
