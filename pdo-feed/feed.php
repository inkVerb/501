<?php
// Include our config (with SQL) up near the top of our PHP file
include ('./in.db.php');

// Parse GET & assign feed-wide values
$regex_match = "/[a-zA-Z0-9-]/";
if ((isset($_GET['s'])) && (preg_match($regex_match, $_GET['s']))) { // Validate
  $regex_replace = "/[^a-zA-Z0-9-]/";
  $series_slug = strtolower(preg_replace($regex_replace,"-", $_GET['s'])); // Lowercase & sanitize
  $series_slug = substr($series_slug, 0, 90); // Limit to 90 characters, as also in database

  // Series Info
  $rows = $pdo->select('series', 'slug', $series_slug, 'id, name, series_lang, series_link, series_author, series_descr, series_summary, series_owner, series_email, series_copy, series_keywords, series_explicit, series_cat1, series_cat2, series_cat3, series_cat4, series_cat5');
  foreach ($rows as $row) {
    $series_id = $row->id;
    $series_name = $row->name;
    $series_lang = $row->series_lang;
    $series_link = $row->series_link;
    $series_author = $row->series_author;
    $series_descr = $row->series_descr;
    $series_summary = $row->series_summary;
    $series_owner = $row->series_owner;
    $series_email = $row->series_email;
    $series_copy = $row->series_copy;
    $series_keywords = $row->series_keywords;
    $series_explicit = $row->series_explicit;
    $series_cat1 = $row->series_cat1;
    $series_cat2 = $row->series_cat2;
    $series_cat3 = $row->series_cat3;
    $series_cat4 = $row->series_cat4;
    $series_cat5 = $row->series_cat5;
  }
  $feed_title = $blog_title.' :: '.$series_name;
  $feed_link = $blog_web_base.'/series/'.$p_series_slug;

  // Images
  $upload_subdir = 'media/pro/';
  $pro_path = $upload_subdir;
  $pro_rss_name = 'series-rss.jpg';
  $pro_podcast_name = 'series-podcast.jpg';
  $pro_rss_path = $pro_path.$series_id.'-'.$pro_rss_name;
  $pro_podcast_path = $pro_path.$series_id.'-'.$pro_podcast_name;

} else {
  // Blog Info
  $query = $database->prepare("SELECT blog_lang, blog_link, blog_author, blog_descr, blog_summary, blog_owner, blog_email, blog_copy, blog_keywords, blog_explicit, blog_cat1, blog_cat2, blog_cat3, blog_cat4, blog_cat5 FROM blog_settings");
  $rows = $pdo->exec_($query);
  foreach ($rows as $row) {
    $blog_lang = $row->blog_lang;
    $blog_link = $row->blog_link;
    $blog_author = $row->blog_author;
    $blog_descr = $row->blog_descr;
    $blog_summary = $row->blog_summary;
    $blog_owner = $row->blog_owner;
    $blog_email = $row->blog_email;
    $blog_copy = $row->blog_copy;
    $blog_keywords = $row->blog_keywords;
    $blog_explicit = $row->blog_explicit;
    $blog_cat1 = $row->blog_cat1;
    $blog_cat2 = $row->blog_cat2;
    $blog_cat3 = $row->blog_cat3;
    $blog_cat4 = $row->blog_cat4;
    $blog_cat5 = $row->blog_cat5;
  }
  $feed_title = $blog_title;
  $feed_link = $blog_web_base;

  // Images
  $upload_subdir = 'media/pro/';
  $pro_path = $upload_subdir;
  $pro_rss_name = 'pro-rss.jpg';
  $pro_rss_path = $pro_path.$pro_rss_name;
  $pro_podcast_name = 'pro-podcast.jpg';
  $pro_podcast_path = $pro_path.$pro_podcast_name;
}

// Verify that our images exist, otherwise leave empty
$pro_rss_path = (file_exists($pro_rss_path)) ? $pro_rss_path : '';
$pro_podcast_path = (file_exists($pro_podcast_path)) ? $pro_podcast_path : '';

// Header of feed

// Iterate each feed item entry
if ((isset($_GET['s'])) && (isset($series_id))) {
  $query = $database->prepare("SELECT piece_id, title, slug, content, series, tags, feat_img, feat_aud, feat_vid, feat_doc, date_live, date_updated FROM publications WHERE type='post' AND status='live' AND pubstatus='published' AND date_live<=NOW() AND series=:series ORDER BY date_live DESC");
  $query->bindParam(':series', $series_id);
} else {
  $query = $database->prepare("SELECT piece_id, title, slug, content, series, tags, feat_img, feat_aud, feat_vid, feat_doc, date_live, date_updated FROM publications WHERE type='post' AND status='live' AND pubstatus='published' AND date_live<=NOW() ORDER BY date_live DESC");
}
$rows = $pdo->exec_($query);
// We have many entries, this will iterate one post per each
foreach ($rows as $row) {
  // Assign the values
  $p_id = "$row->piece_id";
  $p_title = "$row->title";
  $p_slug = "$row->slug";
  $p_content = htmlspecialchars_decode("$row->content"); // We used htmlspecialchars() to enter the database, now we must reverse it
  $p_series_id = "$row->series";
  $p_tags_sqljson = "$row->tags";
  $p_feat_img = $row->feat_img;
  $p_feat_aud = $row->feat_aud;
  $p_feat_vid = $row->feat_vid;
  $p_feat_doc = $row->feat_doc;
  $p_live = "$row->date_live";
  $p_update = "$row->date_updated";

  // Series Name & Slug
  $rows = $pdo->select('series', 'id', $p_series_id, 'name, slug');
  foreach ($rows as $row) { $p_series = $row->name; $p_series_slug = $row->slug; }

  // Tags
  if ($p_tags_sqljson != '[""]') {$tags_array = json_decode($p_tags_sqljson);}
  // Only if we actually have tags
  if (!empty($tags_array)) {
    $p_tags = ''; // Start the $p_tags set
    $tag_count = 1; // Count to limit three
    // Parse $links_array into <a> tag variables
    foreach ($tags_array as $tag_item) {
      if ($tag_count++ > 3) {break;} // Test, then increment at the same time
      $tag_link = '<a href="tags.php?t='.$tag_item.'" title="View all #'.$tag_item.' posts" target="_blank" class="tag_item"><b>#'.$tag_item.'</b></a>';
      $p_tags .= $tag_link.', ';
    }
  }

    // Featured Media
    include ('./in.featuredmediadisplay.php');

    if ($feat_img_id != 0) {

      echo $feat_img_url;
      echo $feat_img_file_size;

    }

    if ($feat_aud_id != 0) {

      echo $feat_aud_url;
      echo $feat_aud_file_size;

    }

    if ($feat_vid_id != 0) {

      echo $feat_vid_url;
      echo $feat_vid_file_size;

    }

    if ($feat_doc_id != 0) {

      echo $feat_vid_url;
      echo $feat_vid_file_size;

    }


} // Close feed item iteration

// Close feed

?>
