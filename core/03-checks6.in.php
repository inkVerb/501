<?php

  // Fruit form
  if (!empty($_POST['fruitname'])) {
    $fruitname = checkPost('fruitname',$_POST['fruitname']);
  } else {
    $check_err['fruitname'] = 'Enter a name! (2-32 characters, letters only)';
  }

  if (!empty($_POST['type'])) {
    $type = checkPost('type',$_POST['type']);
  } else {
    $check_err['type'] = 'Enter a type! (1-32 characters, letters only)';
  }

  if (!empty($_POST['prepared'])) {
    $prepared = checkPost('prepared',$_POST['prepared']);
  } else {
    $check_err['prepared'] = 'Enter the preparation!';
  }

  // Set our variable if everything checks out
  if (empty($check_err)) {
    $checks_out = true;
  }
