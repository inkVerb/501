<?php

// Include our config (with SQL) up near the top of our PHP file
include ('./in.db.php');

// Include our functions
include ('./in.functions.php');

// Include our login cluster
$head_title = "Blog Settings"; // Set a <title> name used next
$edit_page_yn = false; // Include JavaScript for TinyMCE?
$series_editor_yn = true; // Series editor
include ('./in.logincheck.php');
include ('./in.head.php');

// Pro images filenames
$upload_subdir = 'media/pro/';
$file_size_limit = 1000000; // 1MB
$podcast_size_limit = 10000000; // 10MB
$pro_path = $upload_subdir;
$pro_favicon_name = 'pro-favicon.png';
$pro_favicon_path = $pro_path.$pro_favicon_name;
$pro_logo_name = 'pro-logo.png';
$pro_logo_path = $pro_path.$pro_logo_name;
$pro_seo_name = 'pro-seo.jpg';
$pro_seo_path = $pro_path.$pro_seo_name;
$pro_rss_name = 'pro-rss.jpg';
$pro_rss_path = $pro_path.$pro_rss_name;
$pro_podcast_name = 'pro-podcast.jpg';
$pro_podcast_path = $pro_path.$pro_podcast_name;

// POSTed form?
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

  // Include our POST checks
  include ('./in.checks.php');

  // Default Series
  $p_series = (filter_var($_POST['p_series'], FILTER_VALIDATE_INT)) ? filter_var($_POST['p_series'], FILTER_VALIDATE_INT) : false;

  // No errors, all ready
  if ($no_form_errors == true) {

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
    $query = $database->prepare("UPDATE blog_settings SET public=:public, title=:title, tagline=:tagline, description=:description, keywords=:keywords, summary_words=:summary_words, piece_items=:piece_items, feed_items=:feed_items, default_series=:default_series, crawler_index=:crawler_index");
    $query->bindParam(':public', $blog_public_trim);
    $query->bindParam(':title', $blog_title_trim);
    $query->bindParam(':tagline', $blog_tagline_trim);
    $query->bindParam(':description', $blog_description_trim);
    $query->bindParam(':keywords', $blog_keywords_trim);
    $query->bindParam(':summary_words', $blog_summary_words_trim);
    $query->bindParam(':piece_items', $blog_piece_items_trim);
    $query->bindParam(':feed_items', $blog_feed_items_trim);
    $query->bindParam(':default_series', $p_series);
    $query->bindParam(':crawler_index', $blog_crawler_index_trim);

  } else {
    echo '<p class="error">Errors, try again.</p>';
  }

  // Pro image uploads
  // Favicon
  $favicon_info = ($_FILES["pro-favicon"]["tmp_name"]) ? getimagesize($_FILES["pro-favicon"]["tmp_name"]) : false;
  if ($favicon_info) {
    $tmp_file = $_FILES["pro-favicon"]['tmp_name'];
    $image_width = $favicon_info[0];
    $image_height = $favicon_info[1];
    $upload_ext = strtolower(pathinfo(basename($_FILES["pro-favicon"]["name"]),PATHINFO_EXTENSION));
    if ($_FILES['pro-favicon']['size'] <= $file_size_limit) {
      if ($favicon_info["mime"] == "image/png") {
        if (($image_width == $image_height)
        &&  ($image_width >= 128)
        &&  ($image_width <= 512)
        &&  ($image_height >= 128)
        &&  ($image_height <= 512)) {

          if (move_uploaded_file($tmp_file, $pro_favicon_path)) {
            echo '<p class="green">Favicon updated! Cache may need to be refreshed to see changes.</p>';
            $image_uploaded = true;
          } else {
            echo '<p class="red">path:'.$pro_favicon_path.' Favicon upload unknown failure.</p>';
          }

        } else {
          echo '<p class="red">Favicon is wrong size. Must be square and 128-512 pixels.</p>';
        }

      } else {
        echo '<p class="red">Favicon is wrong format. Allowed: JPEG, PNG, GIF</p>';
      }

    } else {
      echo '<p class="red">Favicon file size is too big. Limit is 1MB.</p>';
    }
  } elseif ((isset($_POST['pro-favicon-delete'])) && ($_POST['pro-favicon-delete'] == 'delete') && (isset($_POST['pro-confirm-delete'])) && ($_POST['pro-confirm-delete'] == 'delete')) {
    unlink($pro_favicon_path);
    $deleted_image = true;
  }

  // Logo
  $logo_info = ($_FILES["pro-logo"]["tmp_name"]) ? getimagesize($_FILES["pro-logo"]["tmp_name"]) : false;
  if ($logo_info) {
    $tmp_file = $_FILES["pro-logo"]['tmp_name'];
    $image_width = $logo_info[0];
    $image_height = $logo_info[1];
    $upload_ext = strtolower(pathinfo(basename($_FILES["pro-logo"]["name"]),PATHINFO_EXTENSION));
    if ($_FILES['pro-logo']['size'] <= $file_size_limit) {
      if ($logo_info["mime"] == "image/png") {
        if (($image_width == $image_height)
        &&  ($image_width >= 128)
        &&  ($image_width <= 512)
        &&  ($image_height >= 128)
        &&  ($image_height <= 512)) {

          if (move_uploaded_file($tmp_file, $pro_logo_path)) {
            echo '<p class="green">Logo updated! Cache may need to be refreshed to see changes.</p>';
            $image_uploaded = true;
          } else {
            echo '<p class="red">path:'.$pro_logo_path.' Logo upload unknown failure.</p>';
          }

        } else {
          echo '<p class="red">Logo is wrong size. Must be square and 128-512 pixels.</p>';
        }

      } else {
        echo '<p class="red">Logo is wrong format. Allowed: JPEG, PNG, GIF</p>';
      }

    } else {
      echo '<p class="red">Logo file size is too big. Limit is 1MB.</p>';
    }
  } elseif ((isset($_POST['pro-logo-delete'])) && ($_POST['pro-logo-delete'] == 'delete') && (isset($_POST['pro-confirm-delete'])) && ($_POST['pro-confirm-delete'] == 'delete')) {
    unlink($pro_logo_path);
    $deleted_image = true;
  }

  // SEO image
  $seo_info = ($_FILES["pro-seo"]["tmp_name"]) ? getimagesize($_FILES["pro-seo"]["tmp_name"]) : false;
  if ($seo_info) {
    $tmp_file = $_FILES["pro-seo"]['tmp_name'];
    $image_width = $seo_info[0];
    $image_height = $seo_info[1];
    $upload_ext = strtolower(pathinfo(basename($_FILES["pro-seo"]["name"]),PATHINFO_EXTENSION));
    if ($_FILES['pro-seo']['size'] <= $file_size_limit) {
      if ($seo_info["mime"] == "image/jpeg") {
        if (($image_width != $image_height)
        &&  ($image_width == 1200)
        &&  ($image_height == 630)) {

          if (move_uploaded_file($tmp_file, $pro_seo_path)) {
            echo '<p class="green">SEO image updated! Cache may need to be refreshed to see changes.</p>';
            $image_uploaded = true;
          } else {
            echo '<p class="red">path:'.$pro_seo_path.' SEO image upload unknown failure.</p>';
          }

        } else {
          echo '<p class="red">SEO image is wrong size. Must be exactly 1200 pixels wide and 630 pixels high.</p>';
        }

      } else {
        echo '<p class="red">SEO image is wrong format. Allowed: JPEG, PNG, GIF</p>';
      }

    } else {
      echo '<p class="red">SEO image file size is too big. Limit is 1MB.</p>';
    }
  } elseif ((isset($_POST['pro-seo-delete'])) && ($_POST['pro-seo-delete'] == 'delete') && (isset($_POST['pro-confirm-delete'])) && ($_POST['pro-confirm-delete'] == 'delete')) {
    unlink($pro_seo_path);
    $deleted_image = true;
  }

  // RSS feed
  $rss_info = ($_FILES["pro-rss"]["tmp_name"]) ? getimagesize($_FILES["pro-rss"]["tmp_name"]) : false;
  if ($rss_info) {
    $tmp_file = $_FILES["pro-rss"]['tmp_name'];
    $image_width = $rss_info[0];
    $image_height = $rss_info[1];
    $upload_ext = strtolower(pathinfo(basename($_FILES["pro-rss"]["name"]),PATHINFO_EXTENSION));
    if ($_FILES['pro-rss']['size'] <= $file_size_limit) {
      if ($rss_info["mime"] == "image/jpeg") {
        if (($image_width == $image_height)
        &&  ($image_width == 144)
        &&  ($image_height == 144)) {

          if (move_uploaded_file($tmp_file, $pro_rss_path)) {
            echo '<p class="green">RSS image updated! Cache may need to be refreshed to see changes.</p>';
            $image_uploaded = true;
          } else {
            echo '<p class="red">path:'.$pro_rss_path.' RSS image upload unknown failure.</p>';
          }

        } else {
          echo '<p class="red">Logo is wrong size. Must be square and 144 pixels wide and high.</p>';
        }

      } else {
        echo '<p class="red">RSS image is wrong format. Allowed: JPEG, PNG, GIF</p>';
      }

    } else {
      echo '<p class="red">RSS image file size is too big. Limit is 1MB.</p>';
    }
  } elseif ((isset($_POST['pro-rss-delete'])) && ($_POST['pro-rss-delete'] == 'delete') && (isset($_POST['pro-confirm-delete'])) && ($_POST['pro-confirm-delete'] == 'delete')) {
    unlink($pro_rss_path);
    $deleted_image = true;
  }

  // iTunes Podcast
  $podcast_info = ($_FILES["pro-podcast"]["tmp_name"]) ? getimagesize($_FILES["pro-podcast"]["tmp_name"]) : false;
  if ($podcast_info) {
    $tmp_file = $_FILES["pro-podcast"]['tmp_name'];
    $image_width = $podcast_info[0];
    $image_height = $podcast_info[1];
    $upload_ext = strtolower(pathinfo(basename($_FILES["pro-podcast"]["name"]),PATHINFO_EXTENSION));
    if ($_FILES['pro-podcast']['size'] <= $file_size_limit) {
      if ($podcast_info["mime"] == "image/jpeg") {
        if (($image_width == $image_height)
        &&  ($image_width == 3000)
        &&  ($image_height == 3000)) {

          if (move_uploaded_file($tmp_file, $pro_podcast_path)) {
            echo '<p class="green">Podcast image updated! Cache may need to be refreshed to see changes.</p>';
            $image_uploaded = true;
          } else {
            echo '<p class="red">path:'.$pro_podcast_path.' Podcast image upload unknown failure.</p>';
          }

        } else {
          echo '<p class="red">Podcast image is wrong size. Must be square and 3000 pixels wide and high.</p>';
        }

      } else {
        echo '<p class="red">Podcast image is wrong format. Allowed: JPEG, PNG, GIF</p>';
      }

    } else {
      echo '<p class="red">Podcast image file size is too big. Limit is 1MB.</p>';
    }
  } elseif ((isset($_POST['pro-podcast-delete'])) && ($_POST['pro-podcast-delete'] == 'delete') && (isset($_POST['pro-confirm-delete'])) && ($_POST['pro-confirm-delete'] == 'delete')) {
    unlink($pro_podcast_path);
    $deleted_image = true;
  }
  // End pro image uploads

  // Execute the database query & display any messages
  $pdo->exec_($query);
  // Test the query
  if ($pdo->ok) {
    // Change
    if ($pdo->change) {
      echo '<p class="green">Updated! Some changes may take a moment.</p>';
      // We need to update our default series
      $blog_default_series = $p_series;
    // No change
    } elseif (!$pdo->change) {

      if ($deleted_image == true) {
        echo '<p class="red">Image(s) deleted.</p>';
      } elseif ($image_uploaded != true) {
        echo '<p class="orange">No change.</p>';
      }
    }
  } else {
    echo '<p class="error">Serious error.</p>';
  }

  // Check if deleting an image without checking to "Confirm"
  if (((isset($_POST['pro-favicon-delete'])) && ($_POST['pro-favicon-delete'] == 'delete') && ((!isset($_POST['pro-confirm-delete'])) || ($_POST['pro-confirm-delete'] != 'delete')))
  || ((isset($_POST['pro-logo-delete'])) && ($_POST['pro-logo-delete'] == 'delete') && ((!isset($_POST['pro-confirm-delete'])) || ($_POST['pro-confirm-delete'] != 'delete')))
  || ((isset($_POST['pro-seo-delete'])) && ($_POST['pro-seo-delete'] == 'delete') && ((!isset($_POST['pro-confirm-delete'])) || ($_POST['pro-confirm-delete'] != 'delete')))
  || ((isset($_POST['pro-rss-delete'])) && ($_POST['pro-rss-delete'] == 'delete') && ((!isset($_POST['pro-confirm-delete'])) || ($_POST['pro-confirm-delete'] != 'delete')))
  || ((isset($_POST['pro-logo-delete'])) && ($_POST['pro-logo-delete'] == 'delete') && ((!isset($_POST['pro-confirm-delete'])) || ($_POST['pro-confirm-delete'] != 'delete')))) {
    echo '<p class="red">To delete images, you must check to confirm under the "Save changes" button.</p>';
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

  // Our actual settings page

  echo '<h2>Main</h2>';

  // Settings form
  echo '
  <form action="settings.php" method="post" id="blog_settings" enctype="multipart/form-data">';

  echo 'Title: '.formInput('blog_title', $new_blog_title, $check_err).'<br><br>';
  echo 'Tagline: '.formInput('blog_tagline', $new_blog_tagline, $check_err).'<br><br>';
  echo 'Description:<br>'.formInput('blog_description', $new_blog_description, $check_err).'<br><br>';
  echo 'Summary length: '.formInput('blog_summary_words', $new_blog_summary_words, $check_err).'<br><br>';
  echo 'Key words: '.formInput('blog_keywords', $new_blog_keywords, $check_err).'<br><br>';
  echo 'Pieces per page: '.formInput('blog_piece_items', $new_blog_piece_items, $check_err).'<br><br>';
  echo 'Pieces in feed: '.formInput('blog_feed_items', $new_blog_feed_items, $check_err).'<br><br>';
  echo 'Blog visibility:<br>'.formInput('blog_public', $new_blog_public, $check_err).'<br><br>';
  echo 'Search engines: '.formInput('blog_crawler_index', $new_blog_crawler_index, $check_err).'<br><br>';

  echo '</form>'; // Finish the function-created part of our <form>, more inputs added later

  // Series
  echo '<h2>Series</h2>';
  echo '<p><b>Default Series</b>';

  // Set the values
  $p_series = (isset($p_series)) ? $p_series : $blog_default_series;
  $series_form = 'blog_settings'; // 'edit_piece' or 'blog_settings' or 'new_feed'
  include ('./in.series.php');

  echo '</p>';

  // Edit series button
  include ('./in.editseriesbutton.php');

  // Aggregation link
  echo '<p><a class="blue" href="'.$blog_web_base.'/aggregator.php"><small>Aggregated Feeds</small></a></p>';

  // Show the "Save changes" button prominently
  echo '<h2>Save all changes</h2>';
  echo '
    <input type="submit" value="Save changes" form="blog_settings"><br><br>
    <label for="pro-confirm-delete"><input type="checkbox" form="blog_settings" id="pro-confirm-delete" name="pro-confirm-delete" value="delete"> <i><small>Confirm image delete</small></i></label>
  ';

  // Site pro images
  echo '<h2>Site Images</h2>';

  // Favicon
  echo '<p class="settings-pro-image" id="pro-favicon-upload"><b>Favicon</b> <small>(PNG 128x128 - 512x512)</small> <input type="file" name="pro-favicon" id="pro-favicon" form="blog_settings">';
  if (file_exists($pro_favicon_path)) {
    echo '<br><br><img style="max-width:50px; max-height:50px;" src="'.$pro_favicon_path.'"><label for="pro-favicon-delete"><input type="checkbox" form="blog_settings" id="pro-favicon-delete" name="pro-favicon-delete" value="delete"> <i><small>Delete image</small></i></label>';
  } else {
    echo '<br><br><code class="gray"><i>no image</i></code>';
  }

  echo '</p>';

  // Logo
  echo '<p class="settings-pro-image" id="pro-logo-upload"><b>Site logo</b> <small>(PNG 128x128 - 512x512)</small> <input type="file" name="pro-logo" id="pro-logo" form="blog_settings">';
  if (file_exists($pro_logo_path)) {
    echo '<br><br><img style="max-width:50px; max-height:50px;" src="'.$pro_logo_path.'"><label for="pro-logo-delete"><input type="checkbox" form="blog_settings" id="pro-logo-delete" name="pro-logo-delete" value="delete"> <i><small>Delete image</small></i></label>';
  } else {
    echo '<br><br><code class="gray"><i>no image</i></code>';
  }

  echo '</p>';

  // SEO image
  echo '<p class="settings-pro-image" id="pro-seo-upload"><b>SEO results</b> <small>(JPEG 1200x630)</small> <input type="file" name="pro-seo" id="pro-seo" form="blog_settings">';
  if (file_exists($pro_seo_path)) {
    echo '<br><br><img style="max-width:50px; max-height:50px;" src="'.$pro_seo_path.'"><label for="pro-seo-delete"><input type="checkbox" form="blog_settings" id="pro-seo-delete" name="pro-seo-delete" value="delete"> <i><small>Delete image</small></i></label>';
  } else {
    echo '<br><br><code class="gray"><i>no image</i></code>';
  }

  echo '</p>';

  // RSS feed
  echo '<p class="settings-pro-image" id="pro-rss-upload"><b>RSS</b> <small>(JPEG 144x144)</small> <input type="file" name="pro-rss" id="pro-rss" form="blog_settings">';
  if (file_exists($pro_rss_path)) {
    echo '<br><br><img style="max-width:50px; max-height:50px;" src="'.$pro_rss_path.'"><label for="pro-rss-delete"><input type="checkbox" form="blog_settings" id="pro-rss-delete" name="pro-rss-delete" value="delete"> <i><small>Delete image</small></i></label>';
  } else {
    echo '<br><br><code class="gray"><i>no image</i></code>';
  }

  echo '</p>';

  // iTunes Podcast
  echo '<p class="settings-pro-image" id="pro-podcast-upload"><b>Podcast</b> <small>(JPEG 3000x3000)</small> <input type="file" name="pro-podcast" id="pro-podcast" form="blog_settings">';
  if (file_exists($pro_podcast_path)) {
    echo '<br><br><img style="max-width:50px; max-height:50px;" src="'.$pro_podcast_path.'"><label for="pro-podcast-delete"><input type="checkbox" form="blog_settings" id="pro-podcast-delete" name="pro-podcast-delete" value="delete"> <i><small>Delete image</small></i></label>';
  } else {
    echo '<br><br><code class="gray"><i>no image</i></code>';
  }

  echo '</p>';

// Series edit JavaScript
include ('./in.editseries.php');

// Footer
include ('./in.footer.php');
