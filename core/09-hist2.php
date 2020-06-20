<?php
// No <head> yet because we might redirect, which uses header() and might break after the <head> tag

// Include our config (with SQL) up near the top of our PHP file
include ('./in.config.php');

// Include our login cluster
$head_title = "Publication History"; // Set a <title> name used next
$edit_page_yn = false; // Include JavaScript for TinyMCE?
include ('./in.login_check.php');


// Must be logged in
if (!isset($_SESSION['user_id'])) {
  exit(header("Location: blog.php"));
}

// What type of comparison? Prepare SQL queries accordingly
if ((isset($_GET['d'])) && (filter_var($_GET['d'], FILTER_VALIDATE_INT))) {
  // Set $piece_id via sanitize non-numbers
  $piece_id = preg_replace("/[^0-9]/"," ", $_GET['d']);
  $diffing = "latest draft v current publication";
  $query_p = "SELECT id, title, slug, content, after, date_updated FROM pieces WHERE id='$piece_id' ORDER BY id DESC LIMIT 1";
  $query_o = "SELECT id, title, slug, content, after, date_updated FROM publication_history WHERE piece_id='$piece_id' ORDER BY id DESC LIMIT 1";
  $diff_type = 'd';

} elseif ((isset($_GET['c'])) && (filter_var($_GET['c'], FILTER_VALIDATE_INT))
      &&  (isset($_GET['h'])) && (filter_var($_GET['h'], FILTER_VALIDATE_INT))) {
  // Set $piece_id via sanitize non-numbers
  $piece_id_c = preg_replace("/[^0-9]/"," ", $_GET['c']);
  $piece_id_h = preg_replace("/[^0-9]/"," ", $_GET['h']);
  $diffing = "older publications (not current publication)";
  $query_p = "SELECT piece_id, title, slug, content, after, date_updated FROM publication_history WHERE id='$piece_id_c'";
  $query_o = "SELECT piece_id, title, slug, content, after, date_updated FROM publication_history WHERE id='$piece_id_h'";
  $diff_type = 'ch';

} elseif ((isset($_GET['p'])) && (filter_var($_GET['p'], FILTER_VALIDATE_INT))) {
  // Set $piece_id via sanitize non-numbers
  $piece_id = preg_replace("/[^0-9]/"," ", $_GET['p']);
  $diffing = "latest publication (not current draft)";
  $query_p = "SELECT id, title, slug, content, after, date_updated FROM publication_history WHERE piece_id='$piece_id' ORDER BY id DESC LIMIT 1";
  $query_o = "SELECT id, title, slug, content, after, date_updated FROM publication_history WHERE piece_id='$piece_id' ORDER BY id DESC LIMIT 1,1";
  $diff_type = 'p';

} else {
  exit(header("Location: blog.php"));
}

$query_p = "SELECT id, title, slug, content, after, date_updated FROM publication_history WHERE piece_id='$piece_id' ORDER BY id DESC LIMIT 1";
$call_p = mysqli_query($database, $query_p);
// We have many entries, this will iterate one post per each
$row = mysqli_fetch_array($call_p, MYSQLI_NUM);
  // Assign the values
  $p_id = "$row[0]";
  $p_title = "$row[1]";
  $p_slug = "$row[2]";
  $p_content = htmlspecialchars_decode("$row[3]"); // We used htmlspecialchars() to enter the database, now we must reverse it
  $p_after = "$row[4]";
  $p_update = "$row[5]";

  // Create the text to compare via heredoc
  $p_body = <<<EOP
  <code>Title:</code> $p_title<br>
  <code>Slug:</code> $p_slug<br>
  <br>
  <code>Content:</code><br>
  $p_content
  <br>
  <code>After:</code><br>
  $p_after
EOP;
// No spaces or comments before or after the ending delimeter of a heredoc!

$query_o = "SELECT id, title, slug, content, after, date_updated FROM publication_history WHERE piece_id='$piece_id' ORDER BY id DESC LIMIT 1,1";
$call_o = mysqli_query($database, $query_o);
// We have many entries, this will iterate one post per each
$row = mysqli_fetch_array($call_o, MYSQLI_NUM);
  // Assign the values
  $o_id = "$row[0]";
  $o_title = "$row[1]";
  $o_slug = "$row[2]";
  $o_content = htmlspecialchars_decode("$row[3]"); // We used htmlspecialchars() to enter the database, now we must reverse it
  $o_after = "$row[4]";
  $o_update = "$row[5]";

  // Create the text to compare via heredoc
  $o_body = <<<EOP
  <code>Title:</code> $o_title<br>
  <code>Slug:</code> $o_slug<br>
  <br>
  <code>Content:</code><br>
  $o_content
  <br>
  <code>After:</code><br>
  $o_after
EOP;
// No spaces or comments before or after the ending delimeter of a heredoc!

if ((!$call_p) || (!$call_o)) {
  echo '<pre>Major database error!</pre>';
  exit();
}

echo "<pre><h2>Diffing: $diffing</h2></pre>";

//// Now starts "htmdiff" ////
// echo our diff JS
echo '<script src="htmldiff.js"></script>';

// DOM that the JS can recognize
echo '
<div class="outercard">
  <div class="row">
    <div class="col">
      <pre><h3>'.$o_update.'<br>(previous)</h3></pre>
      <div class="card" id="outputOld"></div>
    </div>
    <div class="col">
      <pre><h3>Changes<br>&nbsp;</h3></pre>
      <div class="card" id="outputDif"></div>
    </div>
    <div class="col">
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

// Footer
include ('./in.footer.php');
