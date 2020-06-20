<?php
// No <head> yet because we might redirect, which uses header() and might break after the <head> tag

// Include our config (with SQL) up near the top of our PHP file
include ('./in.config.php');

// Must be logged in
if (!isset($_SESSION['user_id'])) {
  exit(header("Location: blog.php"));
}

if ((isset($_POST['p'])) && (filter_var($_POST['p'], FILTER_VALIDATE_INT))) {
  // Set $piece_id via sanitize non-numbers
  $_SESSION['piece_id'] = preg_replace("/[^0-9]/"," ", $_POST['p']);
  exit(header("Location: edit.php"));
} else {
  exit(header("Location: blog.php"));
}
