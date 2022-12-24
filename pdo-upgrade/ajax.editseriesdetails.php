<?php

// Include our config (with SQL) up near the top of our PHP file
include ('./in.db.php');
include ('./in.logincheck.php');

// Pro images filenames
$upload_subdir = 'media/pro/';
$file_size_limit = 1000000; // 1MB
$podcast_size_limit = 10000000; // 10MB
$pro_path = $upload_subdir;
$pro_rss_name = 'series-rss.jpg';
$pro_podcast_name = 'series-podcast.jpg';

// Function for iTunes categories
function iTunesCat($series_cat) {
  $cat = 'None'; $val = ''; echo '<option value="'.$val.'"'; echo ($series_cat == $val) ? ' selected="selected"' : ''; echo '>'.$cat.'</option>';
  $cat = 'Arts'; $val = 'Arts'; echo '<option value="'.$val.'"'; echo ($series_cat == $val) ? ' selected="selected"' : ''; echo '>'.$cat.'</option>';
  $cat = '- Books'; $val = 'Arts::Books'; echo '<option value="'.$val.'"'; echo ($series_cat == $val) ? ' selected="selected"' : ''; echo '>'.$cat.'</option>';
  $cat = '- Design'; $val = 'Arts::Design'; echo '<option value="'.$val.'"'; echo ($series_cat == $val) ? ' selected="selected"' : ''; echo '>'.$cat.'</option>';
  $cat = '- Fashion &amp; Beauty'; $val = 'Arts::Fashion &amp; Beauty'; echo '<option value="'.$val.'"'; echo ($series_cat == $val) ? ' selected="selected"' : ''; echo '>'.$cat.'</option>';
  $cat = '- Food'; $val = 'Arts::Food'; echo '<option value="'.$val.'"'; echo ($series_cat == $val) ? ' selected="selected"' : ''; echo '>'.$cat.'</option>';
  $cat = '- Performing Arts'; $val = 'Arts::Performing Arts'; echo '<option value="'.$val.'"'; echo ($series_cat == $val) ? ' selected="selected"' : ''; echo '>'.$cat.'</option>';
  $cat = '- Visual Arts'; $val = 'Arts::Visual Arts'; echo '<option value="'.$val.'"'; echo ($series_cat == $val) ? ' selected="selected"' : ''; echo '>'.$cat.'</option>';
  $cat = 'Business'; $val = 'Business'; echo '<option value="'.$val.'"'; echo ($series_cat == $val) ? ' selected="selected"' : ''; echo '>'.$cat.'</option>';
  $cat = '- Careers'; $val = 'Business::Careers'; echo '<option value="'.$val.'"'; echo ($series_cat == $val) ? ' selected="selected"' : ''; echo '>'.$cat.'</option>';
  $cat = '- Entrepreneurship'; $val = 'Business::Entrepreneurship'; echo '<option value="'.$val.'"'; echo ($series_cat == $val) ? ' selected="selected"' : ''; echo '>'.$cat.'</option>';
  $cat = '- Investing'; $val = 'Business::Investing'; echo '<option value="'.$val.'"'; echo ($series_cat == $val) ? ' selected="selected"' : ''; echo '>'.$cat.'</option>';
  $cat = '- Management'; $val = 'Business::Management'; echo '<option value="'.$val.'"'; echo ($series_cat == $val) ? ' selected="selected"' : ''; echo '>'.$cat.'</option>';
  $cat = '- Marketing'; $val = 'Business::Marketing'; echo '<option value="'.$val.'"'; echo ($series_cat == $val) ? ' selected="selected"' : ''; echo '>'.$cat.'</option>';
  $cat = '- Non-Profit'; $val = 'Business::Non-Profit'; echo '<option value="'.$val.'"'; echo ($series_cat == $val) ? ' selected="selected"' : ''; echo '>'.$cat.'</option>';
  $cat = 'Comedy'; $val = 'Comedy'; echo '<option value="'.$val.'"'; echo ($series_cat == $val) ? ' selected="selected"' : ''; echo '>'.$cat.'</option>';
  $cat = '- Comedy Interviews'; $val = 'Comedy::Comedy Interviews'; echo '<option value="'.$val.'"'; echo ($series_cat == $val) ? ' selected="selected"' : ''; echo '>'.$cat.'</option>';
  $cat = '- Improv'; $val = 'Comedy::Improv'; echo '<option value="'.$val.'"'; echo ($series_cat == $val) ? ' selected="selected"' : ''; echo '>'.$cat.'</option>';
  $cat = '- Stand-Up'; $val = 'Comedy::Stand-Up'; echo '<option value="'.$val.'"'; echo ($series_cat == $val) ? ' selected="selected"' : ''; echo '>'.$cat.'</option>';
  $cat = 'Education'; $val = 'Education'; echo '<option value="'.$val.'"'; echo ($series_cat == $val) ? ' selected="selected"' : ''; echo '>'.$cat.'</option>';
  $cat = '- Courses'; $val = 'Education::Courses'; echo '<option value="'.$val.'"'; echo ($series_cat == $val) ? ' selected="selected"' : ''; echo '>'.$cat.'</option>';
  $cat = '- How To'; $val = 'Education::How To'; echo '<option value="'.$val.'"'; echo ($series_cat == $val) ? ' selected="selected"' : ''; echo '>'.$cat.'</option>';
  $cat = '- Language Learning'; $val = 'Education::Language Learning'; echo '<option value="'.$val.'"'; echo ($series_cat == $val) ? ' selected="selected"' : ''; echo '>'.$cat.'</option>';
  $cat = '- Self-Improvement'; $val = 'Education::Self-Improvement'; echo '<option value="'.$val.'"'; echo ($series_cat == $val) ? ' selected="selected"' : ''; echo '>'.$cat.'</option>';
  $cat = 'Fiction'; $val = 'Fiction'; echo '<option value="'.$val.'"'; echo ($series_cat == $val) ? ' selected="selected"' : ''; echo '>'.$cat.'</option>';
  $cat = '- Comedy Fiction'; $val = 'Fiction::Comedy Fiction'; echo '<option value="'.$val.'"'; echo ($series_cat == $val) ? ' selected="selected"' : ''; echo '>'.$cat.'</option>';
  $cat = '- Drama'; $val = 'Fiction::Drama'; echo '<option value="'.$val.'"'; echo ($series_cat == $val) ? ' selected="selected"' : ''; echo '>'.$cat.'</option>';
  $cat = '- Science Fiction'; $val = 'Fiction::Science Fiction'; echo '<option value="'.$val.'"'; echo ($series_cat == $val) ? ' selected="selected"' : ''; echo '>'.$cat.'</option>';
  $cat = 'Government'; $val = 'Government'; echo '<option value="'.$val.'"'; echo ($series_cat == $val) ? ' selected="selected"' : ''; echo '>'.$cat.'</option>';
  $cat = 'History'; $val = 'History'; echo '<option value="'.$val.'"'; echo ($series_cat == $val) ? ' selected="selected"' : ''; echo '>'.$cat.'</option>';
  $cat = 'Health &amp; Fitness'; $val = 'Health &amp; Fitness'; echo '<option value="'.$val.'"'; echo ($series_cat == $val) ? ' selected="selected"' : ''; echo '>'.$cat.'</option>';
  $cat = '- Alternative Health'; $val = 'Health &amp; Fitness::Alternative Health'; echo '<option value="'.$val.'"'; echo ($series_cat == $val) ? ' selected="selected"' : ''; echo '>'.$cat.'</option>';
  $cat = '- Fitness'; $val = 'Health &amp; Fitness::Fitness'; echo '<option value="'.$val.'"'; echo ($series_cat == $val) ? ' selected="selected"' : ''; echo '>'.$cat.'</option>';
  $cat = '- Medicine'; $val = 'Health &amp; Fitness::Medicine'; echo '<option value="'.$val.'"'; echo ($series_cat == $val) ? ' selected="selected"' : ''; echo '>'.$cat.'</option>';
  $cat = '- Mental Health'; $val = 'Health &amp; Fitness::Mental Health'; echo '<option value="'.$val.'"'; echo ($series_cat == $val) ? ' selected="selected"' : ''; echo '>'.$cat.'</option>';
  $cat = '- Nutrition'; $val = 'Health &amp; Fitness::Nutrition'; echo '<option value="'.$val.'"'; echo ($series_cat == $val) ? ' selected="selected"' : ''; echo '>'.$cat.'</option>';
  $cat = '- Sexuality'; $val = 'Health &amp; Fitness::Sexuality'; echo '<option value="'.$val.'"'; echo ($series_cat == $val) ? ' selected="selected"' : ''; echo '>'.$cat.'</option>';
  $cat = 'Kids &amp; Family'; $val = 'Kids &amp; Family'; echo '<option value="'.$val.'"'; echo ($series_cat == $val) ? ' selected="selected"' : ''; echo '>'.$cat.'</option>';
  $cat = '- Education for Kids'; $val = 'Kids &amp; Family::Education for Kids'; echo '<option value="'.$val.'"'; echo ($series_cat == $val) ? ' selected="selected"' : ''; echo '>'.$cat.'</option>';
  $cat = '- Parenting'; $val = 'Kids &amp; Family::Parenting'; echo '<option value="'.$val.'"'; echo ($series_cat == $val) ? ' selected="selected"' : ''; echo '>'.$cat.'</option>';
  $cat = '- Pets &amp; Animals'; $val = 'Kids &amp; Family::Pets &amp; Animals'; echo '<option value="'.$val.'"'; echo ($series_cat == $val) ? ' selected="selected"' : ''; echo '>'.$cat.'</option>';
  $cat = '- Stories for Kids'; $val = 'Kids &amp; Family::Stories for Kids'; echo '<option value="'.$val.'"'; echo ($series_cat == $val) ? ' selected="selected"' : ''; echo '>'.$cat.'</option>';
  $cat = 'Leisure'; $val = 'Leisure'; echo '<option value="'.$val.'"'; echo ($series_cat == $val) ? ' selected="selected"' : ''; echo '>'.$cat.'</option>';
  $cat = '- Animation &amp; Manga'; $val = 'Leisure::Animation &amp; Manga'; echo '<option value="'.$val.'"'; echo ($series_cat == $val) ? ' selected="selected"' : ''; echo '>'.$cat.'</option>';
  $cat = '- Automotive'; $val = 'Leisure::Automotive'; echo '<option value="'.$val.'"'; echo ($series_cat == $val) ? ' selected="selected"' : ''; echo '>'.$cat.'</option>';
  $cat = '- Aviation'; $val = 'Leisure::Aviation'; echo '<option value="'.$val.'"'; echo ($series_cat == $val) ? ' selected="selected"' : ''; echo '>'.$cat.'</option>';
  $cat = '- Crafts'; $val = 'Leisure::Crafts'; echo '<option value="'.$val.'"'; echo ($series_cat == $val) ? ' selected="selected"' : ''; echo '>'.$cat.'</option>';
  $cat = '- Games'; $val = 'Leisure::Games'; echo '<option value="'.$val.'"'; echo ($series_cat == $val) ? ' selected="selected"' : ''; echo '>'.$cat.'</option>';
  $cat = '- Hobbies'; $val = 'Leisure::Hobbies'; echo '<option value="'.$val.'"'; echo ($series_cat == $val) ? ' selected="selected"' : ''; echo '>'.$cat.'</option>';
  $cat = '- Home &amp; Garden'; $val = 'Leisure::Home &amp; Garden'; echo '<option value="'.$val.'"'; echo ($series_cat == $val) ? ' selected="selected"' : ''; echo '>'.$cat.'</option>';
  $cat = '- Video Games'; $val = 'Leisure::Video Games'; echo '<option value="'.$val.'"'; echo ($series_cat == $val) ? ' selected="selected"' : ''; echo '>'.$cat.'</option>';
  $cat = 'Music'; $val = 'Music'; echo '<option value="'.$val.'"'; echo ($series_cat == $val) ? ' selected="selected"' : ''; echo '>'.$cat.'</option>';
  $cat = '- Music Commentary'; $val = 'Music::Music Commentary'; echo '<option value="'.$val.'"'; echo ($series_cat == $val) ? ' selected="selected"' : ''; echo '>'.$cat.'</option>';
  $cat = '- Music History'; $val = 'Music::Music History'; echo '<option value="'.$val.'"'; echo ($series_cat == $val) ? ' selected="selected"' : ''; echo '>'.$cat.'</option>';
  $cat = '- Music Interviews'; $val = 'Music::Music Interviews'; echo '<option value="'.$val.'"'; echo ($series_cat == $val) ? ' selected="selected"' : ''; echo '>'.$cat.'</option>';
  $cat = 'News'; $val = 'News'; echo '<option value="'.$val.'"'; echo ($series_cat == $val) ? ' selected="selected"' : ''; echo '>'.$cat.'</option>';
  $cat = '- Business News'; $val = 'News::Business News'; echo '<option value="'.$val.'"'; echo ($series_cat == $val) ? ' selected="selected"' : ''; echo '>'.$cat.'</option>';
  $cat = '- Daily News'; $val = 'News::Daily News'; echo '<option value="'.$val.'"'; echo ($series_cat == $val) ? ' selected="selected"' : ''; echo '>'.$cat.'</option>';
  $cat = '- Entertainment News'; $val = 'News::Entertainment News'; echo '<option value="'.$val.'"'; echo ($series_cat == $val) ? ' selected="selected"' : ''; echo '>'.$cat.'</option>';
  $cat = '- News Commentary'; $val = 'News::News Commentary'; echo '<option value="'.$val.'"'; echo ($series_cat == $val) ? ' selected="selected"' : ''; echo '>'.$cat.'</option>';
  $cat = '- Politics'; $val = 'News::Politics'; echo '<option value="'.$val.'"'; echo ($series_cat == $val) ? ' selected="selected"' : ''; echo '>'.$cat.'</option>';
  $cat = '- Sports News'; $val = 'News::Sports News'; echo '<option value="'.$val.'"'; echo ($series_cat == $val) ? ' selected="selected"' : ''; echo '>'.$cat.'</option>';
  $cat = '- Tech News'; $val = 'News::Tech News'; echo '<option value="'.$val.'"'; echo ($series_cat == $val) ? ' selected="selected"' : ''; echo '>'.$cat.'</option>';
  $cat = 'Religion &amp; Spirituality'; $val = 'Religion &amp; Spirituality'; echo '<option value="'.$val.'"'; echo ($series_cat == $val) ? ' selected="selected"' : ''; echo '>'.$cat.'</option>';
  $cat = '- Buddhism'; $val = 'Religion &amp; Spirituality::Buddhism'; echo '<option value="'.$val.'"'; echo ($series_cat == $val) ? ' selected="selected"' : ''; echo '>'.$cat.'</option>';
  $cat = '- Christianity'; $val = 'Religion &amp; Spirituality::Christianity'; echo '<option value="'.$val.'"'; echo ($series_cat == $val) ? ' selected="selected"' : ''; echo '>'.$cat.'</option>';
  $cat = '- Hinduism'; $val = 'Religion &amp; Spirituality::Hinduism'; echo '<option value="'.$val.'"'; echo ($series_cat == $val) ? ' selected="selected"' : ''; echo '>'.$cat.'</option>';
  $cat = '- Islam'; $val = 'Religion &amp; Spirituality::Islam'; echo '<option value="'.$val.'"'; echo ($series_cat == $val) ? ' selected="selected"' : ''; echo '>'.$cat.'</option>';
  $cat = '- Judiasm'; $val = 'Religion &amp; Spirituality::Judiasm'; echo '<option value="'.$val.'"'; echo ($series_cat == $val) ? ' selected="selected"' : ''; echo '>'.$cat.'</option>';
  $cat = '- Religion'; $val = 'Religion &amp; Spirituality::Religion'; echo '<option value="'.$val.'"'; echo ($series_cat == $val) ? ' selected="selected"' : ''; echo '>'.$cat.'</option>';
  $cat = '- Spirituality'; $val = 'Religion &amp; Spirituality::Spirituality '; echo '<option value="'.$val.'"'; echo ($series_cat == $val) ? ' selected="selected"' : ''; echo '>'.$cat.'</option>';
  $cat = 'Science'; $val = 'Science'; echo '<option value="'.$val.'"'; echo ($series_cat == $val) ? ' selected="selected"' : ''; echo '>'.$cat.'</option>';
  $cat = '- Astronomy'; $val = 'Science::Astronomy'; echo '<option value="'.$val.'"'; echo ($series_cat == $val) ? ' selected="selected"' : ''; echo '>'.$cat.'</option>';
  $cat = '- Chemistry'; $val = 'Science::Chemistry'; echo '<option value="'.$val.'"'; echo ($series_cat == $val) ? ' selected="selected"' : ''; echo '>'.$cat.'</option>';
  $cat = '- Earth Sciences'; $val = 'Science::Earth Sciences'; echo '<option value="'.$val.'"'; echo ($series_cat == $val) ? ' selected="selected"' : ''; echo '>'.$cat.'</option>';
  $cat = '- Life Sciences'; $val = 'Science::Life Sciences'; echo '<option value="'.$val.'"'; echo ($series_cat == $val) ? ' selected="selected"' : ''; echo '>'.$cat.'</option>';
  $cat = '- Mathematics'; $val = 'Science::Mathematics'; echo '<option value="'.$val.'"'; echo ($series_cat == $val) ? ' selected="selected"' : ''; echo '>'.$cat.'</option>';
  $cat = '- Natural Sciences'; $val = 'Science::Natural Sciences'; echo '<option value="'.$val.'"'; echo ($series_cat == $val) ? ' selected="selected"' : ''; echo '>'.$cat.'</option>';
  $cat = '- Nature'; $val = 'Science::Nature'; echo '<option value="'.$val.'"'; echo ($series_cat == $val) ? ' selected="selected"' : ''; echo '>'.$cat.'</option>';
  $cat = '- Physics'; $val = 'Science::Physics'; echo '<option value="'.$val.'"'; echo ($series_cat == $val) ? ' selected="selected"' : ''; echo '>'.$cat.'</option>';
  $cat = '- Social Sciences'; $val = 'Science::Social Sciences'; echo '<option value="'.$val.'"'; echo ($series_cat == $val) ? ' selected="selected"' : ''; echo '>'.$cat.'</option>';
  $cat = 'Society &amp; Culture'; $val = 'Society &amp; Culture'; echo '<option value="'.$val.'"'; echo ($series_cat == $val) ? ' selected="selected"' : ''; echo '>'.$cat.'</option>';
  $cat = '- Documentary'; $val = 'Society &amp; Culture::Documentary'; echo '<option value="'.$val.'"'; echo ($series_cat == $val) ? ' selected="selected"' : ''; echo '>'.$cat.'</option>';
  $cat = '- Personal Journals'; $val = 'Society &amp; Culture::Personal Journals'; echo '<option value="'.$val.'"'; echo ($series_cat == $val) ? ' selected="selected"' : ''; echo '>'.$cat.'</option>';
  $cat = '- Philosophy'; $val = 'Society &amp; Culture::Philosophy'; echo '<option value="'.$val.'"'; echo ($series_cat == $val) ? ' selected="selected"' : ''; echo '>'.$cat.'</option>';
  $cat = '- Places &amp; Travel'; $val = 'Society &amp; Culture::Places &amp; Travel'; echo '<option value="'.$val.'"'; echo ($series_cat == $val) ? ' selected="selected"' : ''; echo '>'.$cat.'</option>';
  $cat = '- Relationships'; $val = 'Society &amp; Culture::Relationships'; echo '<option value="'.$val.'"'; echo ($series_cat == $val) ? ' selected="selected"' : ''; echo '>'.$cat.'</option>';
  $cat = 'Sports'; $val = 'Sports'; echo '<option value="'.$val.'"'; echo ($series_cat == $val) ? ' selected="selected"' : ''; echo '>'.$cat.'</option>';
  $cat = '- Baseball'; $val = 'Sports::Baseball'; echo '<option value="'.$val.'"'; echo ($series_cat == $val) ? ' selected="selected"' : ''; echo '>'.$cat.'</option>';
  $cat = '- Basketball'; $val = 'Sports::Basketball'; echo '<option value="'.$val.'"'; echo ($series_cat == $val) ? ' selected="selected"' : ''; echo '>'.$cat.'</option>';
  $cat = '- Cricket'; $val = 'Sports::Cricket'; echo '<option value="'.$val.'"'; echo ($series_cat == $val) ? ' selected="selected"' : ''; echo '>'.$cat.'</option>';
  $cat = '- Fantasy Sports'; $val = 'Sports::Fantasy Sports'; echo '<option value="'.$val.'"'; echo ($series_cat == $val) ? ' selected="selected"' : ''; echo '>'.$cat.'</option>';
  $cat = '- Football'; $val = 'Sports::Football'; echo '<option value="'.$val.'"'; echo ($series_cat == $val) ? ' selected="selected"' : ''; echo '>'.$cat.'</option>';
  $cat = '- Golf'; $val = 'Sports::Golf'; echo '<option value="'.$val.'"'; echo ($series_cat == $val) ? ' selected="selected"' : ''; echo '>'.$cat.'</option>';
  $cat = '- Hockey'; $val = 'Sports::Hockey'; echo '<option value="'.$val.'"'; echo ($series_cat == $val) ? ' selected="selected"' : ''; echo '>'.$cat.'</option>';
  $cat = '- Rugby'; $val = 'Sports::Rugby'; echo '<option value="'.$val.'"'; echo ($series_cat == $val) ? ' selected="selected"' : ''; echo '>'.$cat.'</option>';
  $cat = '- Soccer'; $val = 'Sports::Soccer'; echo '<option value="'.$val.'"'; echo ($series_cat == $val) ? ' selected="selected"' : ''; echo '>'.$cat.'</option>';
  $cat = '- Swimming'; $val = 'Sports::Swimming'; echo '<option value="'.$val.'"'; echo ($series_cat == $val) ? ' selected="selected"' : ''; echo '>'.$cat.'</option>';
  $cat = '- Tennis'; $val = 'Sports::Tennis'; echo '<option value="'.$val.'"'; echo ($series_cat == $val) ? ' selected="selected"' : ''; echo '>'.$cat.'</option>';
  $cat = '- Volleyball'; $val = 'Sports::Volleyball'; echo '<option value="'.$val.'"'; echo ($series_cat == $val) ? ' selected="selected"' : ''; echo '>'.$cat.'</option>';
  $cat = '- Wilderness'; $val = 'Sports::Wilderness'; echo '<option value="'.$val.'"'; echo ($series_cat == $val) ? ' selected="selected"' : ''; echo '>'.$cat.'</option>';
  $cat = '- Wrestling'; $val = 'Sports::Wrestling'; echo '<option value="'.$val.'"'; echo ($series_cat == $val) ? ' selected="selected"' : ''; echo '>'.$cat.'</option>';
  $cat = 'Technology'; $val = 'Technology'; echo '<option value="'.$val.'"'; echo ($series_cat == $val) ? ' selected="selected"' : ''; echo '>'.$cat.'</option>';
  $cat = 'True Crime'; $val = 'True Crime'; echo '<option value="'.$val.'"'; echo ($series_cat == $val) ? ' selected="selected"' : ''; echo '>'.$cat.'</option>';
  $cat = 'TV &amp; Film'; $val = 'TV &amp; Film'; echo '<option value="'.$val.'"'; echo ($series_cat == $val) ? ' selected="selected"' : ''; echo '>'.$cat.'</option>';
  $cat = '- After Shows'; $val = 'TV &amp; Film::After Shows'; echo '<option value="'.$val.'"'; echo ($series_cat == $val) ? ' selected="selected"' : ''; echo '>'.$cat.'</option>';
  $cat = '- Film History'; $val = 'TV &amp; Film::Film History'; echo '<option value="'.$val.'"'; echo ($series_cat == $val) ? ' selected="selected"' : ''; echo '>'.$cat.'</option>';
  $cat = '- Film Interviews'; $val = 'TV &amp; Film::Film Interviews'; echo '<option value="'.$val.'"'; echo ($series_cat == $val) ? ' selected="selected"' : ''; echo '>'.$cat.'</option>';
  $cat = '- Film Reviews'; $val = 'TV &amp; Film::Film Reviews'; echo '<option value="'.$val.'"'; echo ($series_cat == $val) ? ' selected="selected"' : ''; echo '>'.$cat.'</option>';
  $cat = '- TV Reviews'; $val = 'TV &amp; Film::TV Reviews'; echo '<option value="'.$val.'"'; echo ($series_cat == $val) ? ' selected="selected"' : ''; echo '>'.$cat.'</option>';
}

// Check & validate for what we need
if ( ($_SERVER['REQUEST_METHOD'] === 'POST') && (!empty($_POST['u_id'])) && (filter_var($_POST['u_id'], FILTER_VALIDATE_INT)) && (isset($_SESSION['user_id'])) && (($_POST['u_id']) == (isset($_SESSION['user_id']))) ) {

  // AJAX token check
  if ( $_POST['ajax_token'] !== $_SESSION['ajax_token'] ) {
    exit();
  }

  // Set our $u_id
  $u_id = preg_replace("/[^0-9]/"," ", $_POST['u_id']);

  // Create our AJAX response array
  $ajax_response = array();

  // Update Series
  if ((!empty($_POST['s_id'])) && (filter_var($_POST['s_id'], FILTER_VALIDATE_INT))
  && (isset($_POST['series_name']))
  && (isset($_POST['series_slug']))
  && (isset($_POST['series_lang']))
  && (isset($_POST['series_link']))
  && (isset($_POST['series_author']))
  && (isset($_POST['series_descr']))
  && (isset($_POST['series_summary']))
  && (isset($_POST['series_owner']))
  && (isset($_POST['series_email']))
  && (isset($_POST['series_copy']))
  && (isset($_POST['series_keywords']))
  && (isset($_POST['series_explicit']))
  && (isset($_POST['series_cat1']))
  && (isset($_POST['series_cat2']))
  && (isset($_POST['series_cat3']))
  && (isset($_POST['series_cat4']))
  && (isset($_POST['series_cat5'])) ) {

    // Assign the media ID and sanitize as the same time
    $s_id = preg_replace("/[^0-9]/"," ", $_POST['s_id']);

    // Set our reponse ID
    $ajax_response['s_id'] = $s_id;
    $ajax_nameslug_warning = '';

    // Pro images
    // RSS feed
    $rss_info = ($_FILES["pro-rss"]["tmp_name"]) ? getimagesize($_FILES["pro-rss"]["tmp_name"]) : false;
    $pro_rss_path = $pro_path.$s_id.'-'.$pro_rss_name;
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
              $upload_img_success = true;
            } else {
              $ajax_nameslug_warning .= " <span class='red'>Image unknown failure.</span>";
            }

          } else {
            $ajax_nameslug_warning .= " <span class='red'>Wrong image size.</span>";
          }

        } else {
          $ajax_nameslug_warning .= " <span class='red'>Wrong image format.</span>";
        }

      } else {
        $ajax_nameslug_warning .= " <span class='red'>Image size limit is 1MB.</span>";
      }
    }

    // iTunes Podcast
    $podcast_info = ($_FILES["pro-podcast"]["tmp_name"]) ? getimagesize($_FILES["pro-podcast"]["tmp_name"]) : false;
    $pro_podcast_path = $pro_path.$s_id.'-'.$pro_podcast_name;
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
              $upload_img_success = true;
            } else {
              $ajax_nameslug_warning .= " <span class='red'>Image unknown failure.</span>";
            }

          } else {
            $ajax_nameslug_warning .= " <span class='red'>Wrong image size.</span>";
          }

        } else {
          $ajax_nameslug_warning .= " <span class='red'>Wrong image format.</span>";
        }

      } else {
        $ajax_nameslug_warning .= " <span class='red'>Image size limit is 1MB.</span>";
      }
    }
    // End pro image uploads

    // Non-upload
    // Series slug
    if (preg_replace('/\s+/', '', $_POST['series_slug']) != '') {
      $regex_replace = "/[^a-zA-Z0-9-]/";
      $result = strip_tags($_POST['series_slug']); // Remove any HTML tags
      $result = strtolower(preg_replace($regex_replace,"-", $result)); // Lowercase, all non-alnum to hyphen
      $result = substr($result, 0, 90); // Limit to 90 characters
      $clean_slug = $result;
      $clean_slug = DB::trimspace($clean_slug);
      $query = $database->prepare("SELECT id FROM series WHERE slug=:slug AND NOT id=:id");
      $query->bindParam(':slug', $clean_slug);
      $query->bindParam(':id', $s_id);
      $rows = $pdo->exec_($query);
      if ($pdo->numrows > 0) {
        $ajax_nameslug_warning .= " <span class='red'>Slug '$clean_slug' already in use!</span>";
        $slug_nochange = true;
      }
    } else {
      $ajax_nameslug_warning .= " <span class='red'>Slug can't be empty!</span>";
      $slug_nochange = true;
    }
      // Won't change slug, keep old slug
      if ($slug_nochange) {
        $query = $database->prepare("SELECT slug FROM series WHERE id=:id");
        $query->bindParam(':id', $s_id);
        $rows = $pdo->exec_($query);
        if ($pdo->numrows > 0) {
          foreach ($rows as $row) {
            $clean_slug = $row->slug;
          }
        }
      }
    // Series name
    if (preg_replace('/\s+/', '', $_POST['series_name']) != '') {
      $result = strip_tags($_POST['series_name']); // Remove any HTML tags
      $result = preg_replace('/([0-9]$)+-+([0-9])/','$1–$2',$result); // to en-dash
      $result = str_replace(' -- ',' – ',$result); // to en-dash
      $result = str_replace('---','—',$result); // to em-dash
      $result = str_replace('--','—',$result); // to em-dash
      $result = substr($result, 0, 90); // Limit to 90 characters
      $series_name = $result;
      $series_name = DB::trimspace($series_name);

      $query = $database->prepare("SELECT id FROM series WHERE name=:name AND NOT id=:id");
      $query->bindParam(':name', $series_name);
      $query->bindParam(':id', $s_id);
      $rows = $pdo->exec_($query);
      if ($pdo->numrows > 0) {
        $ajax_nameslug_warning .= " <span class='red'>Series name '$series_name' already in use!</span>";
        $series_nochange = true;
      }

    } else {
      $ajax_nameslug_warning .= " <span class='red'>Series name can't be empty!</span>";
      $series_nochange = true;
    }
      // Won't change series name, keep old series name
      if ($series_nochange) {
        $query = $database->prepare("SELECT name FROM series WHERE id=:id");
        $query->bindParam(':id', $s_id);
        $rows = $pdo->exec_($query);
        if ($pdo->numrows > 0) {
          foreach ($rows as $row) {
            $series_name = $row->name;
          }
        }
      }
    // Series language
    if (preg_replace('/\s+/', '', $_POST['series_lang']) != '') {
      $regex_replace = "/[^a-zA-Z0-9-]/";
      $result = strip_tags($_POST['series_lang']); // Remove any HTML tags
      $result = strtolower(preg_replace($regex_replace,"-", $result)); // Lowercase, remove non-accepted characters
      $result = substr($result, 0, 90); // Limit to 90 characters
      $series_lang = $result;
      $series_lang = DB::trimspace($series_lang);

    } else {
      $ajax_empty_field_warning = true;
    }
    // Series link
    if (filter_var($_POST['series_link'], FILTER_VALIDATE_URL)) {
      $result = filter_var($_POST['series_link'], FILTER_VALIDATE_URL); // Real URL
      $series_link = $result;
      $series_link = DB::trimspace($series_link);

    } else {
      $ajax_empty_field_warning = true;
    }
    // Series author
    if (preg_replace('/\s+/', '', $_POST['series_author']) != '') {
      $result = strip_tags($_POST['series_author']); // Remove any HTML tags
      $result = substr($result, 0, 255); // Limit to 255 characters
      $series_author = $result;
      $series_author = DB::trimspace($series_author);

    } else {
      $ajax_empty_field_warning = true;
    }
    // Series description
    if (preg_replace('/\s+/', '', $_POST['series_descr']) != '') {
      $result = strip_tags($_POST['series_descr']); // Remove any HTML tags
      $result = substr($result, 0, 255); // Limit to 255 characters
      $series_descr = $result;
      $series_descr = DB::trimspace($series_descr);

    } else {
      $ajax_empty_field_warning = true;
    }
    // Series summary
    if (preg_replace('/\s+/', '', $_POST['series_summary']) != '') {
      $result = strip_tags($_POST['series_summary']); // Remove any HTML tags
      $result = substr($result, 0, 255); // Limit to 255 characters
      $series_summary = $result;
      $series_summary = DB::trimspace($series_summary);

    } else {
      $ajax_empty_field_warning = true;
    }
    // Series owner
    if (preg_replace('/\s+/', '', $_POST['series_owner']) != '') {
      $result = strip_tags($_POST['series_owner']); // Remove any HTML tags
      $result = substr($result, 0, 255); // Limit to 255 characters
      $series_owner = $result;
      $series_owner = DB::trimspace($series_owner);

    } else {
      $ajax_empty_field_warning = true;
    }
    // Series email
    if (filter_var($_POST['series_email'], FILTER_VALIDATE_EMAIL)) {
      $result = filter_var($_POST['series_email'], FILTER_VALIDATE_EMAIL); // Real email
      $result = substr($result, 0, 255); // Limit to 255 characters
      $series_email = $result;
      $series_email = DB::trimspace($series_email);

    } else {
      $ajax_empty_field_warning = true;
    }
    // Series keywords
    if (preg_replace('/\s+/', '', $_POST['series_keywords']) != '') {
      $regex_replace = "/[^a-zA-Z0-9-, ]/";
      $result = strip_tags($_POST['series_keywords']); // Remove any HTML tags
      $result = preg_replace($regex_replace,"", $result); // Remove non-accepted characters
      $result = substr($result, 0, 255); // Limit to 255 characters
      $series_keywords = $result;
      $series_keywords = DB::trimspace($series_keywords);

    } else {
      $ajax_empty_field_warning = true;
    }
    // Series explicit
    if ((preg_replace('/\s+/', '', $_POST['series_explicit']) != '')
    && ($_POST['series_explicit'] == 'true')
    || ($_POST['series_explicit'] == 'false')) {
      $regex_replace = "/[^truefals]/";
      $result = strip_tags($_POST['series_explicit']); // Remove any HTML tags
      $result = preg_replace($regex_replace,"", $result); // Remove non-accepted characters
      $result = substr($result, 0, 5); // Limit to 5 characters
      $series_explicit = $result;
      $series_explicit = DB::trimspace($series_explicit);

    } else {
      $ajax_empty_field_warning = true;
    }
    // Series copyright
    if (preg_replace('/\s+/', '', $_POST['series_copy']) != '') {
      $result = strip_tags($_POST['series_copy']); // Remove any HTML tags
      $result = substr($result, 0, 255); // Limit to 255 characters
      $series_copy = $result;
      $series_copy = DB::trimspace($series_copy);

    } else {
      $ajax_empty_field_warning = true;
    }
    // Series category 1
    if (isset($_POST['series_cat1'])) {
      $regex_replace = "/[^a-zA-Z-&;: ]/";
      $result = strip_tags($_POST['series_cat1']); // Remove any HTML tags
      $result = preg_replace($regex_replace,"-", $result); // Remove non-accepted characters
      $result = substr($result, 0, 255); // Limit to 255 characters
      $series_cat1 = $result;
      $series_cat1 = DB::trimspace($series_cat1);

    } else {
      $ajax_empty_field_warning = true;
    }
    // Series category 2
    if (isset($_POST['series_cat2'])) {
      $regex_replace = "/[^a-zA-Z-&;: ]/";
      $result = strip_tags($_POST['series_cat2']); // Remove any HTML tags
      $result = preg_replace($regex_replace,"-", $result); // Remove non-accepted characters
      $result = substr($result, 0, 255); // Limit to 255 characters
      $series_cat2 = $result;
      $series_cat2 = DB::trimspace($series_cat2);

    } else {
      $ajax_empty_field_warning = true;
    }
    // Series category 3
    if (isset($_POST['series_cat3'])) {
      $regex_replace = "/[^a-zA-Z-&;: ]/";
      $result = strip_tags($_POST['series_cat3']); // Remove any HTML tags
      $result = preg_replace($regex_replace,"-", $result); // Remove non-accepted characters
      $result = substr($result, 0, 255); // Limit to 255 characters
      $series_cat3 = $result;
      $series_cat3 = DB::trimspace($series_cat3);

    } else {
      $ajax_empty_field_warning = true;
    }
    // Series category 4
    if (isset($_POST['series_cat4'])) {
      $regex_replace = "/[^a-zA-Z-&;: ]/";
      $result = strip_tags($_POST['series_cat4']); // Remove any HTML tags
      $result = preg_replace($regex_replace,"-", $result); // Remove non-accepted characters
      $result = substr($result, 0, 255); // Limit to 255 characters
      $series_cat4 = $result;
      $series_cat4 = DB::trimspace($series_cat4);

    } else {
      $ajax_empty_field_warning = true;
    }
    // Series category 5
    if (isset($_POST['series_cat5'])) {
      $regex_replace = "/[^a-zA-Z-&;: ]/";
      $result = strip_tags($_POST['series_cat5']); // Remove any HTML tags
      $result = preg_replace($regex_replace,"-", $result); // Remove non-accepted characters
      $result = substr($result, 0, 255); // Limit to 255 characters
      $series_cat5 = $result;
      $series_cat5 = DB::trimspace($series_cat5);

    } else {
      $ajax_empty_field_warning = true;
    }
    $ajax_empty_field_warning = (isset($ajax_empty_field_warning)) ? " <span class='red'>Empty podcast field(s)!</span>" : '';

    // SQL
    $query = $database->prepare("UPDATE series SET
      name=:name,
      slug=:slug,
      series_lang=:series_lang,
      series_link=:series_link,
      series_author=:series_author,
      series_descr=:series_descr,
      series_summary=:series_summary,
      series_owner=:series_owner,
      series_email=:series_email,
      series_copy=:series_copy,
      series_keywords=:series_keywords,
      series_explicit=:series_explicit,
      series_cat1=:series_cat1,
      series_cat2=:series_cat2,
      series_cat3=:series_cat3,
      series_cat4=:series_cat4,
      series_cat5=:series_cat5
      WHERE id=:id");
    $query->bindParam(':name', $series_name);
    $query->bindParam(':slug', $clean_slug);
    $query->bindParam(':series_lang', $series_lang);
    $query->bindParam(':series_link', $series_link);
    $query->bindParam(':series_author', $series_author);
    $query->bindParam(':series_descr', $series_descr);
    $query->bindParam(':series_summary', $series_summary);
    $query->bindParam(':series_owner', $series_owner);
    $query->bindParam(':series_email', $series_email);
    $query->bindParam(':series_copy', $series_copy);
    $query->bindParam(':series_keywords', $series_keywords);
    $query->bindParam(':series_explicit', $series_explicit);
    $query->bindParam(':series_cat1', $series_cat1);
    $query->bindParam(':series_cat2', $series_cat2);
    $query->bindParam(':series_cat3', $series_cat3);
    $query->bindParam(':series_cat4', $series_cat4);
    $query->bindParam(':series_cat5', $series_cat5);
    $query->bindParam(':id', $s_id);
    $pdo->exec_($query);
    if ($pdo->change) { // Successful change
      $ajax_response['message'] .= "<small>".$series_name.':'.$ajax_nameslug_warning.$ajax_empty_field_warning." <span class='green'>Podcast details saved.</span></small>";
    } else { // No changes
      // Images?
      if ($upload_img_success == true) {
        $ajax_response['message'] .= "<small>".$series_name.':'.$ajax_nameslug_warning.$ajax_empty_field_warning." <span class='green'>Image uploaded. Podcast details saved.</span></small>";
      // Truly no changes at all
      } else {
        $ajax_response['message'] .= "<small>".$series_name.':'.$ajax_nameslug_warning.$ajax_empty_field_warning." <span class='orange'>No changes.</span></small>";

      } // Delete check
    } // Changes check

    // We're done here
    $json_response = json_encode($ajax_response, JSON_FORCE_OBJECT);
    echo $json_response;

  // Just getting the <form> from seriesEditor AJAX loader
} elseif ((isset($_POST['s_id'])) && (filter_var($_POST['s_id'], FILTER_VALIDATE_INT))) {

    $s_id = preg_replace("/[^0-9]/"," ", $_POST['s_id']);

    echo '<div id="series-details-container">';
    echo '<h2 class="editor-title">Series Podcast Details</h2>';

    // Fetch the information for this series
    $query = $database->prepare("SELECT * FROM series WHERE id=:id");
    $query->bindParam(':id', $s_id);
    $rows = $pdo->exec_($query);
    if ($pdo->numrows > 0) {

      foreach ($rows as $row) {
        $series_id = $row->id;
        $series_name = $row->name;
        $series_slug = $row->slug;
        $series_lang = $row->series_lang;
        $series_link = $row->series_link;
        $series_author = $row->series_author;
        $series_descr = $row->series_descr;
        $series_summary = $row->series_summary;
        $series_owner = $row->series_owner;
        $series_email = $row->series_email;
        $series_keywords = $row->series_keywords;
        $series_explicit = $row->series_explicit;
        $series_cat1 = $row->series_cat1;
        $series_cat2 = $row->series_cat2;
        $series_cat3 = $row->series_cat3;
        $series_cat4 = $row->series_cat4;
        $series_cat5 = $row->series_cat5;
        $series_copy = $row->series_copy;

        // File paths
        $pro_rss_path = $pro_path.$series_id.'-'.$pro_rss_name;
        $pro_podcast_path = $pro_path.$series_id.'-'.$pro_podcast_name;
        // Form
        echo '<form id="series-details" enctype="multipart/form-data">
        <input type="hidden" name="u_id" value="'.$user_id.'">
        <input type="hidden" name="s_id" value="'.$series_id.'">
        </form>';
        // Contents
        echo '<table class="contentlib"><tbody>
        <tr><td colspan="4">
        <button type="button" onclick="detailsSave('.$series_id.', '.$u_id.');">Save</button>&nbsp;
        <button type="button" onclick="seriesEditor('.$u_id.');">Cancel</button>
        </td></tr>
        <tr><td>
          <label for="input-name">Name: </label><br><br><input type="text" form="series-details" id="input-name" name="series_name" value="'.$series_name.'">
        </td><td>
          <label for="input-slug">Slug: </label><br><br><input type="text" form="series-details" id="input-slug" name="series_slug" value="'.$series_slug.'">
        </td><td class="settings-pro-image" id="pro-rss-upload">';
          // RSS feed
          echo '<b>RSS</b> <small>(JPEG 144x144)</small> <input type="file" name="pro-rss" id="pro-rss" form="series-details"><br><br>';
          if (file_exists($pro_rss_path)) {
            echo '<img id="rss-image-'.$series_id.'" style="max-width:50px; max-height:50px; display:inline;" src="'.$pro_rss_path.'">';
            echo '<code id="rss-none-'.$series_id.'" style="max-width:50px; max-height:50px; display:none;" class="gray"><i>no image</i></code>';
          } else {
            echo '<img id="rss-image-'.$series_id.'" style="max-width:50px; max-height:50px; display:none;" src="'.$pro_rss_path.'">';
            echo '<code id="rss-none-'.$series_id.'" style="max-width:50px; max-height:50px; display:inline;" class="gray"><i>no image</i></code>';

          }
        echo '</td>';
        echo '<td class="settings-pro-image" id="pro-podcast-upload">';
          // iTunes Podcast
          echo '<b>Podcast</b> <small>(JPEG 3000x3000)</small> <input type="file" name="pro-podcast" id="pro-podcast" form="series-details"><br><br>';
          if (file_exists($pro_podcast_path)) {
            echo '<img id="podcast-image-'.$series_id.'" style="max-width:50px; max-height:50px; display:inline;" src="'.$pro_podcast_path.'">';
            echo '<code id="podcast-none-'.$series_id.'" style="max-width:50px; max-height:50px; display:none;" class="gray"><i>no image</i></code>';
          } else {
            echo '<img id="podcast-image-'.$series_id.'" style="max-width:50px; max-height:50px; display:none;" src="'.$pro_podcast_path.'">';
            echo '<code id="podcast-none-'.$series_id.'" style="max-width:50px; max-height:50px; display:inline;" class="gray"><i>no image</i></code>';
          }
        echo '</td></tr></tbody></table>';

        // Three-column table
        echo '<table class="contentlib"><tbody><tr>
        <td>';

        // Categories
        echo '<label for="input-cat-1">Category 1: </label><br><br>
        <select id="input-cat-1" name="series_cat1" form="series-details">';
        iTunesCat($series_cat1);
        echo '</select><br><br>';
        echo '<label for="input-cat-2">Category 2: </label><br><br>
        <select id="input-cat-2" name="series_cat2" form="series-details">';
        iTunesCat($series_cat2);
        echo '</select><br><br>';
        echo '<label for="input-cat-3">Category 3: </label><br><br>
        <select id="input-cat-3" name="series_cat3" form="series-details">';
        iTunesCat($series_cat3);
        echo '</select><br><br>';
        echo '<label for="input-cat-4">Category 4: </label><br><br>
        <select id="input-cat-4" name="series_cat4" form="series-details">';
        iTunesCat($series_cat4);
        echo '</select><br><br>';
        echo '<label for="input-cat-5">Category 5: </label><br><br>
        <select id="input-cat-5" name="series_cat5" form="series-details">';
        iTunesCat($series_cat5);
        echo '</select>
        </td>';

        echo '
        <td>
          <label for="input-author">Author: </label><br><br><input type="text" form="series-details" id="input-author" name="series_author" value="'.$series_author.'">
        <br><br>
          <label for="input-owner">Owner: </label><br><br><input type="text" form="series-details" id="input-owner" name="series_owner" value="'.$series_owner.'">
        <br><br>
          <label for="input-email">Email: </label><br><br><input type="email" form="series-details" id="input-email" name="series_email" value="'.$series_email.'">
        <br><br>
          <label for="input-link">URL Link: </label><br><br><input type="url" form="series-details" id="input-link" name="series_link" value="'.$series_link.'">
        <br><br>
          <label for="input-copy">Copyright line: </label><br><br><input type="text" form="series-details" id="input-copy" name="series_copy" value="'.$series_copy.'">
          <br><br>
        </td>';

        // Subtitle & Summary
        echo
        '<td>
          <label for="input-descr">Subtitle/Description: </label><br><br>
          <textarea id="input-descr" name="series_descr" form="series-details" rows="4" cols="50">'.$series_descr.'</textarea>
        <br><br>
          <label for="input-summary">Summary: </label><br><br>
          <textarea id="input-summary" name="series_summary" form="series-details" rows="4" cols="50">'.$series_summary.'</textarea>
        <br><br>';

        // Keywords & Language
        echo '
            <label for="input-keywords">Keywords: (comma-separated list) </label><br><br><input type="text" form="series-details" id="input-keywords" name="series_keywords" value="'.$series_keywords.'">
          <br><br>
            <label for="input-lang">Language: </label>
            <select id="input-lang" name="series_lang" form="series-details">
              <option value="af"'; echo ($series_lang == "af") ? ' selected="selected"' : ''; echo '>Afrikaans</option>
              <option value="sq"'; echo ($series_lang == "sq") ? ' selected="selected"' : ''; echo '>Albanian</option>
              <option value="ar"'; echo ($series_lang == "ar") ? ' selected="selected"' : ''; echo '>Arabic</option>
              <option value="en"'; echo ($series_lang == "en") ? ' selected="selected"' : ''; echo '>English</option>
              <option value="bn"'; echo ($series_lang == "bn") ? ' selected="selected"' : ''; echo '>Bengali</option>
              <option value="cs"'; echo ($series_lang == "cs") ? ' selected="selected"' : ''; echo '>Czech</option>
              <option value="zh"'; echo ($series_lang == "zh") ? ' selected="selected"' : ''; echo '>Chinese</option>
              <option value="nl"'; echo ($series_lang == "nl") ? ' selected="selected"' : ''; echo '>Dutch</option>
              <option value="en"'; echo ($series_lang == "en") ? ' selected="selected"' : ''; echo '>English</option>
              <option value="fr"'; echo ($series_lang == "fr") ? ' selected="selected"' : ''; echo '>French</option>
              <option value="ka"'; echo ($series_lang == "ka") ? ' selected="selected"' : ''; echo '>Georgian</option>
              <option value="de"'; echo ($series_lang == "de") ? ' selected="selected"' : ''; echo '>German</option>
              <option value="el"'; echo ($series_lang == "el") ? ' selected="selected"' : ''; echo '>Greek</option>
              <option value="gu"'; echo ($series_lang == "gu") ? ' selected="selected"' : ''; echo '>Gujarati</option>
              <option value="ha"'; echo ($series_lang == "ha") ? ' selected="selected"' : ''; echo '>Hausa</option>
              <option value="he"'; echo ($series_lang == "he") ? ' selected="selected"' : ''; echo '>Hebrew</option>
              <option value="hi"'; echo ($series_lang == "hi") ? ' selected="selected"' : ''; echo '>Hindi</option>
              <option value="ga"'; echo ($series_lang == "ga") ? ' selected="selected"' : ''; echo '>Irish</option>
              <option value="id"'; echo ($series_lang == "id") ? ' selected="selected"' : ''; echo '>Indonesian</option>
              <option value="it"'; echo ($series_lang == "it") ? ' selected="selected"' : ''; echo '>Italian</option>
              <option value="ja"'; echo ($series_lang == "ja") ? ' selected="selected"' : ''; echo '>Japanese</option>
              <option value="jv"'; echo ($series_lang == "jv") ? ' selected="selected"' : ''; echo '>Javanese</option>
              <option value="ko"'; echo ($series_lang == "ko") ? ' selected="selected"' : ''; echo '>Korean</option>
              <option value="ml"'; echo ($series_lang == "ml") ? ' selected="selected"' : ''; echo '>Malay</option>
              <option value="mr"'; echo ($series_lang == "mr") ? ' selected="selected"' : ''; echo '>Marathi</option>
              <option value="nn"'; echo ($series_lang == "nn") ? ' selected="selected"' : ''; echo '>Norwegian</option>
              <option value="fa"'; echo ($series_lang == "fa") ? ' selected="selected"' : ''; echo '>Persian</option>
              <option value="pl"'; echo ($series_lang == "pl") ? ' selected="selected"' : ''; echo '>Polish</option>
              <option value="pt"'; echo ($series_lang == "pt") ? ' selected="selected"' : ''; echo '>Portuguese</option>
              <option value="pa"'; echo ($series_lang == "pa") ? ' selected="selected"' : ''; echo '>Punjabi</option>
              <option value="ro"'; echo ($series_lang == "ro") ? ' selected="selected"' : ''; echo '>Romanian</option>
              <option value="ru"'; echo ($series_lang == "ru") ? ' selected="selected"' : ''; echo '>Russian</option>
              <option value="sm"'; echo ($series_lang == "sm") ? ' selected="selected"' : ''; echo '>Samoan</option>
              <option value="sd"'; echo ($series_lang == "sd") ? ' selected="selected"' : ''; echo '>Sindhi</option>
              <option value="es"'; echo ($series_lang == "es") ? ' selected="selected"' : ''; echo '>Spanish</option>
              <option value="su"'; echo ($series_lang == "su") ? ' selected="selected"' : ''; echo '>Sundanese</option>
              <option value="sw"'; echo ($series_lang == "sw") ? ' selected="selected"' : ''; echo '>Swahili</option>
              <option value="ty"'; echo ($series_lang == "ty") ? ' selected="selected"' : ''; echo '>Tahitian</option>
              <option value="ta"'; echo ($series_lang == "ta") ? ' selected="selected"' : ''; echo '>Tamil</option>
              <option value="te"'; echo ($series_lang == "te") ? ' selected="selected"' : ''; echo '>Telugu</option>
              <option value="bo"'; echo ($series_lang == "bo") ? ' selected="selected"' : ''; echo '>Tibetan</option>
              <option value="th"'; echo ($series_lang == "th") ? ' selected="selected"' : ''; echo '>Thai</option>
              <option value="sk"'; echo ($series_lang == "sk") ? ' selected="selected"' : ''; echo '>Slovak</option>
              <option value="sv"'; echo ($series_lang == "sv") ? ' selected="selected"' : ''; echo '>Swedish</option>
              <option value="uk"'; echo ($series_lang == "uk") ? ' selected="selected"' : ''; echo '>Ukrainian</option>
              <option value="ur"'; echo ($series_lang == "ur") ? ' selected="selected"' : ''; echo '>Urdu</option>
              <option value="ug"'; echo ($series_lang == "ug") ? ' selected="selected"' : ''; echo '>Uyghur</option>
              <option value="vi"'; echo ($series_lang == "vi") ? ' selected="selected"' : ''; echo '>Vietnamese</option>
              <option value="yo"'; echo ($series_lang == "yo") ? ' selected="selected"' : ''; echo '>Yoruba</option>
              <option value="zu"'; echo ($series_lang == "zu") ? ' selected="selected"' : ''; echo '>Zulu</option>
            </select>
            &nbsp;
            <label for="explicit-true"><input type="radio" id="explicit-true" name="series_explicit" value="true" form="series-details"'; echo ($series_explicit == "true") ? ' checked' : ''; echo '> explicit</label>
            <label for="explicit-false"><input type="radio" id="explicit-false" name="series_explicit" value="false" form="series-details"'; echo ($series_explicit == "false") ? ' checked' : ''; echo '> clean</label>
          ';

        // Finish table
        echo '</td></tr></tbody></table>';

      }

      echo '</div>';

    } else { // If no entries in the series table
      echo "<p>That's strange. Nothing here.</p>";
    }

  } // seriesEditor AJAX loader

} else { // End POST check
  exit ();
}

?>
