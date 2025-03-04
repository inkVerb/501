<?php

// Include our config (with SQL) up near the top of our PHP file
include ('./in.db.php');

// Include our POST processor
include ('./in.piecefunctions.php');

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

// What type of comparison? Prepare SQL queries accordingly
// Ultimately restoring an Autosave, clicked from this page and will redirect to edit.php
if (isset($_POST['as_json'])) {
  // Validate & parse our JSON from the Autosave
  $as_diff_array = json_decode($_POST['as_json'], true); // We need true because we are not working with OOP in PHP yet
  if (json_last_error() != JSON_ERROR_NONE) {
    exit (header("Location: blog.php"));
  }
  // Get our info, update the dateabse, redirect to the Editor
  $piece_id = filter_var($as_diff_array["piece_id"], FILTER_VALIDATE_INT);
  $piece_id_trim = DB::trimspace($piece_id);
  $p_title = checkPiece('p_title',$as_diff_array["p_title"]);
    // Apply Title to Slug if empty
    if ((empty($as_diff_array["p_slug"])) || (empty($as_diff_array["p_slug"] == ''))) {
      $p_slug = checkPiece('p_slug',$p_title);
    } else {
      $p_slug = checkPiece('p_slug',$as_diff_array["p_slug"]);
    }

    // Check that the slug isn't already used
    $p_slug_test_trim = DB::trimspace($p_slug);
    $query = $database->prepare("SELECT id FROM pieces WHERE slug=:slug AND NOT id=:id");
    $query->bindParam(':slug', $p_slug_test_trim);
    $query->bindParam(':id', $piece_id_trim);
    $pdo->exec_($query);
    if ($pdo->numrows > 0) {
      $add_num = 0;
      $dup = true;
      // If there were no changes
      while ($dup = true) {
        $add_num = $add_num + 1;
        $new_p_slug = $p_slug.'-'.$add_num;
        $new_p_slug_test_trim = DB::trimspace($new_p_slug);

        // Check again
        $query = $database->prepare("SELECT id FROM pieces WHERE slug=:slug AND NOT id=:id");
        $query->bindParam(':slug', $new_p_slug_test_trim);
        $query->bindParam(':id', $piece_id_trim);
        $pdo->exec_($query);
        if ($pdo->numrows == 0) {
          $p_slug = $new_p_slug;
          break;
        }
      }
    }

  $p_content = checkPiece('p_content',$as_diff_array["p_content"]);
  $p_after = checkPiece('p_after',$as_diff_array["p_after"]);
  $p_tags_json = checkPiece('p_tags',$as_diff_array["p_tags"]);
  $p_links_json = checkPiece('p_links',$as_diff_array["p_links"]);
  $p_update = date("Y-m-d H:i:s", substr($as_diff_array["as_time"], 0, 10));

  // Prepare our database values for entry
  $p_title_trim = DB::trimspace($p_title);
  $p_slug_trim = DB::trimspace($p_slug);
  $p_content_trim = DB::trimspace($p_content);
  $p_after_trim = DB::trimspace($p_after);
  $p_tags_sqljson = (json_decode($p_tags_json)) ? $p_tags_json : NULL; // We need JSON as is, no SQL-escape; run an operation, keep value if true, set NULL if false
  $p_links_sqljson = (json_decode($p_links_json)) ? $p_links_json : NULL; // We need JSON as is, no SQL-escape; run an operation, keep value if true, set NULL if false

  // Run the query
  $query = $database->prepare("UPDATE pieces SET title=:title, slug=:slug, content=:content, after=:after, tags=:tags, links=:links, date_updated=NOW() WHERE id=:id");
  $query->bindParam(':title', $p_title_trim);
  $query->bindParam(':slug', $p_slug_trim);
  $query->bindParam(':content', $p_content_trim);
  $query->bindParam(':after', $p_after_trim);
  $query->bindParam(':tags', $p_tags_sqljson);
  $query->bindParam(':links', $p_links_sqljson);
  $query->bindParam(':id', $piece_id_trim);
  $pdo->exec_($query);
  if ($pdo->ok) {
    $_SESSION['as_recovered'] = true;
    header("Location: edit.php?p=$piece_id");
    exit ();
  } else {
    echo 'SQL error recovering piece.';
    exit ();
  }

// Most recent draft
} elseif ((isset($_GET['p'])) && (filter_var($_GET['p'], FILTER_VALIDATE_INT))) {
  // Set $piece_id via sanitize non-numbers
  $piece_id = preg_replace("/[^0-9]/"," ", $_GET['p']);
  $diffing = "latest draft v current publication";
  $query_p = $database->prepare("SELECT title, slug, content, after, tags, links, date_updated FROM pieces WHERE id=:id ORDER BY id DESC LIMIT 1");
  $query_p->bindParam(':id', $piece_id);
  $query_o = $database->prepare("SELECT id, title, slug, content, after, tags, links, date_updated FROM publication_history WHERE piece_id=:piece_id ORDER BY id DESC LIMIT 1");
  $query_o->bindParam(':piece_id', $piece_id);
  $diff_type = 'p';
  // Values
  $rows_p = $pdo->exec_($query_p);
    foreach ($rows_p as $row_p) {
      // Assign the values
      $p_id = "draft_"; // Make sure this can't be a slug, underscore is not allowed for slugs
      $p_title = "$row_p->title";
      $p_slug = "$row_p->slug";
      $p_content = htmlspecialchars_decode("$row_p->content"); // We used htmlspecialchars() to enter the database, now we must reverse it
      $p_after = "$row_p->after";
      $p_tags_json = "$row_p->tags";
      $p_links_json = "$row_p->links";
      $p_update = "$row_p->date_updated";
    }
  $rows_o = $pdo->exec_($query_o);
    foreach ($rows_o as $row_o) {
      // Assign the values
      $o_id = "$row_o->id";
      $o_title = "$row_o->title";
      $o_slug = "$row_o->slug";
      $o_content = htmlspecialchars_decode("$row_o->content"); // We used htmlspecialchars() to enter the database, now we must reverse it
      $o_after = "$row_o->after";
      $o_tags_sqljson = "$row_o->tags";
      $o_links_sqljson = "$row_o->links";
      $o_update = "$row_o->date_updated";
    }
// Recovered autosave
} elseif ((isset($_GET['o'])) && (filter_var($_GET['o'], FILTER_VALIDATE_INT))
      &&  (isset($_GET['a'])) && ($_GET['a'] == 1)
      &&  (isset($_POST['old_as']))) {
  // Validate & parse our JSON from the Autosave
  $as_diff_array = json_decode($_POST['old_as'], true); // We need true because we are not working with OOP in PHP yet
  if (json_last_error() == JSON_ERROR_NONE) {
    $as_diff_json_string = htmlspecialchars($_POST['old_as']); // We use this when recovering
  } else {
    exit (header("Location: blog.php"));
  }

  $piece_id_o = preg_replace("/[^0-9]/"," ", $_GET['o']);
  $diffing = "recovered autosave (unsaved changes from current browser, not available in any history)";
  $query_o = $database->prepare("SELECT title, slug, content, after, tags, links, date_updated FROM pieces WHERE id=:id ORDER BY id DESC LIMIT 1");
  $query_o->bindParam(':id', $piece_id_o);
  $row_p = true; // So we don't fail tests since all other scenarios use two calls
  $diff_type = 'as';
  // Values
  $rows_o = $pdo->exec_($query_o);
    foreach ($rows_o as $row_o) {
      // Assign the values
      $o_title = "$row_o->title";
      $o_slug = "$row_o->slug";
      $o_content = htmlspecialchars_decode("$row_o->content"); // We used htmlspecialchars() to enter the database, now we must reverse it
      $o_after = "$row_o->after";
      $o_tags_sqljson = "$row_o->tags";
      $o_links_sqljson = "$row_o->links";
      $o_update = "$row_o->date_updated";
      $o_id = $piece_id_o;
    }
    // Assign the values
    $piece_id = $as_diff_array["piece_id"]; // We still need this
    $piece_id_p = $as_diff_array["piece_id"];
    $p_title = $as_diff_array["p_title"];
    $p_slug = $as_diff_array["p_slug"];
    $p_content = $as_diff_array["p_content"];
    $p_after = $as_diff_array["p_after"];
    $p_tags_json = $as_diff_array["p_tags"];
    $p_links_json = $as_diff_array["p_links"];
    $p_update = date("Y-m-d H:i:s", substr($as_diff_array["as_time"], 0, 10));

    // Make sure both history IDs match the same piece ID
    if ($piece_id_p == $piece_id_o) {
      $piece_id = $piece_id_p;
    } else {
      exit (header("Location: blog.php"));
    }

    // Form for our recover
    ?>
      <form id="restore_autosave" method="post" action="hist.php?asr=<?php echo $piece_id; ?>">
        <input form="restore_autosave" type="hidden" name="as_json" value="<?php echo $as_diff_json_string; ?>">
      </form>
    <?php


// Deeper History
} elseif ((isset($_GET['c'])) && (filter_var($_GET['c'], FILTER_VALIDATE_INT))
      &&  (isset($_GET['h'])) && (filter_var($_GET['h'], FILTER_VALIDATE_INT))) {
  // Set $piece_id via sanitize non-numbers
  $piece_id_c = preg_replace("/[^0-9]/"," ", $_GET['c']);
  $piece_id_h = preg_replace("/[^0-9]/"," ", $_GET['h']);
  $diffing = "older publications (not current publication)";
  $query_p = $database->prepare("SELECT piece_id, title, slug, content, after, tags, links, date_updated FROM publication_history WHERE id=:id");
  $query_p->bindParam(':id', $piece_id_c);
  $query_o = $database->prepare("SELECT piece_id, title, slug, content, after, tags, links, date_updated FROM publication_history WHERE id=:id");
  $query_o->bindParam(':id', $piece_id_h);
  $diff_type = 'ch';
  // Values
  $rows_p = $pdo->exec_($query_p);
    foreach ($rows_p as $row_p) {
      // Assign the values
      $piece_id = "$row_p->piece_id"; // We still need this
      $piece_id_p = "$row_p->piece_id";
      $p_title = "$row_p->title";
      $p_slug = "$row_p->slug";
      $p_content = htmlspecialchars_decode("$row_p->content"); // We used htmlspecialchars() to enter the database, now we must reverse it
      $p_after = "$row_p->after";
      $p_tags_json = "$row_p->tags";
      $p_links_json = "$row_p->links";
      $p_update = "$row_p->date_updated";
      $p_id = $piece_id_c;
    }
  $rows_o = $pdo->exec_($query_o);
    foreach ($rows_o as $row_o) {
      // Assign the values
      $piece_id_o = "$row_o->piece_id";
      $o_title = "$row_o->title";
      $o_slug = "$row_o->slug";
      $o_content = htmlspecialchars_decode("$row_o->content"); // We used htmlspecialchars() to enter the database, now we must reverse it
      $o_after = "$row_o->after";
      $o_tags_sqljson = "$row_o->tags";
      $o_links_sqljson = "$row_o->links";
      $o_update = "$row_o->date_updated";
      $o_id = $piece_id_h;
    }

    // Make sure both history IDs match the same piece ID
    if ($piece_id_p == $piece_id_o) {
      $piece_id = $piece_id_p;
    } else {
      exit (header("Location: blog.php"));
    }


// Most recent published History
} elseif ((isset($_GET['r'])) && (filter_var($_GET['r'], FILTER_VALIDATE_INT))) {
  // Set $piece_id via sanitize non-numbers
  $piece_id = preg_replace("/[^0-9]/"," ", $_GET['r']);
  $diffing = "latest publication (not current draft)";
  $query_p = $database->prepare("SELECT id, title, slug, content, after, tags, links, date_updated FROM publication_history WHERE piece_id=:piece_id ORDER BY id DESC LIMIT 1");
  $query_p->bindParam(':piece_id', $piece_id);
  $query_o = $database->prepare("SELECT id, title, slug, content, after, tags, links, date_updated FROM publication_history WHERE piece_id=:piece_id ORDER BY id DESC LIMIT 1,1");
  $query_o->bindParam(':piece_id', $piece_id);
  $diff_type = 'r';
  // Values
  $rows_p = $pdo->exec_($query_p);
    foreach ($rows_p as $row_p) {
      // Assign the values
      $p_id = "$row_p->id";
      $p_title = "$row_p->title";
      $p_slug = "$row_p->slug";
      $p_content = htmlspecialchars_decode("$row_p->content"); // We used htmlspecialchars() to enter the database, now we must reverse it
      $p_after = "$row_p->after";
      $p_tags_json = "$row_p->tags";
      $p_links_json = "$row_p->links";
      $p_update = "$row_p->date_updated";
    }
  $rows_o = $pdo->exec_($query_o);
    foreach ($rows_o as $row_o) {
      // Assign the values
      $o_id = "$row_o->id";
      $o_title = "$row_o->title";
      $o_slug = "$row_o->slug";
      $o_content = htmlspecialchars_decode("$row_o->content"); // We used htmlspecialchars() to enter the database, now we must reverse it
      $o_after = "$row_o->after";
      $o_tags_sqljson = "$row_o->tags";
      $o_links_sqljson = "$row_o->links";
      $o_update = "$row_o->date_updated";
    }
} else {
  exit (header("Location: blog.php"));
}

// Check our SQL queries
if ((!$row_p) || (!$row_o)) {
  echo '<pre>Major database error!</pre>';
  exit ();
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
} else {
  $o_tags = '';
}
if ($p_tags_json != '[""]') {$tags_array = json_decode($p_tags_json);}
if (!empty($tags_array)) {
  $p_tags = ''; // Start the $p_tags set
  // Parse $links_array into <a> tag variables
  foreach ($tags_array as $tag_item) {
    $p_tags .= $tag_item.'<br>';
  }
} else {
  $p_tags = '';
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
} else {
  $o_links = '';
}
if ($p_links_json != '[""]') {$links_array = json_decode($p_links_json);}
if (!empty($links_array)) {
  $links = ''; // Start the $links set
  foreach ($links_array as $line_item) {
    $link_item = $line_item[0].' ;; '.$line_item[1].' ;; '.$line_item[2];
    $links .= $link_item.'<br>';
  }
  // Set our final value
  $p_links = $links;
} else {
  $p_links = '';
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
echo '<script src="htmldiff.min.js"></script>';

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
      echo '<code><a class="green" href="edit.php?p='.$piece_id.'">edit current draft</a></code>
      <pre><h3>'.$p_update.'<br>(latest draft)</h3></pre>';
    } elseif ($diff_type == 'ch') {
      echo '<code><a class="orange" href="edit.php?h='.$p_id.'">revert</a></code>
      <pre><h3>'.$p_update.'<br>(later)</h3></pre>';
    } elseif ($diff_type == 'r') {
      echo '<code><a class="orange" href="edit.php?h='.$p_id.'">revert</a></code>
      <pre><h3>'.$p_update.'<br>(current publication)</h3></pre>';
    }
echo '
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
$query = $database->prepare("SELECT id, date_updated FROM publication_history WHERE piece_id=:piece_id ORDER BY id DESC");
$query->bindParam(':piece_id', $piece_id);
$rows = $pdo->exec_($query);
if ($pdo->numrows > 0) { // Only if there is more than one item
  echo '<p><code><b>Revision history:</b></code></p>';

  // echo a link to current draft
  echo '<pre><i><a href="hist.php?p='.$piece_id.'">Diff latest draft</a></i></pre>';
}
foreach ($rows as $row) {
  // Retain previous entries, only render HTML if there was one
  if (isset($prev_piece)) {

    // Retain previous values
    $c_id = $h_id;
    $c_update = $h_update;

    // Assign the values
    $h_id = "$row->id";
    $h_update = "$row->date_updated";

    // echo a link to the past publications
    echo '<pre><i><a href="hist.php?h='.$h_id.'&c='.$c_id.'">'.$c_update.'</a></i></pre>';

  } else {
    // Assign the values
    $h_id = "$row[0]";
    $h_update = "$row[1]";

    // Don't do this else again
    $prev_piece = true;

    // echo a link to the most recent publication
    echo '<pre><i><a href="hist.php?r='.$piece_id.'">'.$h_update.'</a></i></pre>';
  }

}

// Footer
include ('./in.footer.php');
