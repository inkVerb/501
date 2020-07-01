<?php

// Define our error array
$check_err = array();

// Define the Validation-Sanitization check function, useable for all our checks
function checkPost($name, $value) {

  // We need our error array inside and outside this function
  global $check_err;

  // Use an if test to run the proper check for each value
  if ($name == 'fruitname') {
    $result = (preg_match('/^[a-zA-Z ]{2,32}$/i', $value))
    ? preg_replace("/[^a-zA-Z ]/","", $value) : '';
    // Add an entry to $check_err array if there is an error
    if ($result == '') {
      $check_err[$name] = 'Not a valid name! (2-32 characters, letters only)';
    }

  } elseif ($name == 'type') {
    $result = (preg_match('/^[a-zA-Z ]{1,32}$/i', $value))
    ? preg_replace("/[^a-zA-Z ]/","", $value) : '';
    // Add an entry to $check_err array if there is an error
    if ($result == '') {
      $check_err[$name] = 'Not a valid type! (1-32 characters, letters only)';
    }

  } elseif ($name == 'prepared') {
    $result = ( ($value == 'dry') || ($value == 'fresh') || ($value == 'cooked') || ($value == 'NA') )
    ? preg_replace("/[^a-zA-Z]/","", $value) : '';
    // Add an entry to $check_err array if there is an error
    if ($result == '') {
      $check_err[$name] = 'Not a valid preparation!';
    }

  } // Finish $name if

  return $result;
} // Finish function

// Create the web form input function, usable for all inputs
function formInput($name, $value, $errors) {

  // Use an if test to create the proper HTML input
  if ($name == 'fruitname') {
    $result = 'Name: <input type="text" name="fruitname" value="'.$value.'"';

  } elseif ($name == 'type') {
    $result = 'Type: <input type="text" name="type" value="'.$value.'"';

  } elseif ($name == 'prepared') {
    // Tip: Add "selected" and "hidden" and "disabled" to have a placeholder in a <select><option> dropdown
    $result = 'Prepared:
    <select id="prepared" name="prepared"';

  } // Finish $name if

  if (array_key_exists($name, $errors)) {
    $result = $result.' class="error"> <span class="error">'.$errors[$name].'</span>';
  } else {
    $result = $result.'>';
  }

  // Finish the form for the Prepared dropdown
  if ($name == 'prepared') {
    $result .= '
      <option selected hidden disabled>Choose a preparation...</option>
      <option value="fresh">Fresh</option>
      <option value="dry">Dry</option>
      <option value="cooked">Cooked</option>
      <option value="NA">NA</option>
    </select>';
  }

  $result .= '<br><br>';

  return $result;
} // Finish function
