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

  } elseif ($name == 'have') {
    $result = ($value == 'true') ? 1 : 0;
    // We can't just check an empty string against a true/false (boolean) value
    if (($result != true) && ($result != false)) {
      $check_err[$name] = 'Current status not valid!';
    }

  } elseif ($name == 'count') {
    $result = ((filter_var($value, FILTER_VALIDATE_INT, array("options"=>array('min_range'=>0, 'max_range'=>10000)))) === false)
    ? 'NOT_INT' : preg_replace("/[^0-9]/","", $value);
    // We can't just check an empty string against an integer value
    if ($result == 'NOT_INT') {
      $check_err[$name] = 'Not a valid quantity!';
      $result = 0;
    }

  } elseif ($name == 'fruitid') {
    $result = (filter_var($value, FILTER_VALIDATE_INT))
    ? preg_replace("/[^0-9]/","", $value) : '';
    // Add an entry to $check_err array if there is an error
    if ($result == '') {
      $check_err[$name] = 'Not a valid id!';
    }

  } // Finish $name if

  return $result;
} // Finish function

// Create the web form input function, usable for all inputs
function formInput($name, $value, $errors, $updateid) { // Add the extra argument $updateid so our errors only show for the right item

  // We need our error array inside and outside this function
  global $check_err;

  // Use an if test to create the proper HTML input
  if ($name == 'fruitname') {
    $result = '<input type="text" name="fruitname" value="'.$value.'"';

  } elseif ($name == 'type') {
    $result = '<input type="text" name="type" value="'.$value.'"';

  } elseif ($name == 'prepared') {
    $result = '
    <select id="prepared" name="prepared"';

  } elseif ($name == 'have') {
    $result = '<div';

  } elseif ($name == 'count') {
    $result = '<input type="number" name="count" value="'.$value.'" min="0" max="10000"';
  } // Finish $name if

  if ((array_key_exists($name, $errors)) &&  // The two new tests below use $updateid so our errors only show for the right item
     ( ((!empty($_POST['newfruit'])) && ($updateid == "new")) || ((!empty($check_err['fruitid'])) && ($updateid == $check_err['fruitid'])) ) ) {
  // if (array_key_exists($name, $errors)) { // uncomment, then comment the top two lines to see the $updateid stop filtering
    $result = $result.' class="error"> <span class="error">'.$errors[$name].'</span>';
  } elseif ((array_key_exists($name, $errors)) && ($name == 'prepared')) {
    $result = $result.' class="error">';
  } else {
    $result = $result.'>';
  }

  // Finish the form for the Prepared dropdown
  if ($name == 'prepared') {
    // Tip: put ternary statement to add "selected" to the HTML to whichever value is set
    $result .= '
      <option '. (($value == "") ? " selected" : "") .' hidden disabled>Choose a preparation...</option>
      <option value="fresh"'. (($value == "fresh") ? " selected" : "") .'>Fresh</option>
      <option value="dry"'. (($value == "dry") ? " selected" : "") .'>Dry</option>
      <option value="cooked"'. (($value == "cooked") ? " selected" : "") .'>Cooked</option>
      <option value="NA"'. (($value == "NA") ? " selected" : "") .'>NA</option>
    </select>';
  // OR finish the form for the Have radio selection
  } elseif ($name == 'have') {
    // Tip: Wrap the input in its label to make all the text clickable ;-)
    $result .= '
    <label for="have">Yes<input type="radio" id="have" name="have" value="true"'.(($value == true) ? ' checked' : '').'></label><br>
    <label for="have">No<input type="radio" id="have" name="have" value="false"'.(($value == false) ? ' checked' : '').'></label><br>
    </div>';
  }
  
  if ((array_key_exists($name, $errors)) && ($name == 'prepared')) {
    $result = $result.' <span class="error">'.$errors[$name].'</span>';
  }


  $result .= '<br><br>';
  //$result = $result.'<br><br>';

  return $result;
} // Finish function
