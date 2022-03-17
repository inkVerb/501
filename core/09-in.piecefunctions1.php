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
    $result = strip_tags($value); // Remove any HTML tags
    $result = preg_replace('/([0-9]$)+-+([0-9])/','$1–$2',$result); // to en-dash
    $result = str_replace(' -- ',' – ',$result); // to en-dash
    $result = str_replace('---','—',$result); // to em-dash
    $result = str_replace('--','—',$result); // to em-dash
    $result = substr($result, 0, 90); // Limit to 90 characters

  } elseif ($name == 'p_slug') {
    $regex_replace = "/[^a-zA-Z0-9-]/";
    $result = strtolower(preg_replace($regex_replace,"-", $value)); // Lowercase, all non-alnum to hyphen
    $result = substr($result, 0, 90); // Limit to 90 characters

    // Make sure the slug is not empty
    $result = ($result == '') ? 'piece' : $result;

  } elseif ($name == 'p_type') {
    if (($value == 'post') || ($value == 'page')) {
      $result = $value;
    } else {
      $result = 'post';
    }

  } elseif ($name == 'p_content') {
    $result = preg_replace('/([0-9]$)+-+([0-9])/','$1–$2',$value); // to en-dash
    $result = str_replace(' -- ',' – ',$result); // to en-dash
    $result = str_replace('---','—',$result); // to em-dash
    $result = str_replace('--','—',$result); // to em-dash
    $result = htmlspecialchars($result); // Convert HTML tags to their HTML entities

  } elseif ($name == 'p_after') {
    $result = preg_replace('/([0-9]$)+-+([0-9])/','$1–$2',$value); // to en-dash
    $result = str_replace(' -- ',' – ',$result); // to en-dash
    $result = str_replace('---','—',$result); // to em-dash
    $result = str_replace('--','—',$result); // to em-dash
    $result = strip_tags($result); // Remove any HTML tags

  // Date-time Live
  } elseif ($name == 'p_live_schedule') {
    $result = ($value == true)
    ? true : false;
    define ('p_live_schedule', $result); // Define a constant to allow scheduling so it edures multiple function calls
    // lowercase because it won't be used everywhere in our app

  } elseif ($name == 'p_live_yr') {
    $regex = '/[0-9]{4}$/i';
    // Our date range is from the creation of the Gutenberg press through the millenium of Christ
    $result = (((preg_match($regex, $value)) && (1500 <= $value) && (3300 >= $max)) && (p_live_schedule == true))
    ? $value : $p_live_yr_curr;
    define ('limit_day_yr', $result); // Define a constant for date-range so it edures multiple function calls
    // lowercase because it won't be used everywhere in our app

  } elseif ($name == 'p_live_mo') {
    $regex = '/(0[1-9]|1[0-2])$/i';
    $result = ((preg_match($regex, $value)) && (p_live_schedule == true))
    ? $value : $p_live_mo_curr;
    define ('limit_day_mo', $result); // Define a constant for date-range so it edures multiple function calls
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
    $result = '<input type="text" id="p_title" name="p_title" maxlength="90" value="'.$value.'" required>';

  } elseif ($name == 'p_slug') {
    $result = '<input type="text" id="p_slug" name="p_slug" maxlength="90" value="'.$value.'">';

  } elseif ($name == 'p_type') {
    // type_post checked or not?
    if (($value == 'post') || ($value == '')) {
      $input_post = '<input type="radio" id="type_post" name="p_type" value="post" checked>';
    } else {
      $input_post = '<input type="radio" id="type_post" name="p_type" value="post">';
    }
    // type_page checked or not?
    if ($value == 'page') {
      $input_page = '<input type="radio" id="type_page" name="p_type" value="page" checked>';
    } else {
      $input_page = '<input type="radio" id="type_page" name="p_type" value="page">';
    }
    // Create the full set of radio options
    $result = '<label for="type_post">'.$input_post.' Post</label><br>
      <label for="type_page">'.$input_page.' Page</label>';

  } elseif ($name == 'p_pubstatus') {
    // published selected?
    if (($value == 'published')) {
      $status_live = '<option value="published" selected>Published</option>';
    } else {
      $status_live = '<option value="published">Published</option>';
    }
    // redrafting selected?
    if ($value == 'redrafting' || ($value == '')) {
      $status_draft = '<option value="redrafting" selected>Drafting</option>';
    } else {
      $status_draft = '<option value="redrafting">Draftting</option>';
    }

    $result = '
    <select name="p_pubstatus" id="p_pubstatus">
      '.$status_live.'
      '.$status_draft.'
      '.$status_dead.'
    </select>';

  } elseif ($name == 'p_content') {
    $result = '<textarea id="p_content" class="tinymce_editor" name="p_content">'.$value.'</textarea>';

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
        <option value="01"'.( $value == '01' ? ' selected="selected>"': '>').'01-Jan</option>
        <option value="02"'.( $value == '02' ? ' selected="selected>"': '>').'02-Feb</option>
        <option value="03"'.( $value == '03' ? ' selected="selected>"': '>').'03-Mar</option>
        <option value="04"'.( $value == '04' ? ' selected="selected>"': '>').'04-Apr</option>
        <option value="05"'.( $value == '05' ? ' selected="selected>"': '>').'05-May</option>
        <option value="06"'.( $value == '06' ? ' selected="selected>"': '>').'06-Jun</option>
        <option value="07"'.( $value == '07' ? ' selected="selected>"': '>').'07-Jul</option>
        <option value="08"'.( $value == '08' ? ' selected="selected>"': '>').'08-Aug</option>
        <option value="09"'.( $value == '09' ? ' selected="selected>"': '>').'09-Sep</option>
        <option value="10"'.( $value == '10' ? ' selected="selected>"': '>').'10-Oct</option>
        <option value="11"'.( $value == '11' ? ' selected="selected>"': '>').'11-Nov</option>
        <option value="12"'.( $value == '12' ? ' selected="selected>"': '>').'12-Dec</option>
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
