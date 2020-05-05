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

  if ((!empty($_POST['password'])) && (!empty($_POST['password2']))) {
    // Second test to see if passwords match
    if ($_POST['password'] == $_POST['password2']) {
      $password = checkPost('password',$_POST['password']);
    } else { // Error if no match for double-check password
      $check_err['password2'] = 'Passwords much match!';
    }
  }

  // Set a variable if $check_err is empty
  if (empty($check_err)) {
    $no_form_errors = true;
  }
