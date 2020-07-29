<?php

// Include our config (with SQL) up near the top of our PHP file
include ('./in.config.php');

// Include our login cluster
$head_title = "501 Blog"; // Set a <title> name used next
$nologin_allowed = true; // Login required?
include ('./in.login_check.php');

// Cut wordlength function
$limited = array(); // Create our limit-note array
function preview_text($text, $limit, $lid) {
  global $limited; // Make our limit-note array work in this function
  if (str_word_count($text, 0) > $limit) {
      $words = str_word_count($text, 2);
      $place = array_keys($words);
      $text = substr($text, 0, $place[$limit]).'...';
      $limited[$lid] = $lid; // Leave a note to test if it was cut
  }
  return $text;
}

// Check the database for published pieces
$query = "SELECT piece_id, title, slug, content, tags, date_live, date_updated FROM publications WHERE type='post' AND status='live' AND pubstatus='published'";
$call = mysqli_query($database, $query);
// Start our show_div counter
$show_div_count = 1;
// We have many entries, this will iterate one post per each
while ($row = mysqli_fetch_array($call, MYSQLI_NUM)) {
  // Assign the values
  $p_id = "$row[0]";
  $p_title = "$row[1]";
  $p_slug = "$row[2]";
  $p_content = htmlspecialchars_decode("$row[3]"); // We used htmlspecialchars() to enter the database, now we must reverse it
  $p_tags_sqljson = "$row[4]";
  $p_live = "$row[5]";
  $p_update = "$row[6]";

  // Start our hoverable <div>
  echo '<div onmouseover="showTags'.$show_div_count.'()" onmouseout="showTags'.$show_div_count.'()">';

  // Linked title (we will create piece.php with a RewriteMod in a later lesson)
  echo '<h2><a title="Continue reading" href="piece.php?s='.$p_slug.'">'.$p_title.'</a></h2>';

  // Date published
  echo '<p class="gray"><small><i>'.$p_live.'</i>';
  // If updated is different, show that too
  if ($p_live != $p_update) {
    echo '<br><i>(Updated '.$p_update.')</i>';
  }
  echo '</small></p>';

  // Content
    // Shorten
    $p_content = preview_text($p_content, 5, $p_id);
    // Display
    echo '<br>'.$p_content;

    // Show "read" link if limit_text() cut anything
    if (isset($limited[$p_id])) {
      echo ' <a title="Continue reading" href="piece.php?s='.$p_slug.'">read <b>&rarr;</b></a>';
    }
    echo '<br>';

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

    // Tags
    // We use a bold non-breaking space <b>&nbsp;</b> to hold the line so other text doesn't shift when the tags show up
    echo '<br><section class="tags" id="tags'.$show_div_count.'" style="display: none;">'.$p_tags.'</section><br>';

    // JavaScript with unique function name per post, show/hide tags
    ?>
    <script>
    function showTags<?php echo $show_div_count; ?>() {
      var x = document.getElementById("tags<?php echo $show_div_count; ?>");
      if (x.style.display === "inline") {
        x.style.display = "none";
      } else {
        x.style.display = "inline";
      }
    }
    </script>
    <?php

  }

  // Edit for logged-in users
  if (isset($user_id)) {
    echo '<p><a href="edit.php?p='.$p_id.'">edit</a></p>';
  }

  // End our hoverable <div>
  echo '</div>';

  // Separater
  echo '<hr>';

  // Increment our show div counter
  ++$show_div_count;

}

// Footer
include ('./in.footer.php');

?>
