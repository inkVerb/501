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

  } elseif ($name == 'blog_public') {
    $result = ($value == 'true')
    ? true : false;
    if (($value != 'true') && ($value != 'false')) {
      $check_err[$name] = 'Not a valid status! Something is wrong.';
    }

  } elseif ($name == 'blog_title') {
    $regex_match = '/[0-9a-zA-Z_ !@&#$%.,+-=\/|]{1,90}$/';
    $regex_replace = "/[^0-9a-zA-Z_ !@&#$%.,+-=\/|]/";
    $result = (preg_match($regex_match, $value))
    ? preg_replace($regex_replace,"", $value) : '';
    if ($result == '') {
      $check_err[$name] = 'Not a valid title! (1-90 characters, special characters allowed: ! @ & # $ % - _ . , + - = / | )';
    }

  } elseif ($name == 'blog_tagline') {
    $regex_match = '/[0-9a-zA-Z_ !@&#$%.,+-=\/|]{1,120}$/';
    $regex_replace = "/[^0-9a-zA-Z_ !@&#$%.,+-=\/|]/";
    $result = (preg_match($regex_match, $value))
    ? preg_replace($regex_replace,"", $value) : '';
    if ($result == '') {
      $check_err[$name] = 'Not a valid tagline! (1-120 characters, special characters allowed: ! @ & # $ % - _ . , + - = / | )';
    }

  } elseif ($name == 'blog_description') {
    $regex_match = '/[0-9a-zA-Z_ !@&#$%.,+-=\/|]{1,500}$/';
    $regex_replace = "/[^0-9a-zA-Z_ !@&#$%.,+-=\/|]/";
    $result = (preg_match($regex_match, $value))
    ? preg_replace($regex_replace,"", $value) : '';
    if ($result == '') {
      $check_err[$name] = 'Not a valid description! (1-500 characters, special characters allowed: ! @ & # $ % - _ . , + - = / | )';
    }

  } elseif ($name == 'blog_keywords') {
    $regex_match = '/[0-9a-zA-Z-_, ]{1,100}$/';
    $regex_replace = "/[^0-9a-zA-Z-_, ]/";
    $result = (preg_match($regex_match, $value))
    ? preg_replace($regex_replace,"", $value) : '';
    if ($result == '') {
      $check_err[$name] = 'Not valid keywords! (1-100 characters, a comma-separated list, hyphen and underscore allowed)';
    }

  } elseif ($name == 'blog_summary_words') {
    $result = (filter_var($value, FILTER_VALIDATE_INT, array("options"=>array('min_range'=>1, 'max_range'=>1000))))
    ? preg_replace("/[^0-9]/"," ", $value) : '';
    if ($result == '') {
      $check_err[$name] = 'Not a valid number of words for summary! (1-1000)';
    }

  } elseif ($name == 'blog_piece_items') {
    $result = (filter_var($value, FILTER_VALIDATE_INT, array("options"=>array('min_range'=>1, 'max_range'=>500))))
    ? preg_replace("/[^0-9]/"," ", $value) : '';
    if ($result == '') {
      $check_err[$name] = 'Not a valid number of pieces! (1-500)';
    }

  } elseif ($name == 'blog_feed_items') {
    $result = (filter_var($value, FILTER_VALIDATE_INT, array("options"=>array('min_range'=>1, 'max_range'=>500))))
    ? preg_replace("/[^0-9]/"," ", $value) : '';
    if ($result == '') {
      $check_err[$name] = 'Not a valid number of feed items! (1-500)';
    }

  } elseif ($name == 'blog_crawler_index') {
    $result = (($value == 'index') || ($value == 'noindex'))
    ? $value : '';
    if ($result == '') {
      $check_err[$name] = 'Not a valid SEO indexing option! Something is wrong.';
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

  } elseif ($name == 'blog_title') {
    $result = '<input type="text" maxlength="90" id="blog_title" name="blog_title" value="'.$value.'"';

  } elseif ($name == 'blog_tagline') {
    $result = '<input type="text" maxlength="120" id="blog_tagline" name="blog_tagline" value="'.$value.'"';

  } elseif ($name == 'blog_keywords') {
    $result = '<input type="text" maxlength="100" id="blog_keywords" name="blog_keywords" value="'.$value.'"';

  } elseif ($name == 'blog_summary_words') {
    $result = '<input type="number" min="1" max="1000" id="blog_summary_words" name="blog_summary_words" value="'.$value.'"';

  } elseif ($name == 'blog_piece_items') {
    $result = '<input type="number" min="1" max="500" id="blog_piece_items" name="blog_piece_items" value="'.$value.'"';

  } elseif ($name == 'blog_feed_items') {
    $result = '<input type="number" min="1" max="500" id="blog_feed_items" name="blog_feed_items" value="'.$value.'"';

  } elseif ($name == 'blog_public') {
    $result = '<label for="blog_public_true"><input type="radio" id="blog_public_true" name="blog_public" value="true"';
    $result .= ($value == true) ? ' checked="checked"> Public</label><br>' : '> Public</label>';
    $result .= '<label for="blog_public_false"><input type="radio" id="blog_public_false" name="blog_public" value="false"';
    $result .= ($value == false) ? ' checked="checked"> Private</label><br>' : '> Private</label>';
    if (array_key_exists($name, $errors)) {
      $result .= '<br><span class="error">'.$errors[$name].'</span><br>';
    }

  } elseif ($name == 'blog_description') {
    $result = '<textarea id="blog_description" maxlength="500" cols="70" rows="5" name="blog_description"';
    if (array_key_exists($name, $errors)) {
      $result .= '<span class="error">'.$errors[$name].'</span><br>';
    }

    if (array_key_exists($name, $errors)) {
      $result = $result.' class="error">';
    } else {
      $result = $result.'>';
    }

    $result .=  $value.'</textarea>';

  } elseif ($name == 'blog_crawler_index') {
    if (array_key_exists($name, $errors)) {
      $result .= '<span class="error">'.$errors[$name].'</span><br>';
    }

    $result = '<select id="blog_crawler_index" name="blog_crawler_index" form="blog_settings"';
    if (array_key_exists($name, $errors)) {
      $result = $result.' class="error">';
    } else {
      $result = $result.'>';
    }

     $result .= '<option value="index"';
     $result .= ($value == 'index') ? ' selected="selected"' : '';
     $result .= '>index</option>';

     $result .= '<option value="noindex"';
     $result .= ($value == 'noindex') ? ' selected="selected"' : '';
     $result .= '>noindex</option>';
     $result .= '</select>';

  } // Finish $name if

  if ((array_key_exists($name, $errors)) && ($name != 'blog_crawler_index') && ($name != 'blog_description') && ($name != 'blog_public')) {
    $result = $result.' class="error"> <span class="error">'.$errors[$name].'</span>';
  } elseif (($name != 'blog_crawler_index') && ($name != 'blog_description') && ($name != 'blog_public')) {
    $result = $result.'>';
  }

  return $result;
} // Finish function
