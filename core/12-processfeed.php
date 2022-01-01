<?php

$rss = simplexml_load_file('http://localhost/web/feed.rss');

echo '<h1>'.$rss->channel->title.'</h1>';
echo '<h2>'.$rss->channel->description.'</h2>';

foreach ($rss->channel->item as $item) {

  $itunes = $item->children('http://www.itunes.com/dtds/podcast-1.0.dtd');
  $content = $item->children('http://purl.org/rss/1.0/modules/content/');
  $atom = $item->children('http://www.w3.org/2005/Atom'); // For future use
  $dc = $item->children('http://purl.org/dc/elements/1.1/');

  echo '<p><b><a href="'.$item->link.'">'.$item->title."</a></b></p>";
  echo "<p>$item->pubDate</p>";
  echo "<p>$item->description</p>";
  echo "<p>$itunes->author</p>";
  echo "<p>$content->encoded</p>";
  echo "<p>$dc->creator</p>";
  echo (isset($item->enclosure['url'])) ? '<p>'.$item->enclosure['url'].'</p>' : false;

}

?>
