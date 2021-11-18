<?php

// Include our config (with SQL) up near the top of our PHP file
include ('./in.config.php');

// Include our pieces functions
include ('./in.metaeditfunctions.php');

// Include our login cluster
$head_title = "Publication History"; // Set a <title> name used next
$edit_page_yn = false; // Include JavaScript for TinyMCE?
include ('./in.logincheck.php');
include ('./in.head.php');


// Must be logged in
if (!isset($_SESSION['user_id'])) {
  exit (header("Location: blog.php"));
}

if ((isset($_GET['p'])) && (filter_var($_GET['p'], FILTER_VALIDATE_INT))) {
  // Set $piece_id via sanitize non-numbers
  $piece_id = preg_replace("/[^0-9]/"," ", $_GET['p']);

} else {
  exit (header("Location: blog.php"));
}

$query_p = "SELECT id, title, slug, content, after, date_updated FROM publication_history WHERE piece_id='$piece_id' ORDER BY id DESC LIMIT 1";
$call_p = mysqli_query($database, $query_p);
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
  exit ();
}

echo '<pre><a href="piece.php?p='.$piece_id.'" target="_blank">view on blog</a></pre>';

//// Now starts "htmdiff" ////
// echo our diff JS
echo '<script src="htmldiff.min.js"></script>';

// DOM that the JS can recognize
echo '
<div class="outercard">
  <div class="row">
    <div class="col">
      <code><a class="orange" href="edit.php?h='.$o_id.'">revert</a></code>
      <pre><h2>'.$o_update.'<br>(previous)</h2></pre>
      <div class="card" id="outputOld"></div>
    </div>
    <div class="col">
      <code>&nbsp;</code>
      <pre><h2>Changes<br>&nbsp;</h2></pre>
      <div class="card" id="outputDif"></div>
    </div>
    <div class="col">
      <code><a class="orange" href="edit.php?h='.$p_id.'">revert</a></code>
      <pre><h2>'.$p_update.'<br>(latest)</h2></pre>
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
