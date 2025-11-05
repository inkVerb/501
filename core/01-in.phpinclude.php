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
    ? preg_replace("/[^a-zA-Z0-9-_:\/.]/","", $value) : '';
    // Add an entry to $check_err array if there is an error
    if ($result == '') {
      $check_err[$name] = 'Not a website!';
    }

  } elseif ($name == 'email') {
    $result = ((filter_var($value,FILTER_VALIDATE_EMAIL)) && (strlen($value) <= 128))
    ? preg_replace("/[^a-zA-Z0-9-_@.]/","", $value) : '';
    // Add an entry to $check_err array if there is an error
    if ($result == '') {
      $check_err[$name] = 'Not an email!';
    }

  } elseif ($name == 'number') {
    $result = (filter_var($value, FILTER_VALIDATE_INT, array("options"=>array('min_range'=>0, 'max_range'=>100))))
    ? preg_replace("/[^0-9]/"," ", $value) : '';
    // Add an entry to $check_err array if there is an error
    if ($result == '') {
      $check_err[$name] = 'Not a valid number!';
    }

  } elseif ($name == 'fullname') {
    $result = (preg_match('/^[a-zA-Z ]{6,32}$/i', $value))
    ? preg_replace("/[^a-zA-Z ]/","", $value) : '';
    // Add an entry to $check_err array if there is an error
    if ($result == '') {
      $check_err[$name] = 'Not a valid name!';
    }

  } elseif ($name == 'username') {
    $result = (preg_match('/[a-zA-Z0-9_]{6,32}$/i', $value))
    ? preg_replace("/[^a-zA-Z0-9_]/","", strtolower($value)) : '';
    // Add an entry to $check_err array if there is an error
    if ($result == '') {
      $check_err[$name] = 'Not a valid username!';
    }

  } elseif ($name == 'password') {
    $result = (preg_match('/^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])[0-9a-zA-Z!@&#$%]{6,32}$/', $value))
    ? preg_replace("/[^a-zA-Z0-9!@&#$%]/","", $value) : '';
    // Add an entry to $check_err array if there is an error
    if ($result == '') {
      $check_err[$name] = 'Not a valid password!';
    }

  } // Finish $name if

  return $result;
} // Finish function

// Create the web form input function, usable for all inputs
function formInput($name, $value, $errors) {

  // Use an if test to create the proper HTML input
  if ($name == 'website') {
    $result = 'Website: <input type="text" name="website" placeholder="http://..." value="'.$value.'"';

  } elseif ($name == 'email') {
    $result = 'Email: (valid email address) <input type="text" name="email" placeholder="johndoe@verb.vip..." value="'.$value.'"';

  } elseif ($name == 'number') {
    $result = 'Favorite number: (between 1 and 100) <input type="text" name="number" placeholder="12..." value="'.$value.'"';

  } elseif ($name == 'fullname') {
    $result = 'Name: (6-32 characters, letters only) <input type="text" name="fullname" placeholder="John Doe..." value="'.$value.'"';

  } elseif ($name == 'username') {
    $result = 'Username: (6-32 characters, only letters, numbers, and underscore) <input type="text" name="username" placeholder="abc123..." value="'.$value.'"';

  } elseif ($name == 'password') {
    $result = 'Password: (6-32 characters, one lowercase letter, one uppercase letter, one number, also allowed: ! @ & # $ %)<br>
    <input type="password" name="password" placeholder="Abcd123..."';

  // Double check password
  } elseif ($name == 'password2') {
    $result = 'Double check password:<br>
    <input type="password" name="password2" placeholder="Abcd123 again..."';

  } // Finish $name if

  if (array_key_exists($name, $errors)) {
    $result = $result.' class="error"> <span class="error">'.$errors[$name].'</span>';
  } else {
    $result = $result.'>';
  }

  $result .= '<br><br>';
  //$result = $result.'<br><br>';

  return $result;
} // Finish function
