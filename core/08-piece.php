<?php
// No <head> yet because we might redirect, which uses header() and might break after the <head> tag

// Include our config (with SQL) up near the top of our PHP file
include ('./in.config.php');

// Include our login cluster
$head_title = "501 Blog"; // Set a <title> name used next
include ('./in.logincheck.php');

if ((isset($_GET['p'])) && (filter_var($_GET['p'], FILTER_VALIDATE_INT))) {

  $p_id = preg_replace("/[^0-9]/"," ", $_GET['p']); // Set $p_id via sanitize non-numbers
  $query = "SELECT title, slug, content, after, date_live, date_updated FROM publications WHERE status='live' AND pubstatus='published' AND piece_id='$p_id'";

} elseif ((isset($_GET['s'])) && (preg_match('/[a-zA-Z0-9-]{1,90}$/i', $_GET['s']))) {

  $regex_replace = "/[^a-zA-Z0-9-]/"; // Sanitize all non-slug characters
  $result = strtolower(preg_replace($regex_replace,"-", $_GET['s'])); // Lowercase, all non-alnum to hyphen
  $p_slug = substr($result, 0, 90); // Limit to 90 characters
  $query = "SELECT title, piece_id, content, after, date_live, date_updated FROM publications WHERE status='live' AND pubstatus='published' AND slug='$p_slug'";

} else {
  exit (header("Location: blog.php"));
}

// Check the database for published pieces
$call = mysqli_query($database, $query);
$row = mysqli_fetch_array($call, MYSQLI_NUM);
if (mysqli_num_rows($call) == 1) {
  // Assign the values based on results from if statement just above
  $p_title = "$row[0]";
  if (isset($p_id)) {
    $p_slug = "$row[1]";
  } elseif (isset($p_slug)) {
    $p_id = $row[1];
  }
  $p_content = htmlspecialchars_decode("$row[2]"); // We used htmlspecialchars() to enter the database, now we must reverse it
  $p_after = "$row[3]";
  $p_live = "$row[4]";
  $p_update = "$row[5]";

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
  echo '<br><div class="piece-content">'.$p_content.'</div>';

  // After
  echo '<br><div class="gray">'.$p_after.'</div><br>';

  // Edit for logged-in users
  if (isset($user_id)) {
    echo '<p><a href="edit.php?p='.$p_id.'">edit</a></p>';
  }

  // Separater
  echo '<hr>';

} else {
  echo '<h1>Nothing here!</h1>';
  exit();
}

?>

</body>
</html>
