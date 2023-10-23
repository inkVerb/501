<?php

// Load the feed file (from URL, could be local)
$rss = simplexml_load_file('http://localhost/web/feed.rss');

// Info at top
echo '<h1>'.$rss->channel->title.'</h1>';
echo '<h2>'.$rss->channel->description.'</h2>';

// Fetch the children for the <itunes:> prefix, which are in the <channel> element
$channel_itunes = $rss->channel->children('http://www.itunes.com/dtds/podcast-1.0.dtd');
$itunes_summary = $channel_itunes->summary;
echo "<p>$itunes_summary</p>";
$itunes_author = $channel_itunes->author;
$itunes_owner_name = $channel_itunes->owner->name;
echo "<p>$itunes_owner_name <i>($itunes_author)</i></p>";
$itunes_owner_email = $channel_itunes->owner->email;
echo "<p>$itunes_owner_email</p>";
$itunes_image_url = $channel_itunes->image->attributes()['href'];
echo "<p>$itunes_image_url</p>";

echo "<hr>";

// Iterate each <item> entry
foreach ($rss->channel->item as $item) {

  // Fetch the children for the <itunes:> prefix, which are in the <item> element
  $item_itunes = $item->children('http://www.itunes.com/dtds/podcast-1.0.dtd');
  $content = $item->children('http://purl.org/rss/1.0/modules/content/');
  $atom = $item->children('http://www.w3.org/2005/Atom'); // For future use
  $dc = $item->children('http://purl.org/dc/elements/1.1/');

  echo '<p><b><a href="'.$item->link.'">'.$item->title."</a></b></p>";
  echo "<p><small><i>$item->pubDate</i></small></p>";
  echo "<p><b>$dc->creator</b></p>";
  echo "<p><i>$item->description</i></p>";
  echo "<p>$content->encoded</p>";
  echo "<p>Keywords: <i>$item_itunes->keywords</i></p>";
  echo "<p><code>$item_itunes->duration</code></p>";
  echo '<p><code>'.$item->enclosure['url'].'</code></p>';
  echo (isset($item->guid)) ? "<p><code><b>$item->guid</b></code></p>" : false;

}

?>
