<?php
// This is an XML document, say so first!
header('Content-type: text/xml');

// Include our config (with SQL) up near the top of our PHP file
include ('./in.db.php');

// Parse GET & assign feed-wide values
$regex_match = "/[a-zA-Z0-9-]/";
if ((isset($_GET['s'])) && (preg_match($regex_match, $_GET['s'])) && ($_GET['s'] != 0)) { // Validate
  $regex_replace = "/[^a-zA-Z0-9-]/";
  $series_slug = strtolower(preg_replace($regex_replace,"-", $_GET['s'])); // Lowercase & sanitize
  $series_slug = substr($series_slug, 0, 90); // Limit to 90 characters, as also in database

  // Series Info
  $rows = $pdo->select('series', 'slug', $series_slug, 'id, name, series_lang, series_link, series_author, series_descr, series_summary, series_owner, series_email, series_copy, series_keywords, series_explicit, series_cat1, series_cat2, series_cat3, series_cat4, series_cat5');
  foreach ($rows as $row) {
    $series_id = htmlspecialchars("$row->id");
    $series_name = htmlspecialchars("$row->name");
    $feed_lang = htmlspecialchars("$row->series_lang");
    $feed_link = htmlspecialchars("$row->series_link");
    $feed_author = htmlspecialchars("$row->series_author");
    $feed_descr = htmlspecialchars("$row->series_descr");
    $feed_summary = htmlspecialchars("$row->series_summary");
    $feed_owner = htmlspecialchars("$row->series_owner");
    $feed_email = htmlspecialchars("$row->series_email");
    $feed_copy = htmlspecialchars("$row->series_copy");
    $feed_keywords = htmlspecialchars("$row->series_keywords");
    $feed_explicit = htmlspecialchars("$row->series_explicit");
    $feed_cat1 = htmlspecialchars("$row->series_cat1");
    $feed_cat2 = htmlspecialchars("$row->series_cat2");
    $feed_cat3 = htmlspecialchars("$row->series_cat3");
    $feed_cat4 = htmlspecialchars("$row->series_cat4");
    $feed_cat5 = htmlspecialchars("$row->series_cat5");
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

} elseif ((isset($_GET['s'])) && (preg_match($regex_match, $_GET['s'])) && ($_GET['s'] == 0)) {
  // Blog Info
  //$rows = $pdo->select('blog_settings', 'public', '1', 'blog_lang, blog_link, blog_author, blog_descr, blog_summary, blog_owner, blog_email, blog_copy, blog_keywords, blog_explicit, blog_cat1, blog_cat2, blog_cat3, blog_cat4, blog_cat5');
  $query = $database->prepare("SELECT blog_lang, blog_link, blog_author, blog_descr, blog_summary, blog_owner, blog_email, blog_copy, blog_keywords, blog_explicit, blog_cat1, blog_cat2, blog_cat3, blog_cat4, blog_cat5 FROM blog_settings");
  $rows = $pdo->exec_($query);
  foreach ($rows as $row) {
    $feed_lang = htmlspecialchars("$row->blog_lang");
    $feed_link = htmlspecialchars("$row->blog_link");
    $feed_author = htmlspecialchars("$row->blog_author");
    $feed_descr = htmlspecialchars("$row->blog_descr");
    $feed_summary = htmlspecialchars("$row->blog_summary");
    $feed_owner = htmlspecialchars("$row->blog_owner");
    $feed_email = htmlspecialchars("$row->blog_email");
    $feed_copy = htmlspecialchars("$row->blog_copy");
    $feed_keywords = htmlspecialchars("$row->blog_keywords");
    $feed_explicit = htmlspecialchars("$row->blog_explicit");
    $feed_cat1 = htmlspecialchars("$row->blog_cat1");
    $feed_cat2 = htmlspecialchars("$row->blog_cat2");
    $feed_cat3 = htmlspecialchars("$row->blog_cat3");
    $feed_cat4 = htmlspecialchars("$row->blog_cat4");
    $feed_cat5 = htmlspecialchars("$row->blog_cat5");
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
} else {
  exit();
}

// Verify that our images exist, otherwise leave empty
$pro_rss_path = (file_exists($pro_rss_path)) ? $pro_rss_path : '';
$pro_podcast_path = (file_exists($pro_podcast_path)) ? $pro_podcast_path : '';

// Header of feed
echo <<<EOF
<?xml version="1.0" encoding="UTF-8"?>
<?xml-stylesheet type="text/xsl" href="rss.xsl" ?>
<rss version="2.0"
  xmlns:itunes="http://www.itunes.com/dtds/podcast-1.0.dtd"
  xmlns:content="http://purl.org/rss/1.0/modules/content/"
  xmlns:atom="http://www.w3.org/2005/Atom"
	xmlns:dc="http://purl.org/dc/elements/1.1/"
  >
<channel>
	<title>$feed_title</title>
	<link>$feed_link</link>
	<image>
		<url>$pro_rss_path</url>
		<title>$feed_title</title>
		<link>$feed_link</link>
	</image>
	<description>$feed_descr</description>
	<language>$feed_lang</language>
  <itunes:author>$feed_author</itunes:author>
	<itunes:summary>$feed_summary</itunes:summary>
	<itunes:subtitle>$feed_descr</itunes:subtitle>
  <itunes:owner>
    <itunes:name>$feed_owner</itunes:name>
    <itunes:email>$feed_email</itunes:email>
  </itunes:owner>
	<itunes:keywords>$feed_keywords</itunes:keywords>
	<itunes:image href="$pro_podcast_path"/>
	<itunes:explicit>$feed_explicit</itunes:explicit>
EOF;

// Categories
if (str_contains($feed_cat1, '::')) {
  $cat1 = strtok($feed_cat1, '::');
  $cat2 = preg_replace("/$cat1::/i", "", $feed_cat1);
  echo '<itunes:category text="'.$cat1.'"/>';
  echo '<itunes:category text="'.$cat2.'"/>';
} else {
  echo '<itunes:category text="'.$feed_cat1.'"/>';
}
if (str_contains($feed_cat2, '::')) {
  $cat1 = strtok($feed_cat2, '::');
  $cat2 = preg_replace("/$cat1::/i", "", $feed_cat2);
  echo '<itunes:category text="'.$cat1.'"/>';
  echo '<itunes:category text="'.$cat2.'"/>';
} else {
  echo '<itunes:category text="'.$feed_cat2.'"/>';
}
if (str_contains($feed_cat3, '::')) {
  $cat1 = strtok($feed_cat3, '::');
  $cat2 = preg_replace("/$cat1::/i", "", $feed_cat3);
  echo '<itunes:category text="'.$cat1.'"/>';
  echo '<itunes:category text="'.$cat2.'"/>';
} else {
  echo '<itunes:category text="'.$feed_cat3.'"/>';
}
if (str_contains($feed_cat4, '::')) {
  $cat1 = strtok($feed_cat4, '::');
  $cat2 = preg_replace("/$cat1::/i", "", $feed_cat4);
  echo '<itunes:category text="'.$cat1.'"/>';
  echo '<itunes:category text="'.$cat2.'"/>';
} else {
  echo '<itunes:category text="'.$feed_cat4.'"/>';
}
if (str_contains($feed_cat5, '::')) {
  $cat1 = strtok($feed_cat5, '::');
  $cat2 = preg_replace("/$cat1::/i", "", $feed_cat5);
  echo '<itunes:category text="'.$cat1.'"/>';
  echo '<itunes:category text="'.$cat2.'"/>';
} else {
  echo '<itunes:category text="'.$feed_cat5.'"/>';
}
// Iterate each feed item entry
if ((isset($_GET['s'])) && (isset($series_id))) {
  $query = $database->prepare("SELECT piece_id, title, subtitle, slug, content, excerpt, series, tags, feat_img, feat_aud, feat_vid, feat_doc, date_live, date_updated FROM publications WHERE type='post' AND status='live' AND pubstatus='published' AND date_live<=NOW() AND series=:series ORDER BY date_live DESC LIMIT $blog_feed_items");
  $query->bindParam(':series', $series_id);
  $queryPub = $database->prepare("SELECT date_live FROM publications WHERE type='post' AND status='live' AND pubstatus='published' AND date_live<=NOW() AND series=:series ORDER BY date_live LIMIT 1");
  $queryPub->bindParam(':series', $series_id);
  $queryBuild = $database->prepare("SELECT date_live FROM publications WHERE type='post' AND status='live' AND pubstatus='published' AND date_live<=NOW() AND series=:series ORDER BY date_live DESC LIMIT 1");
  $queryBuild->bindParam(':series', $series_id);
} else {
  $query = $database->prepare("SELECT piece_id, title, subtitle, slug, content, excerpt, series, tags, feat_img, feat_aud, feat_vid, feat_doc, date_live, date_updated FROM publications WHERE type='post' AND status='live' AND pubstatus='published' AND date_live<=NOW() ORDER BY date_live DESC LIMIT $blog_feed_items");
  $queryPub = $database->prepare("SELECT date_live FROM publications WHERE type='post' AND status='live' AND pubstatus='published' AND date_live<=NOW() ORDER BY date_live LIMIT 1");
  $queryBuild = $database->prepare("SELECT date_live FROM publications WHERE type='post' AND status='live' AND pubstatus='published' AND date_live<=NOW() ORDER BY date_live DESC LIMIT 1");
}
$rows = $pdo->exec_($queryPub);
foreach ($rows as $row) { $feed_pub = "$row->date_live"; }
$rows = $pdo->exec_($queryBuild);
foreach ($rows as $row) { $feed_build = "$row->date_live"; }

// Pub & build dates
$feed_timezone = date_default_timezone_get();
$feed_pub = date("D, M j G:i:s Y", strtotime($feed_pub));
$feed_build = date("D, M j G:i:s Y", strtotime($feed_build));
echo <<<EOF
<pubDate>$feed_pub $feed_timezone</pubDate>
<lastBuildDate>$feed_build $feed_timezone</lastBuildDate>
EOF;

$rows = $pdo->exec_($query);
// We have many entries, this will iterate one post per each
foreach ($rows as $row) {
  // Assign the values
  $p_id = "$row->piece_id";
  $p_title = "$row->title";
  $p_subtitle = "$row->subtitle";
  $p_slug = "$row->slug";
  $p_content = strip_tags(htmlspecialchars_decode("$row->content")); // We used htmlspecialchars() to enter the database, now we must reverse it, then remove all tags
  $p_excerpt = "$row->excerpt";
  $p_series_id = "$row->series";
  $p_tags_sqljson = "$row->tags";
  $p_feat_img = "$row->feat_img";
  $p_feat_aud = "$row->feat_aud";
  $p_feat_vid = "$row->feat_vid";
  $p_feat_doc = "$row->feat_doc";
  $p_live = "$row->date_live";
  $p_update = "$row->date_updated";

  // Excerpt?
  if (($p_excerpt != '') && ($p_excerpt != NULL)) {
    // Change the content to the excerpt for our remaining purposes
    $p_content = $p_excerpt;
  }

  // Featured Media
  include ('./in.featuredmedia.php');

  // Series Name & Slug
  $rows = $pdo->select('series', 'id', $p_series_id, 'name, slug');
  foreach ($rows as $row) { $p_series = $row->name; $p_series_slug = $row->slug; }

  // Tags
  if ($p_tags_sqljson != '[""]') {$tags_array = json_decode($p_tags_sqljson);}
  // Only if we actually have tags
  if (!empty($tags_array)) {
    $p_tags = ''; // Start the $p_tags set
    foreach ($tags_array as $tag_item) {
      $p_tags .= $tag_item.', ';
    }
  }

  // Date
  $p_date = date("D, M j G:i:s Y", strtotime($p_live));

  // echo the <item>

echo <<<EOF
<item>
  <title>$p_title</title>
  <link>$blog_web_base/$p_slug</link>
  <guid>$p_id-$p_slug</guid>
  <pubDate>$p_date $feed_timezone</pubDate>
  <author><![CDATA[$feed_author]]></author>
  <dc:creator><![CDATA[$feed_author]]></dc:creator>
  <category><![CDATA[$p_series]]></category>
  <description>$p_subtitle</description>
  <content:encoded><![CDATA[$p_content]]></content:encoded>
  <itunes:subtitle>$p_subtitle</itunes:subtitle>
  <itunes:summary>$p_excerpt</itunes:summary>
  <itunes:author>$feed_author</itunes:author>
  <itunes:keywords>$p_tags</itunes:keywords>
  <itunes:explicit>$feed_explicit</itunes:explicit>
EOF;

    if ($feat_img_id != 0) {

echo <<<EOF
  <enclosure url="$feat_img_url" length="$feat_img_file_size" type="$feat_img_mime" />
EOF;

    }

    if ($feat_aud_id != 0) {

echo <<<EOF
  <enclosure url="$feat_aud_url" length="$feat_aud_file_size" type="$feat_aud_mime" />
EOF;

      echo ($feat_aud_mime == "audio/mpeg") ? "<itunes:duration>$feat_aud_duration</itunes:duration>" : "";

    }

    if ($feat_vid_id != 0) {

echo <<<EOF
  <enclosure url="$feat_vid_url" length="$feat_vid_file_size" type="$feat_vid_mime" />
EOF;

      echo ($feat_vid_mime == "video/mp4") ? "<itunes:duration>$feat_vid_duration</itunes:duration>" : "";

    }

    if ($feat_doc_id != 0) {

echo <<<EOF
  <enclosure url="$feat_doc_url" length="$feat_vid_file_size" type="$feat_doc_mime" />
EOF;

    }

echo <<<EOF
</item>
EOF;

} // Close feed item iteration

// Close feed
echo <<<EOF
</channel>
</rss>
EOF;

?>
