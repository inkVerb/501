<?php

// Define the Validation-Sanitization check function, useable for all our checks
function postform($name, $p_id) {

  // Get the page we're going to
  if ($name == 'undelete') {
    $post_to = 'undelete.php';
    $color_class = 'blue';
    $float_ = 'left';
  } elseif ($name == 'unpublish') {
    $post_to = 'unpublish.php';
    $color_class = 'orange';
    $float_ = 'left';
  } elseif ($name == 'republish') {
    $post_to = 'republish.php';
    $color_class = 'green';
    $float_ = 'left';
  } elseif ($name == 'publish') {
    $post_to = 'newpublish.php';
    $color_class = 'green';
    $float_ = 'left';
  } elseif ($name == 'delete') {
    $post_to = 'delete.php';
    $color_class = 'red';
    $float_ = 'right';
  } elseif ($name == 'delete forever') {
    $post_to = 'empty_delete.php';
    $color_class = 'red';
    $float_ = 'right';
  } elseif ($name == 'restore') {
    $post_to = 'undelete_trash.php';
    $color_class = 'orange';
    $float_ = 'left';
  } elseif ($name == 'permanently delete') {
    $post_to = 'empty_delete_trash.php';
    $color_class = 'red';
    $float_ = 'left';
  } elseif ($name == 'make post') {
    $post_to = 'postify.php';
    $color_class = 'blue';
    $float_ = 'left';
  } elseif ($name == 'make page') {
    $post_to = 'pagify.php';
    $color_class = 'blue';
    $float_ = 'left';
  }

  $result = '
<form method="post" action="'.$post_to.'" style ="float: '.$float_.';" class="postform inline">
  <input type="hidden" name="p" value="'.$p_id.'">
  <button type="submit" class="postform inline link-button '.$color_class.'">'.$name.'</button>
</form>';

  return $result;
} // Finish function
