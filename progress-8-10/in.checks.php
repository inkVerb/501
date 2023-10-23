<?php

  if (!empty($_POST['website'])) {
    $website = checkPost('website',$_POST['website']);
  }

  if (!empty($_POST['email'])) {
    $email = checkPost('email',$_POST['email']);
  }

  if (!empty($_POST['favnumber'])) {
    $favnumber = checkPost('favnumber',$_POST['favnumber']);
  }

  if (!empty($_POST['fullname'])) {
    $fullname = checkPost('fullname',$_POST['fullname']);
  }

  if (!empty($_POST['username'])) {
    $username = checkPost('username',$_POST['username']);
  }

  if (!empty($_POST['password'])) {
    $password = checkPost('password',$_POST['password']);
  }

  // Test only to see if passwords match
  if ((!empty($_POST['password2'])) && (!empty($_POST['password']))) {
    if ($_POST['password'] != $_POST['password2']) {
      $check_err['password2'] = 'Passwords must match!';
    }
  }

  // Set a variable if $check_err is empty
  if (empty($check_err)) {
    $no_form_errors = true;
  }
