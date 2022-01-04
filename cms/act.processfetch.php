<?php
// Include our config (with SQL) up near the top of our PHP file
require_once ('./in.conf.php');
session_start();
if ((isset($_GET['f']))
&& (filter_var($_GET['f'], FILTER_VALIDATE_INT, array('min_range' => 1)))
&& (isset($_SESSION['user_id']))) {
  $agg_id = preg_replace("/[^0-9]/","", $_GET['f']);
  $process_action = true;
} else {
  echo 'problem';
  exit;
  //header("Location: $blog_web_base");
}

include ('./task.aggregatefetch.php');

// Return
header("Location: $blog_web_base/aggregator.php");

?>
