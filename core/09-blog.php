<?php
// No <head> yet because we might redirect, which uses header() and might break after the <head> tag

// Include our config (with SQL) up near the top of our PHP file
include ('./in.config.php');

// Include our login cluster
$head_title = "501 Blog"; // Set a <title> name used next
$nologin_allowed = true; // Login requires this page
include ('./in.login_check.php');

// Check the database for published pieces
$query = "SELECT piece_id, title, slug, content, after, date_live, date_updated FROM publications WHERE type='post' AND pubstatus='published'";
$call = mysqli_query($database, $query);
// We have many entries, this will iterate one post per each
while ($row = mysqli_fetch_array($call, MYSQLI_NUM)) {
  // Assign the values
  $p_id = "$row[0]";
  $p_title = "$row[1]";
  $p_slug = "$row[2]";
  $p_content = htmlspecialchars_decode("$row[3]"); // We used htmlspecialchars() to enter the database, now we must reverse it
  $p_after = "$row[4]";
  $p_live = "$row[5]";
  $p_update = "$row[6]";

  // Linked title (we will create piece.php with a RewriteMod in a later lesson)
  echo '<h2><a href="piece.php?s='.$p_slug.'">'.$p_title.'</a></h2>';

  // Date published
  echo '<p class="gray"><small><i>'.$p_live.'</i>';
  // If updated is different, show that too
  if ($p_live != $p_update) {
    echo '<br><i>(Updated '.$p_update.')</i>';
  }
  echo '</small></p>';

  // Content
  echo '<br>'.$p_content.'<br>';

  // After
  echo '<br><div class="gray">'.$p_after.'</div><br>';

  // Edit for logged-in users
  if (isset($user_id)) {
    echo '<p><a href="edit.php?p='.$p_id.'">edit</a></p>';
  }

  // Separater
  echo '<hr>';

}

// Footer
include ('./in.footer.php');
