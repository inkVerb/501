<?php
// No <head> yet because we might redirect, which uses header() and might break after the <head> tag

// Include our config (with SQL) up near the top of our PHP file
include ('./in.config.php');

// Include our login cluster
$head_title = "501 Blog"; // Set a <title> name used next
$nologin_allowed = (isset($_GET['preview'])) ? false : true; // Login required?
include ('./in.logincheck.php');
include ('./in.head.php');

// Preview?
if ((isset($_SESSION['user_id'])) && (isset($_GET['preview']))) {
  $from_where = "pieces WHERE ";
  echo '<h2><code>(Draft Preview!)</code></h2>';
} else {
  $from_where = "publications WHERE status='live' AND pubstatus='published' AND piece_";
}

if ((isset($_GET['p'])) && (filter_var($_GET['p'], FILTER_VALIDATE_INT))) {

  $p_id = preg_replace("/[^0-9]/"," ", $_GET['p']); // Set $p_id via sanitize non-numbers
  $query = "SELECT title, slug, content, after, tags, links, date_live, date_updated FROM ${from_where}id='$p_id'";

} elseif ((isset($_GET['s'])) && (preg_match('/[a-zA-Z0-9-]{1,90}$/i', $_GET['s']))) {

  $regex_replace = "/[^a-zA-Z0-9-]/"; // Sanitize all non-slug characters
  $result = strtolower(preg_replace($regex_replace,"-", $_GET['s'])); // Lowercase, all non-alnum to hyphen
  $p_slug = substr($result, 0, 90); // Limit to 90 characters
  $query = "SELECT title, piece_id, content, after, tags, links, date_live, date_updated FROM publications WHERE status='live' AND pubstatus='published' AND slug='$p_slug'";

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
  $p_tags_json = "$row[4]";
  $p_links_json = "$row[5]";
  $p_live = "$row[6]";
  $p_update = "$row[7]";

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
  echo '<br><div class="piece-content">'.$p_content.'</div><br>';

  // After
  echo '<br><div class="gray">'.$p_after.'</div><br>';

  // Links
  if ($p_links_json != '[""]') {$links_array = json_decode($p_links_json);}
  // Only if we actually have links
  if (!empty($links_array)) {
    $p_links = ''; // Start the $p_links set
    // Parse $links_array into <a> tag variables
    foreach ($links_array as $line_item) {
      $link_item = '<a href="'.$line_item[0].'" title="'.$line_item[2].'" target="_blank" class="link_item">'.$line_item[1].' <i>// '.$line_item[2].'</i></a>';
      $p_links .= $link_item.'<br class="link_item">';
    }

    // Display Links
    echo '<br><section id="links" class="links">'.$p_links.'</section><br>';

  }

  // Tags
  if ($p_tags_json != '[""]') {$tags_array = json_decode($p_tags_json);}
  // Only if we actually have tags
  if (!empty($tags_array)) {
    $p_tags = ''; // Start the $p_tags set
    // Parse $links_array into <a> tag variables
    foreach ($tags_array as $tag_item) {
      $tag_link = '<a href="tags.php?t='.$tag_item.'" title="View all #'.$tag_item.' posts" target="_blank" class="tag_item"><b>#'.$tag_item.'</b></a>';
      $p_tags .= $tag_link.', ';
    }

    // Display Tags
    echo '<br><section id="tags" class="tags">'.$p_tags.'</section><br>';

  }

  // Edit for logged-in users
  if (isset($user_id)) {
    echo '<p><a href="edit.php?p='.$p_id.'">Edit</a></p>';
  }
  
} else {
  echo '<h1>Nothing here!</h1>';
  exit();
}

// Footer
include ('./in.footer.php');

?>
