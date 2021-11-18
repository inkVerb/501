<?php

function metaeditform($name, $p_id) {

  // Validate the $p_id
  if (!filter_var($p_id, FILTER_VALIDATE_INT)) {exit ();}

  // Get the page we're going to
  switch ($name) {
    case 'undelete':
      $post_to = 'undelete.php';
      $color_class = 'orange';
      $float_ = 'left';
      break;
    case 'unpublish':
      $post_to = 'unpublish.php';
      $color_class = 'orange';
      $float_ = 'left';
      break;
    case 'republish':
      $post_to = 'republish.php';
      $color_class = 'green';
      $float_ = 'left';
      break;
    case 'delete':
      $post_to = 'delete.php';
      $color_class = 'red';
      $float_ = 'right';
      break;
    case 'delete forever':
      $post_to = 'purge_delete.php';
      $color_class = 'red';
      $float_ = 'right';
      break;
    case 'restore':
      $post_to = 'undelete_trash.php';
      $color_class = 'orange';
      $float_ = 'left';
      break;
    case 'purge':
      $post_to = 'purge_delete_trash.php';
      $color_class = 'red';
      $float_ = 'right';
      break;
    case 'make post':
      $post_to = 'postify.php';
      $color_class = 'blue';
      $float_ = 'left';
      break;
    case 'make page':
      $post_to = 'pagify.php';
      $color_class = 'blue';
      $float_ = 'left';
      break;
  }

  $result = '
<form method="post" action="'.$post_to.'" style="float: '.$float_.';" class="postform inline">
  <input type="hidden" name="p" value="'.$p_id.'">
  <button type="submit" class="postform inline link-button '.$color_class.'">'.$name.'</button>
</form>';

  return $result;
} // Finish function
