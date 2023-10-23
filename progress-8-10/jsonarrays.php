<?php

// Create our $tags string
$tags_once = 'one tag, second tag, tertiary, quarternary, ternary, idunnory';
echo '<pre><b>$tags_once:</b> '.$tags_once.'</pre>';

// Convert to JSON
$tags_json = json_encode(explode(', ', $tags_once));
echo '<pre><b>$tags_json:</b> '.$tags_json.'</pre>';

// Change back from JSON to string
$tags_back = implode(', ', json_decode($tags_json, true));
echo '<pre><b>$tags_back:</b> '.$tags_back.'</pre>';

?>
