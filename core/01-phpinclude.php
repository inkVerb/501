<!DOCTYPE html>
<html>
<head>
  <!-- CSS to make our class="error" stuff red -->
  <style>
    form input.error {
    	border: 2px solid #cc3333;
    }
    .error {
    	color: #cc3333;
    	font-weight: thick;
    }
  </style>
</head>
<body>

<?php

// Include our file with the functions
include ('./in.phppost.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

  // See if empty
  //if (isset($_POST['website'])) { // Not this!
  //if (isset($_POST['website'])) && ($_POST['website'] != '') { // Better
  if (!empty($_POST['website'])) { // Simpler same thing
    $website = checkPost('website',$_POST['website']);
  } else { // Empty error message in $check_err array
    $check_err['website'] = 'Enter a website address!';
  }

  if (!empty($_POST['email'])) {
    $email = checkPost('email',$_POST['email']);
  } else {
    $check_err['email'] = 'Enter an email address!';
  }

  if (!empty($_POST['number'])) {
    $number = checkPost('number',$_POST['number']);
  } else {
    $check_err['number'] = 'Enter your favorite number!';
  }

  if (!empty($_POST['name'])) {
    $name = checkPost('name',$_POST['name']);
  } else {
    $check_err['name'] = 'Enter your name!';
  }

  if (!empty($_POST['username'])) {
    $username = checkPost('username',$_POST['username']);
  } else {
    $check_err['username'] = 'Enter your username!';
  }

  if (!empty($_POST['password'])) {
    // Second test to see if passwords match
    if ($_POST['password'] == $_POST['password2']) {
      $password = checkPost('password',$_POST['password']);
    } else { // Error if no match for double-check password
      $check_err['password2'] = 'Passwords much match!';
    }
  } else { // Error if empty
    $check_err['password'] = 'Enter a password!';
  }

  // Show our results if $check_err is empty
  if (empty($check_err)) {
    echo "Website: <b>$website</b><br>Email: <b>$email</b><br>Favorite number: <b>$number</b><br>Name: <b>$name</b><br>Username: <b>$username</b><br>Password: <b>$password</b><br><br>";
  }

} // Finish POST if


echo '
<form action="phppost.php" method="post">';

echo formInput('website', $website, $check_err);
echo formInput('email', $email, $check_err);
echo formInput('number', $number, $check_err);
echo formInput('name', $name, $check_err);
echo formInput('username', $username, $check_err);
echo formInput('password', $password, $check_err);
echo formInput('password2', $password2, $check_err);

echo '
  <input type="submit" value="Submit Button">
</form>
';

?>

</body>
</html>
