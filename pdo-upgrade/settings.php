<?php

// Include our config (with SQL) up near the top of our PHP file
include ('./in.db.php');

// Include our functions
include ('./in.functions.php');

// Include our login cluster
$head_title = "Blog Settings"; // Set a <title> name used next
$edit_page_yn = false; // Include JavaScript for TinyMCE?
include ('./in.logincheck.php');
include ('./in.head.php');

// POSTed form?
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

  // Include our POST checks
  include ('./in.checks.php');

  // No errors, all ready
  if ($no_form_errors == true) {

    // Update the user

    // Prepare our database values for entry
    $blog_public_trim = DB::trimspace($new_blog_public);
    $blog_title_trim = DB::trimspace($new_blog_title);
    $blog_tagline_trim = DB::trimspace($new_blog_tagline);
    $blog_description_trim = DB::trimspace($new_blog_description);
    $blog_keywords_trim = DB::trimspace($new_blog_keywords);
    $blog_summary_words_trim = DB::trimspace($new_blog_summary_words);
    $blog_piece_items_trim = DB::trimspace($new_blog_piece_items);
    $blog_feed_items_trim = DB::trimspace($new_blog_feed_items);
    $blog_crawler_index_trim = DB::trimspace($new_blog_crawler_index);

    // Prepare the query
    $query = $database->prepare("UPDATE blog_settings SET public=:public, title=:title, tagline=:tagline, description=:description, keywords=:keywords, summary_words=:summary_words, piece_items=:piece_items, feed_items=:feed_items, crawler_index=:crawler_index");
    $query->bindParam(':public', $blog_public_trim);
    $query->bindParam(':title', $blog_title_trim);
    $query->bindParam(':tagline', $blog_tagline_trim);
    $query->bindParam(':description', $blog_description_trim);
    $query->bindParam(':keywords', $blog_keywords_trim);
    $query->bindParam(':summary_words', $blog_summary_words_trim);
    $query->bindParam(':piece_items', $blog_piece_items_trim);
    $query->bindParam(':feed_items', $blog_feed_items_trim);
    $query->bindParam(':crawler_index', $blog_crawler_index_trim);


    // Run the query
    $pdo->exec_($query);
    // Test the query
    if ($pdo->ok) {
      // Change
      if ($pdo->change) {
        echo '<p class="green">Updated! Some changes may take a moment.</p>';
      // No change
      } elseif (!$pdo->change) {
        echo '<p class="orange">No change.</p>';
      }
    } else {
      echo '<p class="error">Serious error.</p>';
    }

  } else {
    echo '<p class="error">Errors, try again.</p>';
  }

} else { // Set our values from site defaults if not POST

  $new_blog_public = $blog_public;
  $new_blog_title = $blog_title;
  $new_blog_tagline = $blog_tagline;
  $new_blog_description = $blog_description;
  $new_blog_keywords = $blog_keywords;
  $new_blog_summary_words = $blog_summary_words;
  $new_blog_piece_items = $blog_piece_items;
  $new_blog_feed_items = $blog_feed_items;
  $new_blog_crawler_index = $blog_crawler_index;

} // Finish POST if

// Retrieve the user info from the database
$rows = $pdo->select('users', 'id', $user_id, 'fullname, username, email, favnumber');
// Test the query
if ($pdo->numrows == 1) {
  foreach ($rows as $row) {
  	$fullname = "$row->fullname";
  	$username = "$row->username";
    $email = "$row->email";
    $favnumber = "$row->favnumber";
  }
  // Our actual settings page

  // Settings form
  echo '
  <form action="settings.php" method="post" id="blog_settings">';

  echo 'Title: '.formInput('blog_title', $new_blog_title, $check_err).'<br><br>';
  echo 'Tagline: '.formInput('blog_tagline', $new_blog_tagline, $check_err).' (1-100 security question)<br><br>';
  echo 'Description:<br>'.formInput('blog_description', $new_blog_description, $check_err).'<br><br>';
  echo 'Summary length: '.formInput('blog_summary_words', $new_blog_summary_words, $check_err).'<br><br>';
  echo 'Key words: '.formInput('blog_keywords', $new_blog_keywords, $check_err).'<br><br>';
  echo 'Pieces per page: '.formInput('blog_piece_items', $new_blog_piece_items, $check_err).'<br><br>';
  echo 'Pieces in feed: '.formInput('blog_feed_items', $new_blog_feed_items, $check_err).'<br><br>';
  echo 'Blog visibility:<br>'.formInput('blog_public', $new_blog_public, $check_err).'<br><br>';
  echo 'Search engines: '.formInput('blog_crawler_index', $new_blog_crawler_index, $check_err).'<br><br>';

  echo '
    <input type="submit" value="Save changes">
  </form>
  ';

} else {
  echo '<p class="errors">No settings detected. Something is seriously wrong!</p>';
}

// Footer
include ('./in.footer.php');
