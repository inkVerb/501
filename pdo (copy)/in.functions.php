<?php

// Define our error array
$check_err = array();

// Define the Validation-Sanitization check function, useable for all our checks
function checkPost($name, $value) {

  // We need our error array inside and outside this function
  global $check_err;

  // Use an if test to run the proper check for each value
  if ($name == 'website') {
    $result = ((filter_var($value,FILTER_VALIDATE_URL)) && (strlen($value) <= 128))
    ? substr(preg_replace("/[^a-zA-Z0-9-_:\/.]/","", $value),0,128) : '';
    if ($result == '') {
      $check_err[$name] = 'Not a website!';
    }

  } elseif ($name == 'email') {
    $result = ((filter_var($value,FILTER_VALIDATE_EMAIL)) && (strlen($value) <= 128))
    ? substr(preg_replace("/[^a-zA-Z0-9-_@.]/","", $value),0,128) : '';
    if ($result == '') {
      $check_err[$name] = 'Not an email!';
    }

  } elseif ($name == 'favnumber') {
    $result = (filter_var($value, FILTER_VALIDATE_INT, array("options"=>array('min_range'=>0, 'max_range'=>100))))
    ? preg_replace("/[^0-9]/"," ", $value) : '';
    if ($result == '') {
      $check_err[$name] = 'Not a valid number! (between 1 and 100)';
    }

  } elseif ($name == 'fullname') {
    $regex_match = '/^[a-zA-Z ]{6,32}$/i';
    $regex_replace = "/[^a-zA-Z ]/";
    $result = (preg_match($regex_match, $value))
    ? preg_replace($regex_replace,"", $value) : '';
    if ($result == '') {
      $check_err[$name] = 'Not a valid name! (6-32 characters, letters and spaces only)';
    }

  } elseif ($name == 'username') {
    $regex_match = '/[a-zA-Z0-9_]{6,32}$/i';
    $regex_replace = "/[^a-zA-Z0-9_]/";
    $result = (preg_match($regex_match, $value))
    ? preg_replace($regex_replace,"", strtolower($value)) : '';
    if ($result == '') {
      $check_err[$name] = 'Not a valid username! (6-32 characters, only letters, numbers, and underscore)';
    }

  } elseif ($name == 'password') {
    $regex_match = '/^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])[0-9a-zA-Z!@&#$%]{6,32}$/';
    $regex_replace = "/[^a-zA-Z0-9!@&#$%]/";
    $result = (preg_match($regex_match, $value))
    ? preg_replace($regex_replace,"", $value) : '';
    if ($result == '') {
      $check_err[$name] = 'Not a valid password! (6-32 characters, one lowercase letter, one uppercase letter, one number, also allowed: ! @ & # $ %)';
    }

  } // Finish $name if

  return $result;
} // Finish function

// Create the web form input function, usable for all inputs
function formInput($name, $value, $errors) {

  // Use an if test to create the proper HTML input
  if ($name == 'website') {
    $result = '<input type="text" id="website" name="website" value="'.$value.'"';

  } elseif ($name == 'email') {
    $result = '<input type="text" id="email" name="email" value="'.$value.'"';

  } elseif ($name == 'favnumber') {
    $result = '<input type="text" id="favnumber" name="favnumber" value="'.$value.'"';

  } elseif ($name == 'fullname') {
    $result = '<input type="text" id="fullname" name="fullname" value="'.$value.'"';

  } elseif ($name == 'username') {
    $result = '<input type="text" id="username" name="username" value="'.$value.'"';

  } elseif ($name == 'password') {
    $result = '<input type="password" id="password" name="password"';

  // Double check password
  } elseif ($name == 'password2') {
    $result = '<input type="password" id="password2" name="password2"';

  } // Finish $name if

  if (array_key_exists($name, $errors)) {
    $result = $result.' class="error"> <span class="error">'.$errors[$name].'</span>';
  } else {
    $result = $result.'>';
  }

  return $result;
} // Finish function
