<?php

/* Generate the ridiculously long random string
$newString = alnumString(255);
$newString = alnumSpecialString(255);
$newString = digitString(255);
*/

// Alphanumeric random string
function alnumString($length = 10) {
  // if (preg_match ('/[a-zA-Z0-9]$/i', $_POST['string']))
    $chrs = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $chrsL = strlen($chrs);
    $renderedString = '';
    for ($i = 0; $i < $length; $i++) {
        $renderedString .= $chrs[rand(0, $chrsL - 1)];
    }
    return $renderedString;
}

// Alphanumeric special character random string
// if (preg_match ('/[a-zA-Z0-9!@&#$%]$/i', $_POST['string']))
function alnumSpecialString($length = 10) {
    $chrs = '!@&#$%0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $chrsL = strlen($chrs);
    $renderedString = '';
    for ($i = 0; $i < $length; $i++) {
        $renderedString .= $chrs[rand(0, $chrsL - 1)];
    }
    return $renderedString;
}

// Digits random string
// if (preg_match ('/[0-9]$/i', $_POST['string']))
// if (filter_var($_POST['string'], FILTER_VALIDATE_INT, array('min_range' => 10, 'max_range' => 10)))
function digitString($length = 10) {
    $chrs = '0123456789';
    $chrsL = strlen($chrs);
    $renderedString = '';
    for ($i = 0; $i < $length; $i++) {
        $renderedString .= $chrs[rand(0, $chrsL - 1)];
    }
    return $renderedString;
}
