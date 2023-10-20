<?php

// Include our config (with SQL) up near the top of our PHP file
include ('./in.db.php');

// Include our login cluster
$nologin_allowed = $blog_public; // Login required?
$seo_inf = true; // Should in.head.php include SEO meta?
include ('./in.logincheck.php');

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

// Series
if ((isset($_GET['s'])) && (preg_match('/[a-zA-Z0-9-]{1,90}$/i', $_GET['s']))) {

  $regex_replace = "/[^a-zA-Z0-9-]/"; // Sanitize all non-slug characters
  $result = strtolower(preg_replace($regex_replace,"-", $_GET['s'])); // Lowercase & sanitize
  $series_slug = substr($result, 0, 100); // Limit to 100 characters as also in database
  $rows = $pdo->select('series', 'slug', $series_slug, 'id, name');
  // No such series found?
  if ($pdo->numrows == 0) { header("Location: $blog_web_base");}
  foreach ($rows as $row) { $series_name = $row->name; $series_id = $row->id; }
}

$series_get = (isset($series_slug)) ? "series/$series_slug/" : "?"; // Set series GET string for pagination
$head_title = (isset($series_name)) ? $blog_title . ' :: ' . $series_name : $blog_title; // Set a <title> name used next
include ('./in.head.php');

// Pagination
// Valid the Pagination
if ((isset($_GET['r'])) && (filter_var($_GET['r'], FILTER_VALIDATE_INT, array('min_range' => 1)))) {
 $paged = preg_replace("/[^0-9]/","", $_GET['r']);
} else {
 $paged = 1;
}
// Set pagination variables:
$pageitems = $blog_piece_items;
$itemskip = $pageitems * ($paged - 1);
// We add this to the end of the $query, after DESC
// LIMIT $itemskip,$pageitems

// Pagination navigation: How many items total?
if ((isset($_GET['s'])) && (isset($series_id))) {
  $query = $database->prepare("SELECT id FROM publications WHERE type='post' AND status='live' AND pubstatus='published' AND date_live<=NOW() AND series=:series ORDER BY date_live DESC");
  $query->bindParam(':series', $series_id);
} else {
  $query = $database->prepare("SELECT id FROM publications WHERE type='post' AND status='live' AND pubstatus='published' AND date_live<=NOW() ORDER BY date_live DESC");
}
$rows = $pdo->exec_($query);
$totalrows = $pdo->numrows;

$totalpages = floor($totalrows / $pageitems);
$remainder = $totalrows % $pageitems;
if ($remainder > 0) {
$totalpages = $totalpages + 1;
}
if ($paged > $totalpages) {
$totalpages = 1;
}
$nextpaged = $paged + 1;
$prevpaged = $paged - 1;

// Check the database for published pieces
if ((isset($_GET['s'])) && (isset($series_id))) {
  $query = $database->prepare("SELECT piece_id, title, slug, content, excerpt, series, tags, feat_img, feat_aud, feat_vid, feat_doc, date_live, date_updated FROM publications WHERE type='post' AND status='live' AND pubstatus='published' AND date_live<=NOW() AND series=:series ORDER BY date_live DESC LIMIT $itemskip,$pageitems");
  $query->bindParam(':series', $series_id);
} else {
  $query = $database->prepare("SELECT piece_id, title, slug, content, excerpt, series, tags, feat_img, feat_aud, feat_vid, feat_doc, date_live, date_updated FROM publications WHERE type='post' AND status='live' AND pubstatus='published' AND date_live<=NOW() ORDER BY date_live DESC LIMIT $itemskip,$pageitems");
}
$rows = $pdo->exec_($query);

if ($pdo->$numrows > 0) {
  // Start our show_div counter
  $show_div_count = 1;
  // We have many entries, this will iterate one post per each
  foreach ($rows as $row) {
    // Assign the values
    $p_id = "$row->piece_id";
    $p_title = "$row->title";
    $p_slug = "$row->slug";
    $p_content = htmlspecialchars_decode("$row->content"); // We used htmlspecialchars() to enter the database, now we must reverse it
    $p_excerpt = "$row->excerpt";
    $p_series_id = "$row->series";
    $p_tags_sqljson = "$row->tags";
    $p_feat_img = $row->feat_img;
    $p_feat_aud = $row->feat_aud;
    $p_feat_vid = $row->feat_vid;
    $p_feat_doc = $row->feat_doc;
    $p_live = "$row->date_live";
    $p_update = "$row->date_updated";

    // Series info
    $rows = $pdo->select('series', 'id', $p_series_id, 'name, slug');
    foreach ($rows as $row) { $p_series = $row->name; $p_series_slug = $row->slug; }

    // Start our hoverable <div>
    echo '<div onmouseover="showTags'.$show_div_count.'()" onmouseout="showTags'.$show_div_count.'()">';

    // Linked title (we will create piece.php with a RewriteMod in a later lesson)
    echo '<h2><a title="Continue reading" href="'.$blog_web_base.'/'.$p_slug.'">'.$p_title.'</a></h2>';

    // Date published
    echo '<p class="gray"><small><i>'.$p_live.'</i>';
    // If updated is different, show that too
    if ($p_live != $p_update) {
      echo '<br><i>(Updated '.$p_update.')</i>';
    }

    // Series
    echo ' :: <a href="'.$blog_web_base.'/series/'.$p_series_slug.'">'.$p_series.'</a>'; // series
    echo '</small></p>';

    // Featured Media
    include ('./in.featuredmediadisplay.php');

    // Content
      $preview_text_limit = 200;
      // Is there an excerpt?
      if (($p_excerpt != '') && ($p_excerpt != NULL)) {
        // Change the content to the excerpt for our remaining purposes
        $p_content = $p_excerpt;
      }
      // Shorten
      $p_content = preview_text($p_content, $preview_text_limit, $p_id);

      // Display
      echo '<br><div class="piece-content">'.$p_content.'</div>';

      // Show "read" link if limit_text() cut anything
      if (isset($limited[$p_id])) {
        echo ' <a title="Continue reading" href="'.$blog_web_base.'/'.$p_slug.'">read <b>&rarr;</b></a>';
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
      echo '<p><a href="'.$blog_web_base.'/edit.php?p='.$p_id.'">edit</a></p>';
    }

    // End our hoverable <div>
    echo '</div>';

    // Separater
    echo '<hr>';

    // Increment our show div counter
    ++$show_div_count;

  } // End foreach
  
} else {
  echo "<p>Nothing yet!</p>";
}

// Pagination nav row
if ($totalpages > 1) {
	echo "
	<div class=\"paginate_nav_container\">
		<div class=\"paginate_nav\">
			<table>
				<tr>
					<td>
						<a class=\"paginate";
						if ($paged == 1) {echo " disabled";}
						echo "\" title=\"Page 1\" href=\"$blog_web_base/{$series_get}r=1\">&laquo;</a>
					</td>
					<td>
						<a class=\"paginate";
            if ($paged == 1) {echo " disabled";}
           echo "\" title=\"Previous\" href=\"$blog_web_base/{$series_get}r=$prevpaged\">&lsaquo;&nbsp;</a>
					</td>
					<td>
						<a class=\"paginate current\" title=\"Next\" href=\"$blog_web_base/{$series_get}r=$paged\">Page $paged ($totalpages)</a>
					</td>
					<td>
						<a class=\"paginate";
            if ($paged == $totalpages) {echo " disabled";}
           echo "\" title=\"Next\" href=\"$blog_web_base/{$series_get}r=$nextpaged\">&nbsp;&rsaquo;</a>
					</td>
					 <td>
						 <a class=\"paginate";
						 if ($paged == $totalpages) {echo " disabled";}
	 					echo "\" title=\"Last Page\" href=\"$blog_web_base/{$series_get}r=$totalpages\">&raquo;</a>
					 </td>
		 		</tr>
			</table>
		</div>
	</div>";
}

// Footer
include ('./in.footer.php');

?>
