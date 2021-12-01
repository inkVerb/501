<?php
// No <head> yet because we might redirect, which uses header() and might break after the <head> tag

// Include our config (with SQL) up near the top of our PHP file
include ('./in.db.php');

// Include our login cluster
$heading = ""; // Setting no title, our users know where they are
$nologin_allowed = (isset($_GET['preview'])) ? false : $blog_public; // Login required?
include ('./in.logincheck.php');
$seo_inf = true; // Should in.head.php include SEO meta?

// Preview?
if ((isset($_SESSION['user_id'])) && (isset($_GET['preview']))) {
  $from_where = "pieces WHERE ";
} else {
  $from_where = "publications WHERE status='live' AND pubstatus='published' AND piece_";
}

// Piece content
if ((isset($_GET['p'])) && (filter_var($_GET['p'], FILTER_VALIDATE_INT))) {

  $p_id = preg_replace("/[^0-9]/"," ", $_GET['p']); // Set $p_id via sanitize non-numbers
  $query = $database->prepare("SELECT title, slug, content, after, series, tags, links, feat_img, feat_aud, feat_vid, feat_doc, date_live, date_updated FROM ${from_where}id=:id");
  $query->bindParam(':id', $p_id);
} elseif ((isset($_GET['s'])) && (preg_match('/[a-zA-Z0-9-]{1,90}$/i', $_GET['s']))) {

  $regex_replace = "/[^a-zA-Z0-9-]/"; // Sanitize all non-slug characters
  $result = strtolower(preg_replace($regex_replace,"-", $_GET['s'])); // Lowercase, all non-alnum to hyphen
  $p_slug = substr($result, 0, 90); // Limit to 90 characters
  $query = $database->prepare("SELECT title, piece_id, content, after, series, tags, links, feat_img, feat_aud, feat_vid, feat_doc, date_live, date_updated FROM publications WHERE status='live' AND pubstatus='published' AND slug=:slug");
  $query->bindParam(':slug', $p_slug);

} else {
  exit (header("Location: $blog_web_base/"));
}

// Check the database for published pieces
$rows = $pdo->exec_($query);
  foreach ($rows as $row) {
    // Assign the values based on results from if statement just above
    $p_title = "$row->title";
    if (isset($p_id)) {
      $p_slug = "$row->slug";
    } elseif (isset($p_slug)) {
      $p_id = $row->piece_id;
    }
    $p_content = htmlspecialchars_decode("$row->content"); // We used htmlspecialchars() to enter the database, now we must reverse it
    $p_after = "$row->after";
    $p_series_id = "$row->series";
    $p_tags_json = "$row->tags";
    $p_links_json = "$row->links";
    $p_feat_img = $row->feat_img;
    $p_feat_aud = $row->feat_aud;
    $p_feat_vid = $row->feat_vid;
    $p_feat_doc = $row->feat_doc;
    $p_live = "$row->date_live";
    $p_update = "$row->date_updated";

    $rows = $pdo->select('series', 'id', $p_series_id, 'name, slug');
    foreach ($rows as $row) { $p_series = $row->name; $p_series_slug = $row->slug; }
  }

  // Header
  $head_title = $blog_title;
  $piece_title = $p_title;
  include ('./in.head.php');

  // Preview?
  if ((isset($_SESSION['user_id'])) && (isset($_GET['preview']))) {
    echo '<h2><code>(Draft Preview!)</code></h2>';
  }

  // Linked title (we will create piece.php with a RewriteMod in a later lesson)
  echo '<h2><a href="'.$blog_web_base.'/'.$p_slug.'">'.$p_title.'</a></h2>';

  // Date published & series
  echo '<p class="gray"><small><i>'.$p_live.'</i>';
  // If updated is different, show that too
  if ($p_live != $p_update) {
    echo '<br><i>(Updated '.$p_update.')</i>';
  }
  echo ' :: <a href="'.$blog_web_base.'/series/'.$p_series_slug.'">'.$p_series.'</a>'; // series
  echo '</small></p>';

  // Featured Media
  include ('./in.featuredmediadisplay.php');

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
    echo '<p><a href="'.$blog_web_base.'/edit.php?p='.$p_id.'">Edit</a></p>';
  }

// Footer
include ('./in.footer.php');

?>
