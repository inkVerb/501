<?php

// Include our config (with SQL) up near the top of our PHP file
include ('./in.config.php');

// Include our pieces functions
include ('./in.metaeditfunctions.php');

// Include our login cluster
$head_title = "Publication History"; // Set a <title> name used next
$edit_page_yn = false; // Include JavaScript for TinyMCE?
include ('./in.login_check.php');


// Must be logged in
if (!isset($_SESSION['user_id'])) {
  exit(header("Location: blog.php"));
}

// What type of comparison? Prepare SQL queries accordingly
if ((isset($_GET['p'])) && (filter_var($_GET['p'], FILTER_VALIDATE_INT))) {
  // Set $piece_id via sanitize non-numbers
  $piece_id = preg_replace("/[^0-9]/"," ", $_GET['p']);
  $diffing = "latest draft v current publication";
  $query_p = "SELECT title, slug, content, after, tags, links, date_updated FROM pieces WHERE id='$piece_id' ORDER BY id DESC LIMIT 1";
  $query_o = "SELECT id, title, slug, content, after, tags, links, date_updated FROM publication_history WHERE piece_id='$piece_id' ORDER BY id DESC LIMIT 1";
  $call_p = mysqli_query($database, $query_p);
  $call_o = mysqli_query($database, $query_o);
  $diff_type = 'p';
  // Values
  $row = mysqli_fetch_array($call_p, MYSQLI_NUM);
    // Assign the values
    $p_id = "draft_"; // Make sure this can't be a slug, underscore is not allowed for slugs
    $p_title = "$row[0]";
    $p_slug = "$row[1]";
    $p_content = htmlspecialchars_decode("$row[2]"); // We used htmlspecialchars() to enter the database, now we must reverse it
    $p_after = "$row[3]";
    $p_tags_sqljson = "$row[4]";
    $p_links_sqljson = "$row[5]";
    $p_update = "$row[6]";
  $row = mysqli_fetch_array($call_o, MYSQLI_NUM);
    // Assign the values
    $o_id = "$row[0]";
    $o_title = "$row[1]";
    $o_slug = "$row[2]";
    $o_content = htmlspecialchars_decode("$row[3]"); // We used htmlspecialchars() to enter the database, now we must reverse it
    $o_after = "$row[4]";
    $o_tags_sqljson = "$row[5]";
    $o_links_sqljson = "$row[6]";
    $o_update = "$row[7]";


} elseif ((isset($_GET['c'])) && (filter_var($_GET['c'], FILTER_VALIDATE_INT))
      &&  (isset($_GET['h'])) && (filter_var($_GET['h'], FILTER_VALIDATE_INT))) {
  // Set $piece_id via sanitize non-numbers
  $piece_id_c = preg_replace("/[^0-9]/"," ", $_GET['c']);
  $piece_id_h = preg_replace("/[^0-9]/"," ", $_GET['h']);
  $diffing = "older publications (not current publication)";
  $query_p = "SELECT piece_id, title, slug, content, after, tags, links, date_updated FROM publication_history WHERE id='$piece_id_c'";
  $query_o = "SELECT piece_id, title, slug, content, after, tags, links, date_updated FROM publication_history WHERE id='$piece_id_h'";
  $call_p = mysqli_query($database, $query_p);
  $call_o = mysqli_query($database, $query_o);
  $diff_type = 'ch';
  // Values
  $row = mysqli_fetch_array($call_p, MYSQLI_NUM);
    // Assign the values
    $piece_id_p = "$row[0]";
    $p_title = "$row[1]";
    $p_slug = "$row[2]";
    $p_content = htmlspecialchars_decode("$row[3]"); // We used htmlspecialchars() to enter the database, now we must reverse it
    $p_tags_sqljson = "$row[4]";
    $p_links_sqljson = "$row[5]";
    $p_update = "$row[6]";
    $p_id = $piece_id_c;
  $row = mysqli_fetch_array($call_o, MYSQLI_NUM);
    // Assign the values
    $piece_id_o = "$row[0]";
    $o_title = "$row[1]";
    $o_slug = "$row[2]";
    $o_content = htmlspecialchars_decode("$row[3]"); // We used htmlspecialchars() to enter the database, now we must reverse it
    $o_after = "$row[4]";
    $o_tags_sqljson = "$row[5]";
    $o_links_sqljson = "$row[6]";
    $o_update = "$row[7]";
    $o_id = $piece_id_h;

    // Make sure both history IDs match the same piece ID
    if ($piece_id_p == $piece_id_o) {
      $piece_id = $piece_id_p;
    } else {
      exit(header("Location: blog.php"));
    }

} elseif ((isset($_GET['r'])) && (filter_var($_GET['r'], FILTER_VALIDATE_INT))) {
  // Set $piece_id via sanitize non-numbers
  $piece_id = preg_replace("/[^0-9]/"," ", $_GET['r']);
  $diffing = "latest publication (not current draft)";
  $query_p = "SELECT id, title, slug, content, after, tags, links, date_updated FROM publication_history WHERE piece_id='$piece_id' ORDER BY id DESC LIMIT 1";
  $query_o = "SELECT id, title, slug, content, after, tags, links, date_updated FROM publication_history WHERE piece_id='$piece_id' ORDER BY id DESC LIMIT 1,1";
  $call_p = mysqli_query($database, $query_p);
  $call_o = mysqli_query($database, $query_o);
  $diff_type = 'r';
  // Values
  $row = mysqli_fetch_array($call_p, MYSQLI_NUM);
    // Assign the values
    $p_id = "$row[0]";
    $p_title = "$row[1]";
    $p_slug = "$row[2]";
    $p_content = htmlspecialchars_decode("$row[3]"); // We used htmlspecialchars() to enter the database, now we must reverse it
    $p_tags_sqljson = "$row[4]";
    $p_links_sqljson = "$row[5]";
    $p_update = "$row[6]";
  $row = mysqli_fetch_array($call_o, MYSQLI_NUM);
    // Assign the values
    $o_id = "$row[0]";
    $o_title = "$row[1]";
    $o_slug = "$row[2]";
    $o_content = htmlspecialchars_decode("$row[3]"); // We used htmlspecialchars() to enter the database, now we must reverse it
    $o_after = "$row[4]";
    $o_tags_sqljson = "$row[5]";
    $o_links_sqljson = "$row[6]";
    $o_update = "$row[7]";

} else {
  exit(header("Location: blog.php"));
}

// Check our SQL queries
if ((!$call_p) || (!$call_o)) {
  echo '<pre>Major database error!</pre>';
  exit();
}

// Process for use in HTML
// Tags
if ($o_tags_sqljson != '[""]') {$tags_array = json_decode($o_tags_sqljson);}
if (!empty($tags_array)) {
  $o_tags = ''; // Start the $p_tags set
  // Parse $links_array into <a> tag variables
  foreach ($tags_array as $tag_item) {
    $o_tags .= $tag_item.'<br>';
  }
}
if ($p_tags_sqljson != '[""]') {$tags_array = json_decode($p_tags_sqljson);}
if (!empty($tags_array)) {
  $p_tags = ''; // Start the $p_tags set
  // Parse $links_array into <a> tag variables
  foreach ($tags_array as $tag_item) {
    $p_tags .= $tag_item.'<br>';
  }
}

// Links
if ($o_links_sqljson != '[""]') {$links_array = json_decode($o_links_sqljson);}
if (!empty($links_array)) {
  $links = ''; // Start the $links set
  foreach ($links_array as $line_item) {
    $link_item = $line_item[0].' ;; '.$line_item[1].' ;; '.$line_item[2];
    $links .= $link_item.'<br>';
  }
  // Set our final value
  $o_links = $links;
}
if ($p_links_sqljson != '[""]') {$links_array = json_decode($p_links_sqljson);}
if (!empty($links_array)) {
  $links = ''; // Start the $links set
  foreach ($links_array as $line_item) {
    $link_item = $line_item[0].' ;; '.$line_item[1].' ;; '.$line_item[2];
    $links .= $link_item.'<br>';
  }
  // Set our final value
  $p_links = $links;
}

  // Create the text to compare via heredoc
  $p_body = <<<EOP
  <code>Title:</code> $p_title<br>
  <code>Slug:</code> $p_slug<br>
  <br>
  <code>Content:</code><br>
  <br>
  $p_content
  <br><br>
  <code>After:</code><br>
  <br>
  $p_after
  <br><br>
  <code>Links:</code><br>
  <br>
  $p_links
  <br>
  <code>Tags:</code><br>
  <br>
  $p_tags
EOP;
// No spaces or comments before or after the ending delimeter of a heredoc!

  // Create the text to compare via heredoc
  $o_body = <<<EOP
  <code>Title:</code> $o_title<br>
  <code>Slug:</code> $o_slug<br>
  <br>
  <code>Content:</code><br>
  <br>
  $o_content
  <br><br>
  <code>After:</code><br>
  <br>
  $o_after
  <br><br>
  <code>Links:</code><br>
  <br>
  $o_links
  <br>
  <code>Tags:</code><br>
  <br>
  $o_tags
EOP;
// No spaces or comments before or after the ending delimeter of a heredoc!

echo "<pre><h2>Diffing: $diffing</h2></pre>";

echo '<pre><a href="piece.php?p='.$piece_id.'" target="_blank">view on blog</a></pre>';

//// Now starts "htmdiff" ////
// echo our diff JS
echo '<script src="htmldiff.js"></script>';

// DOM that the JS can recognize
echo '
<div class="outercard">
  <div class="row">
    <div class="col">
      <code><a class="orange" href="edit.php?h='.$o_id.'">revert</a></code>
      <pre><h3>'.$o_update.'<br>(previous)</h3></pre>
      <div class="card" id="outputOld"></div>
    </div>
    <div class="col">
      <code>&nbsp;</code>
      <pre><h3>Changes<br>&nbsp;</h3></pre>
      <div class="card" id="outputDif"></div>
    </div>
    <div class="col">';
    // Editing a current draft?
    if (($diff_type == 'p') && ($p_id = "draft_")) {
      echo '<code><a class="green" href="edit.php?p='.$piece_id.'">edit current draft</a></code>';
    } elseif (($diff_type == 'ch') || ($diff_type == 'r')) {
      echo '<code><a class="orange" href="edit.php?h='.$o_id.'">revert</a></code>';
    }
echo '
      <pre><h3>'.$p_update.'<br>(latest)</h3></pre>
      <div class="card" id="outputCur"></div>
    </div>
  </div>
</div>
';
?>

<script>
let oldHTML = `<?php echo $o_body; ?>`;
let curHTML = `<?php echo $p_body; ?>`;
let difHTML = htmldiff(oldHTML, curHTML);
document.getElementById("outputOld").innerHTML = oldHTML;
document.getElementById("outputCur").innerHTML = curHTML;
document.getElementById("outputDif").innerHTML = difHTML;
</script>

<?php

//// Now ends "htmdiff" ////

// Revision history click-list
$query = "SELECT id, date_updated FROM publication_history WHERE piece_id='$piece_id' ORDER BY id DESC";
$call = mysqli_query($database, $query);
if (mysqli_num_rows($call) > 1) { // Only if there is more than one item
  echo '<p><code><b>Revision history:</b></code></p>';

  // echo a link to current draft
  echo '<pre><i><a href="hist.php?p='.$piece_id.'">Diff latest draft</a></i></pre>';
}
while ($row = mysqli_fetch_array($call, MYSQLI_NUM)) {
  // Retain previous entries, only render HTML if there was one
  if (isset($prev_piece)) {

    // Retain previous values
    $o_id = $p_id;

    // Assign the values
    $p_id = "$row[0]";
    $p_update = "$row[1]";

    // echo a link to the past publications
    echo '<pre><i><a href="hist.php?h='.$o_id.'&c='.$p_id.'">'.$p_update.'</a></i></pre>';

  } else {
    $prev_piece = true;
    // Assign the values
    $p_id = "$row[0]";
    $p_update = "$row[1]";

    // echo a link to the most recent publication
    echo '<pre><i><a href="hist.php?r='.$piece_id.'">'.$p_update.'</a></i></pre>';
  }

}

// Footer
include ('./in.footer.php');
