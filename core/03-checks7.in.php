<?php

  // Fruit form
  if (!empty($_POST['fruitname'])) {
    $fruitname = checkPost('fruitname',$_POST['fruitname']);
    // Register a require error if we got a POST[newfruit] from the form
  } elseif (!empty($_POST['newfruit'])) {
    $check_err['fruitname'] = 'Enter a name! (2-32 characters, letters only)';
  }
  // Assign errors to an item ID if there are any
  if ((!empty($check_err['fruitname'])) && (!empty($_POST['fruitid']))) {
    $check_err['fruitid'] = $_POST['fruitid'];
  }

  if (!empty($_POST['type'])) {
    $type = checkPost('type',$_POST['type']);
    // Register a require error if we got a POST[newfruit] from the form
  } elseif (!empty($_POST['newfruit'])) {
    $check_err['type'] = 'Enter a type! (1-32 characters, letters only)';
  }
  // Assign errors to an item ID if there are any
  if ((!empty($check_err['type'])) && (!empty($_POST['fruitid']))) {
    $check_err['fruitid'] = $_POST['fruitid'];
  }

  if (!empty($_POST['prepared'])) {
    $prepared = checkPost('prepared',$_POST['prepared']);
    // Register a require error if we got a POST[newfruit] from the form
  } elseif (!empty($_POST['newfruit'])) {
    $check_err['prepared'] = 'Enter the preparation!';
  }
  // Assign errors to an item ID if there are any
  if ((!empty($check_err['prepared'])) && (!empty($_POST['fruitid']))) {
    $check_err['fruitid'] = $_POST['fruitid'];
  }

  // We only need to require these if updated an existing item with a POST[fruitid]
  if (!empty($_POST['have'])) {
    $have = checkPost('have',$_POST['have']);
    // Only add the error message if we got a POST[fruitid] from the form
  } elseif (!empty($_POST['fruitid'])) {
    $check_err['have'] = 'Select carrying status!';
  }
  // Assign errors to an item ID if there are any
  if ((!empty($check_err['have'])) && (!empty($_POST['fruitid']))) {
    $check_err['fruitid'] = $_POST['fruitid'];
  }

  if ((!empty($_POST['count'])) || (isset($_POST['count']))) { // 0 returns empty(), but not when using isset()
    $count = checkPost('count',$_POST['count']);
    // Only add the error message if we got a POST[fruitid] from the form
  } elseif (!empty($_POST['fruitid'])) {
    $check_err['count'] = 'Enter the quantity!';
  }
  // Assign errors to an item ID if there are any
  if ((!empty($check_err['count'])) && (!empty($_POST['fruitid']))) {
    $check_err['fruitid'] = $_POST['fruitid'];
  }

  if (!empty($_POST['fruitid'])) {
    $fruitid = checkPost('fruitid',$_POST['fruitid']);
    // Only add the error message if we got a POST[fruitid] from the form
  } elseif (!empty($_POST['fruitid'])) {
    $check_err['fruitid'] = 'Critical error, no ID!';
  }

  // Set our variable if everything checks out
  if (empty($check_err)) {
    $checks_out = true;
  }

  // Set our variable if we have an existing id
  if (!empty($fruitid)) {
    $update_fruit = true;
  } else {
    $new_fruit = true;
  }
