<?php

// Create our time
$p_live_yr_curr = date("Y");
$p_live_mo_curr = date("m");
$p_live_day_curr = date("d");
$p_live_hr_curr = date("H");
$p_live_min_curr = date("i");
$p_live_sec_curr = date("s");

// Define the Validation-Sanitization check function, useable for all our checks
function checkPiece($name, $value) {

  // We need our time variables
  global $p_live_yr_curr;
  global $p_live_mo_curr;
  global $p_live_day_curr;
  global $p_live_hr_curr;
  global $p_live_min_curr;
  global $p_live_sec_curr;

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

  } elseif ($name == 'p_content') {
    $result = htmlentities($value);

  } elseif ($name == 'p_after') {
    $result = htmlentities($value);


  // Date-time Live
  } elseif ($name == 'p_live_schedule') {
    $result = ($value == true)
    ? true : false;
    define (p_live_schedule, $result); // Define a constant to allow scheduling so it edures multiple function calls
    // lowercase because it won't be used everywhere in our app

  } elseif ($name == 'p_live_yr') {
    $regex = '/[0-9]{4}$/i';
    // Our date range is from the creation of the Gutenberg press through the millenium of Christ
    $result = (((preg_match($regex, $value)) && (1500 <= $value) && (3300 >= $max)) && (p_live_schedule == true))
    ? $value : $p_live_yr_curr;
    define (limit_day_yr, $result); // Define a constant for date-range so it edures multiple function calls
    // lowercase because it won't be used everywhere in our app

  } elseif ($name == 'p_live_mo') {
    $regex = '/(0[1-9]|1[0-2])$/i';
    $result = ((preg_match($regex, $value)) && (p_live_schedule == true))
    ? $value : $p_live_mo_curr;
    define (limit_day_mo, $result); // Define a constant for date-range so it edures multiple function calls
    // lowercase because it won't be used everywhere in our app

  } elseif ($name == 'p_live_day') {
    $regex = '/(0[1-9]|1[0-9]|2[0-9]|3[01])$/i';
    $result = ((preg_match($regex, $value)) && (p_live_schedule == true))
    ? $value : $p_live_day_curr;

    // Check date range per month
    if ((limit_day_mo == '04') || (limit_day_mo == '06') || (limit_day_mo == '09') || (limit_day_mo == '11')) {
      if ($result > 30) {$result = 30;}
    } elseif (limit_day_mo == '02') { // Leap year applies: 1. every 4 years, 2. not on centuries, 3. but on millennia
      if ( (limit_day_yr%4 == 0) && ((limit_day_yr%100 != 0) || (limit_day_yr == 2000) || (limit_day_yr == 3000)) ) {
        if ($result > 29) {$result = 29;}
      } else {
        if ($result > 28) {$result = 28;}
      }
    }

  } elseif ($name == 'p_live_hr') {
    $regex = '/([0-1][0-9]|2[0-3])$/i';
    $result = ((preg_match($regex, $value)) && (p_live_schedule == true))
    ? $value : $p_live_hr_curr;

  } elseif ($name == 'p_live_min') {
    $regex = '/([0-5][0-9])$/i';
    $result = ((preg_match($regex, $value)) && (p_live_schedule == true))
    ? $value : $p_live_min_curr;

  } elseif ($name == 'p_live_sec') {
    $regex = '/([0-5][0-9])$/i';
    $result = ((preg_match($regex, $value)) && (p_live_schedule == true))
    ? $value : $p_live_sec_curr;

  } // Finish $name if

  return $result;
} // Finish function

// Create the web form input function, usable for all inputs
function pieceInput($name, $value) {

  // We need our time variables
  global $p_live_yr_curr;
  global $p_live_mo_curr;
  global $p_live_day_curr;
  global $p_live_hr_curr;
  global $p_live_min_curr;
  global $p_live_sec_curr;

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
      $status_live = '<option value="live" selected>Live</option>';
    } else {
      $status_live = '<option value="live">Live</option>';
    }
    // draft selected?
    if ($value == 'draft' || ($value == '')) {
      $status_draft = '<option value="draft" selected>Draft</option>';
    } else {
      $status_draft = '<option value="draft">Draft</option>';
    }
    // draft selected?
    if ($value == 'type_page') {
      $status_dead = '<option value="dead" selected>Dead</option>';
    } else {
      $status_dead = '<option value="dead">Dead</option>';
    }
    $result = '
    <select name="p_status" id="p_status">
      '.$status_live.'
      '.$status_draft.'
      '.$status_dead.'
    </select>';

  } elseif ($name == 'p_content') {
    $result = '<textarea id="p_content" name="p_content">'.$value.'</textarea>';

  } elseif ($name == 'p_after') {
    $result = '<textarea id="p_after" name="p_after">'.$value.'</textarea>';

  // Date-time Live
} elseif ($name == 'p_live_yr') {
  $value = ( $value == '' ) ? $p_live_yr_curr : $value; // If the value is empty, use the current time
  $result = '<input type="text" id="p_live_yr" name="p_live_yr" style="width: 2.2em" maxlength="4" value="'.$value.'">';

} elseif ($name == 'p_live_mo') {
  $value = ( $value == '' ) ? $p_live_mo_curr : $value;
  // Notice our string inline ternary statements from Lesson 1
    $result = '<select type="text" id="p_live_mo" name="p_live_mo" value="'.$value.'">
      <option value="01"'.( $value == '01' ? ' selected>': '>').'01-Jan</option>
      <option value="02"'.( $value == '02' ? ' selected>': '>').'02-Feb</option>
      <option value="03"'.( $value == '03' ? ' selected>': '>').'03-Mar</option>
      <option value="04"'.( $value == '04' ? ' selected>': '>').'04-Apr</option>
      <option value="05"'.( $value == '05' ? ' selected>': '>').'05-May</option>
      <option value="06"'.( $value == '06' ? ' selected>': '>').'06-Jun</option>
      <option value="07"'.( $value == '07' ? ' selected>': '>').'07-Jul</option>
      <option value="08"'.( $value == '08' ? ' selected>': '>').'08-Aug</option>
      <option value="09"'.( $value == '09' ? ' selected>': '>').'09-Sep</option>
      <option value="10"'.( $value == '10' ? ' selected>': '>').'10-Oct</option>
      <option value="11"'.( $value == '11' ? ' selected>': '>').'11-Nov</option>
      <option value="12"'.( $value == '12' ? ' selected>': '>').'12-Dec</option>
    </select>';

  } elseif ($name == 'p_live_day') {
    $value = ( $value == '' ) ? $p_live_day_curr : $value;
    $result = '<input type="text" id="p_live_day" name="p_live_day" style="width: 1.2em" maxlength="2" value="'.$value.'">';

  } elseif ($name == 'p_live_hr') {
    $value = ( $value == '' ) ? $p_live_hr_curr : $value;
    $result = '<input type="text" id="p_live_hr" name="p_live_hr" style="width: 1.2em" maxlength="2" value="'.$value.'">';

  } elseif ($name == 'p_live_min') {
    $value = ( $value == '' ) ? $p_live_min_curr : $value;
    $result = '<input type="text" id="p_live_min" name="p_live_min" style="width: 1.2em" maxlength="2" value="'.$value.'">';

  } elseif ($name == 'p_live_sec') {
    $value = ( $value == '' ) ? $p_live_sec_curr : $value;
    $result = '<input type="text" id="p_live_sec" name="p_live_sec" style="width: 1.2em" maxlength="2" value="'.$value.'">';

  } elseif ($name == 'p_live_schedule') {
    if ($value == true) {
      $result = '<input type="checkbox" id="p_live_schedule" name="p_live_schedule" onclick="showGoLiveOptionsBox()" checked>';
    } else {
      $result = '<input type="checkbox" id="p_live_schedule" name="p_live_schedule" onclick="showGoLiveOptionsBox()">';
    }

  } // Finish $name if

  return $result;
} // Finish function