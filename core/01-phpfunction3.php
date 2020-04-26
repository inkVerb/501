<!DOCTYPE html>
<html>
<body>

<?php

// Define the Validation-Sanitization check function, useable for all our checks
function checkPost($name, $value) {

  // Use an if test to run the proper check for each value
  if ($name == 'website') {
    $result = (filter_var($value,FILTER_VALIDATE_URL))
    ? preg_replace("/[^a-zA-Z0-9-_:\/.]/","", $value) : 'Not a website!';

  } elseif ($name == 'email') {
    $result = (filter_var($value,FILTER_VALIDATE_EMAIL))
    ? preg_replace("/[^a-zA-Z0-9-_@.]/","", $value) : 'Not an email!';

  } elseif ($name == 'number') {
    $result = (filter_var($value, FILTER_VALIDATE_INT, array("options"=>array('min_range'=>0, 'max_range'=>100))))
    ? preg_replace("/[^0-9]/"," ", $value) : 'Not a valid number!';

  } elseif ($name == 'name') {
    $result = (preg_match('/^[a-zA-Z]{6,32}$/i', $value))
    ? preg_replace("/[^a-zA-Z]/","", $value) : 'Not a valid name!';

  } elseif ($name == 'username') {
    $result = (preg_match('/[a-zA-Z0-9_]{6,32}$/i', $value))
    ? preg_replace("/[^a-zA-Z0-9_]/","", strtolower($value)) : 'Not a valid username!';

  } elseif ($name == 'password') {
    $result = (preg_match('/^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])[0-9a-zA-Z!@&#$%]{6,32}$/', $value))
    ? preg_replace("/[^a-zA-Z0-9!@&#$%]/","", $value) : 'Not a valid password!';

  } // Finish $name if

  return $result;
} // Finish function

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

  $website = checkPost('website',$_POST['website']);

  $email = checkPost('email',$_POST['email']);

  $number = checkPost('number',$_POST['number']);

  $name = checkPost('name',$_POST['name']);

  $username = checkPost('username',$_POST['username']);

  $password = checkPost('password',$_POST['password']);


  echo "Website: <b>$website</b><br>Email: <b>$email</b><br>Favorite number: <b>$number</b><br>Name: <b>$name</b><br>Username: <b>$username</b><br>Password: <b>$password</b><br><br>";

} // Finish POST if


// Create the web form input function, usable for all inputs
function formInput($name, $value) {

  // Use an if test to create the proper HTML input
  if ($name == 'website') {
    $result = 'Website: <input type="text" name="website" placeholder="http://..." value="'.$value.'"><br><br>';

  } elseif ($name == 'email') {
    $result = 'Email: (valid email address) <input type="text" name="email" placeholder="johndoe@verb.vip..." value="'.$value.'"><br><br>';

  } elseif ($name == 'number') {
    $result = 'Favorite number: (between 1 and 100) <input type="text" name="number" placeholder="12..." value="'.$value.'"><br><br>';

  } elseif ($name == 'name') {
    $result = 'Name: (6-32 characters, letters only) <input type="text" name="name" placeholder="John Doe..." value="'.$value.'"><br><br>';

  } elseif ($name == 'username') {
    $result = 'Username: (6-32 characters, only letters, numbers, and underscore) <input type="text" name="username" placeholder="abc123..." value="'.$value.'"><br><br>';

  } elseif ($name == 'password') {
    $result = 'Password: (6-32 characters, one lowercase letter, one uppercase letter, one number, also allowed: ! @ & # $ %)<br>
    <input type="password" name="password" placeholder="Abcd123..."><br><br>';

  } // Finish $name if

  return $result;
} // Finish function

echo '
<form action="phppost.php" method="post">';

formInput('website', $website);
formInput('email', $email);
formInput('number', $number);
formInput('name', $name);
formInput('username', $username);
formInput('password', $password);

echo '
  <input type="submit" value="Submit Button">
</form>
';

?>

</body>
</html>
