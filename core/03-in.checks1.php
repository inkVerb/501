<?php

  if (!empty($_POST['website'])) {
    $website = checkPost('website',$_POST['website']);
  } else {
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

  if (!empty($_POST['fullname'])) {
    $fullname = checkPost('fullname',$_POST['fullname']);
  } else {
    $check_err['fullname'] = 'Enter your name!';
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
      $check_err['password2'] = 'Passwords must match!';
    }
  } else { // Error if empty
    $check_err['password'] = 'Enter a password!';
  }

  // Show our results if $check_err is empty
  if (empty($check_err)) {
    echo "Website: <b>$website</b><br>Email: <b>$email</b><br>Favorite number: <b>$number</b><br>Name: <b>$fullname</b><br>Username: <b>$username</b><br>Password: <b>$password</b><br><br>";
  }
