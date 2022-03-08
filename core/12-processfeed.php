<?php

$rss = simplexml_load_file('http://localhost/web/feed.rss');

echo '<h1>'.$rss->channel->title.'</h1>';
echo '<h2>'.$rss->channel->description.'</h2>';

$itunes = $item->children('http://www.itunes.com/dtds/podcast-1.0.dtd');
$itunes_owner_name = $itunes->owner->name;
echo "<p>$itunes_owner_name</p>";
$itunes_owner_email = $itunes->owner->email;
echo "<p>$itunes_owner_email</p>";
$itunes_image_url = $itunes->image->attributes()['href'];
echo "<p>$itunes_image_url</p>";

foreach ($rss->channel->item as $item) {

  $itunes = $item->children('http://www.itunes.com/dtds/podcast-1.0.dtd');
  $content = $item->children('http://purl.org/rss/1.0/modules/content/');
  $atom = $item->children('http://www.w3.org/2005/Atom'); // For future use
  $dc = $item->children('http://purl.org/dc/elements/1.1/');

  echo '<p><b><a href="'.$item->link.'">'.$item->title."</a></b></p>";
  echo "<p><small><i>$item->pubDate</i></small></p>";
  echo "<p><b>$dc->creator</b></p>";
  echo "<p><i>$item->description</i></p>";
  echo "<p>$content->encoded</p>";
  echo "<p><code>$itunes->duration</code></p>";
  echo '<p><code>'.$item->enclosure['url'].'</code></p>';
  echo (isset($item->guid)) ? "<p><code><b>$item->guid</b></code></p>" : false;

}

?>
