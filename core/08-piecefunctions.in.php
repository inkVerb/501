<?php

// Define the Validation-Sanitization check function, useable for all our checks
function checkPiece($name, $value) {

  // Only sanitize, no errors
  if ($name == 'p_title') {
    $result = htmlentities($value);

  } elseif ($name == 'p_slug') {
    $regex_replace = "/[^a-zA-Z0-9-]/";
    // Lowercase, all non-alnum to hyphen
    $result = strtolower(preg_replace($regex_replace,"-", $value));

  } elseif ($name == 'p_type') {
    if (($value == 'post') || ($value == 'page')) {
      $result = $value;
    } else {
      $result = 'post';
    }

  } elseif ($name == 'p_status') {
    if (($value == 'live') || ($value == 'dead')) {
      $result = $value;
    } else {
      $result = 'draft';
    }

  } elseif ($name == 'p_live') {
    $datetime_local = date("Y-m-dTH:i:s");
    if (date("Y-m-dTH:i:s", strtotime($value)) == $value) {
      $result = $value;
    } else {
      $result = $datetime_local;
    }

  } elseif ($name == 'p_content') {
    $result = htmlentities($value);

  } elseif ($name == 'p_after') {
    $result = htmlentities($value);

  } // Finish $name if

  return $result;
} // Finish function

// Create the web form input function, usable for all inputs
function pieceInput($name, $value, $errors) {

  // Use an if test to create the proper HTML input
  if ($name == 'p_title') {
    $result = '<input type="text" id="p_title" name="p_title" value="'.$value.'" required>';

  } elseif ($name == 'p_slug') {
    $result = '<input type="text" id="p_slug" name="p_slug" value="'.$value.'">';

  } elseif ($name == 'p_type') {
    // type_post checked or not?
    if (($value == 'type_post') || ($value == '')) {
      $input_post = '<input type="radio" id="type_post" name="p_type" value="type_post" checked>';
    } else {
      $input_post = '<input type="radio" id="type_post" name="p_type" value="type_post">';
    }
    // type_page checked or not?
    if ($value == 'type_page') {
      $input_page = '<input type="radio" id="type_page" name="p_type" value="type_page" checked>';
    } else {
      $input_page = '<input type="radio" id="type_page" name="p_type" value="type_page">';
    }
    // Create the full set of radio options
    $result = $input_post.'
      <label for="male">Post</label><br>
      '.$input_page.'
      <label for="female">Page</label>';

  } elseif ($name == 'p_status') {
    // live selected?
    if (($value == 'live')) {
      $status_live = '<option value="live" selected="selected">Live</option>';
    } else {
      $status_live = '<option value="live">Live</option>';
    }
    // draft selected?
    if ($value == 'draft' || ($value == '')) {
      $status_draft = '<option value="draft" selected="selected">Draft</option>';
    } else {
      $status_draft = '<option value="draft">Draft</option>';
    }
    // draft selected?
    if ($value == 'type_page') {
      $status_dead = '<option value="dead" selected="selected">Dead</option>';
    } else {
      $status_dead = '<option value="dead">Dead</option>';
    }
    $result = '
    <select name="p_status" id="p_status">
      '.$status_live.'
      '.$status_draft.'
      '.$status_dead.'
    </select>';

  } elseif ($name == 'p_live') {
    $result = '<input type="datetime-local" timezone="[[timezone]]" id="p_live" name="p_live" value="'.$value.'">';

  } elseif ($name == 'p_content') {
    $result = '<textarea id="p_content" name="p_content" value="'.$value.'">';

  } elseif ($name == 'p_after') {
    $result = '<textarea id="p_after" name="p_after" value="'.$value.'">';

  } // Finish $name if

  return $result;
} // Finish function
