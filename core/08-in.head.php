<!DOCTYPE html>
<html>
<head>
  <!-- CSS file included as <link> -->
  <link href="style.css" rel="stylesheet" type="text/css" />

  <!-- One line of PHP with our <title> -->
  <title><?php echo $head_title; ?></title>

</head>
<body>
<h1>501 Blog</h1>

<?php // Start php

// See if we are logged in by now
if ((isset($_SESSION['user_id'])) && (isset($_SESSION['full_name']))) {

  // Set our variables
  $user_id = $_SESSION['user_id'];
  $fullname = $_SESSION['full_name'];

  // Echo our header links
  echo '<p>Hi, '.$fullname.'! <a href="account.php">Account Settings</a> | <a href="logout.php">Logout</a></p>';

}

?>
