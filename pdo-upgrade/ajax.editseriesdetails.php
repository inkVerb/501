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
$pro_rss_path = $pro_path.$pro_rss_name;
$pro_podcast_name = 'series-podcast.jpg';

// Function for iTunes categories
function iTunesCat($series_cat) {
  $cat = 'None'; $val = ''; echo '<option value="'.$val.'"'; echo ($series_cat == $val) ? ' selected' : ''; echo '>'.$cat.'</option>';
  $cat = 'Arts'; $val = 'Arts'; echo '<option value="'.$val.'"'; echo ($series_cat == $val) ? ' selected' : ''; echo '>'.$cat.'</option>';
  $cat = '- Books'; $val = 'Arts::Books'; echo '<option value="'.$val.'"'; echo ($series_cat == $val) ? ' selected' : ''; echo '>'.$cat.'</option>';
  $cat = '- Design'; $val = 'Arts::Design'; echo '<option value="'.$val.'"'; echo ($series_cat == $val) ? ' selected' : ''; echo '>'.$cat.'</option>';
  $cat = '- Fashion &amp; Beauty'; $val = 'Arts::Fashion &amp; Beauty'; echo '<option value="'.$val.'"'; echo ($series_cat == $val) ? ' selected' : ''; echo '>'.$cat.'</option>';
  $cat = '- Food'; $val = 'Arts::Food'; echo '<option value="'.$val.'"'; echo ($series_cat == $val) ? ' selected' : ''; echo '>'.$cat.'</option>';
  $cat = '- Performing Arts'; $val = 'Arts::Performing Arts'; echo '<option value="'.$val.'"'; echo ($series_cat == $val) ? ' selected' : ''; echo '>'.$cat.'</option>';
  $cat = '- Visual Arts'; $val = 'Arts::Visual Arts'; echo '<option value="'.$val.'"'; echo ($series_cat == $val) ? ' selected' : ''; echo '>'.$cat.'</option>';
  $cat = 'Business'; $val = 'Business'; echo '<option value="'.$val.'"'; echo ($series_cat == $val) ? ' selected' : ''; echo '>'.$cat.'</option>';
  $cat = '- Careers'; $val = 'Business::Careers'; echo '<option value="'.$val.'"'; echo ($series_cat == $val) ? ' selected' : ''; echo '>'.$cat.'</option>';
  $cat = '- Entrepreneurship'; $val = 'Business::Entrepreneurship'; echo '<option value="'.$val.'"'; echo ($series_cat == $val) ? ' selected' : ''; echo '>'.$cat.'</option>';
  $cat = '- Investing'; $val = 'Business::Investing'; echo '<option value="'.$val.'"'; echo ($series_cat == $val) ? ' selected' : ''; echo '>'.$cat.'</option>';
  $cat = '- Management'; $val = 'Business::Management'; echo '<option value="'.$val.'"'; echo ($series_cat == $val) ? ' selected' : ''; echo '>'.$cat.'</option>';
  $cat = '- Marketing'; $val = 'Business::Marketing'; echo '<option value="'.$val.'"'; echo ($series_cat == $val) ? ' selected' : ''; echo '>'.$cat.'</option>';
  $cat = '- Non-Profit'; $val = 'Business::Non-Profit'; echo '<option value="'.$val.'"'; echo ($series_cat == $val) ? ' selected' : ''; echo '>'.$cat.'</option>';
  $cat = 'Comedy'; $val = 'Comedy'; echo '<option value="'.$val.'"'; echo ($series_cat == $val) ? ' selected' : ''; echo '>'.$cat.'</option>';
  $cat = '- Comedy Interviews'; $val = 'Comedy::Comedy Interviews'; echo '<option value="'.$val.'"'; echo ($series_cat == $val) ? ' selected' : ''; echo '>'.$cat.'</option>';
  $cat = '- Improv'; $val = 'Comedy::Improv'; echo '<option value="'.$val.'"'; echo ($series_cat == $val) ? ' selected' : ''; echo '>'.$cat.'</option>';
  $cat = '- Stand-Up'; $val = 'Comedy::Stand-Up'; echo '<option value="'.$val.'"'; echo ($series_cat == $val) ? ' selected' : ''; echo '>'.$cat.'</option>';
  $cat = 'Education'; $val = 'Education'; echo '<option value="'.$val.'"'; echo ($series_cat == $val) ? ' selected' : ''; echo '>'.$cat.'</option>';
  $cat = '- Courses'; $val = 'Education::Courses'; echo '<option value="'.$val.'"'; echo ($series_cat == $val) ? ' selected' : ''; echo '>'.$cat.'</option>';
  $cat = '- How To'; $val = 'Education::How To'; echo '<option value="'.$val.'"'; echo ($series_cat == $val) ? ' selected' : ''; echo '>'.$cat.'</option>';
  $cat = '- Language Learning'; $val = 'Education::Language Learning'; echo '<option value="'.$val.'"'; echo ($series_cat == $val) ? ' selected' : ''; echo '>'.$cat.'</option>';
  $cat = '- Self-Improvement'; $val = 'Education::Self-Improvement'; echo '<option value="'.$val.'"'; echo ($series_cat == $val) ? ' selected' : ''; echo '>'.$cat.'</option>';
  $cat = 'Fiction'; $val = 'Fiction'; echo '<option value="'.$val.'"'; echo ($series_cat == $val) ? ' selected' : ''; echo '>'.$cat.'</option>';
  $cat = '- Comedy Fiction'; $val = 'Fiction::Comedy Fiction'; echo '<option value="'.$val.'"'; echo ($series_cat == $val) ? ' selected' : ''; echo '>'.$cat.'</option>';
  $cat = '- Drama'; $val = 'Fiction::Drama'; echo '<option value="'.$val.'"'; echo ($series_cat == $val) ? ' selected' : ''; echo '>'.$cat.'</option>';
  $cat = '- Science Fiction'; $val = 'Fiction::Science Fiction'; echo '<option value="'.$val.'"'; echo ($series_cat == $val) ? ' selected' : ''; echo '>'.$cat.'</option>';
  $cat = 'Government'; $val = 'Government'; echo '<option value="'.$val.'"'; echo ($series_cat == $val) ? ' selected' : ''; echo '>'.$cat.'</option>';
  $cat = 'History'; $val = 'History'; echo '<option value="'.$val.'"'; echo ($series_cat == $val) ? ' selected' : ''; echo '>'.$cat.'</option>';
  $cat = 'Health &amp; Fitness'; $val = 'Health &amp; Fitness'; echo '<option value="'.$val.'"'; echo ($series_cat == $val) ? ' selected' : ''; echo '>'.$cat.'</option>';
  $cat = '- Alternative Health'; $val = 'Health &amp; Fitness::Alternative Health'; echo '<option value="'.$val.'"'; echo ($series_cat == $val) ? ' selected' : ''; echo '>'.$cat.'</option>';
  $cat = '- Fitness'; $val = 'Health &amp; Fitness::Fitness'; echo '<option value="'.$val.'"'; echo ($series_cat == $val) ? ' selected' : ''; echo '>'.$cat.'</option>';
  $cat = '- Medicine'; $val = 'Health &amp; Fitness::Medicine'; echo '<option value="'.$val.'"'; echo ($series_cat == $val) ? ' selected' : ''; echo '>'.$cat.'</option>';
  $cat = '- Mental Health'; $val = 'Health &amp; Fitness::Mental Health'; echo '<option value="'.$val.'"'; echo ($series_cat == $val) ? ' selected' : ''; echo '>'.$cat.'</option>';
  $cat = '- Nutrition'; $val = 'Health &amp; Fitness::Nutrition'; echo '<option value="'.$val.'"'; echo ($series_cat == $val) ? ' selected' : ''; echo '>'.$cat.'</option>';
  $cat = '- Sexuality'; $val = 'Health &amp; Fitness::Sexuality'; echo '<option value="'.$val.'"'; echo ($series_cat == $val) ? ' selected' : ''; echo '>'.$cat.'</option>';
  $cat = 'Kids &amp; Family'; $val = 'Kids &amp; Family'; echo '<option value="'.$val.'"'; echo ($series_cat == $val) ? ' selected' : ''; echo '>'.$cat.'</option>';
  $cat = '- Education for Kids'; $val = 'Kids &amp; Family::Education for Kids'; echo '<option value="'.$val.'"'; echo ($series_cat == $val) ? ' selected' : ''; echo '>'.$cat.'</option>';
  $cat = '- Parenting'; $val = 'Kids &amp; Family::Parenting'; echo '<option value="'.$val.'"'; echo ($series_cat == $val) ? ' selected' : ''; echo '>'.$cat.'</option>';
  $cat = '- Pets &amp; Animals'; $val = 'Kids &amp; Family::Pets &amp; Animals'; echo '<option value="'.$val.'"'; echo ($series_cat == $val) ? ' selected' : ''; echo '>'.$cat.'</option>';
  $cat = '- Stories for Kids'; $val = 'Kids &amp; Family::Stories for Kids'; echo '<option value="'.$val.'"'; echo ($series_cat == $val) ? ' selected' : ''; echo '>'.$cat.'</option>';
  $cat = 'Leisure'; $val = 'Leisure'; echo '<option value="'.$val.'"'; echo ($series_cat == $val) ? ' selected' : ''; echo '>'.$cat.'</option>';
  $cat = '- Animation &amp; Manga'; $val = 'Leisure::Animation &amp; Manga'; echo '<option value="'.$val.'"'; echo ($series_cat == $val) ? ' selected' : ''; echo '>'.$cat.'</option>';
  $cat = '- Automotive'; $val = 'Leisure::Automotive'; echo '<option value="'.$val.'"'; echo ($series_cat == $val) ? ' selected' : ''; echo '>'.$cat.'</option>';
  $cat = '- Aviation'; $val = 'Leisure::Aviation'; echo '<option value="'.$val.'"'; echo ($series_cat == $val) ? ' selected' : ''; echo '>'.$cat.'</option>';
  $cat = '- Crafts'; $val = 'Leisure::Crafts'; echo '<option value="'.$val.'"'; echo ($series_cat == $val) ? ' selected' : ''; echo '>'.$cat.'</option>';
  $cat = '- Games'; $val = 'Leisure::Games'; echo '<option value="'.$val.'"'; echo ($series_cat == $val) ? ' selected' : ''; echo '>'.$cat.'</option>';
  $cat = '- Hobbies'; $val = 'Leisure::Hobbies'; echo '<option value="'.$val.'"'; echo ($series_cat == $val) ? ' selected' : ''; echo '>'.$cat.'</option>';
  $cat = '- Home &amp; Garden'; $val = 'Leisure::Home &amp; Garden'; echo '<option value="'.$val.'"'; echo ($series_cat == $val) ? ' selected' : ''; echo '>'.$cat.'</option>';
  $cat = '- Video Games'; $val = 'Leisure::Video Games'; echo '<option value="'.$val.'"'; echo ($series_cat == $val) ? ' selected' : ''; echo '>'.$cat.'</option>';
  $cat = 'Music'; $val = 'Music'; echo '<option value="'.$val.'"'; echo ($series_cat == $val) ? ' selected' : ''; echo '>'.$cat.'</option>';
  $cat = '- Music Commentary'; $val = 'Music::Music Commentary'; echo '<option value="'.$val.'"'; echo ($series_cat == $val) ? ' selected' : ''; echo '>'.$cat.'</option>';
  $cat = '- Music History'; $val = 'Music::Music History'; echo '<option value="'.$val.'"'; echo ($series_cat == $val) ? ' selected' : ''; echo '>'.$cat.'</option>';
  $cat = '- Music Interviews'; $val = 'Music::Music Interviews'; echo '<option value="'.$val.'"'; echo ($series_cat == $val) ? ' selected' : ''; echo '>'.$cat.'</option>';
  $cat = 'News'; $val = 'News'; echo '<option value="'.$val.'"'; echo ($series_cat == $val) ? ' selected' : ''; echo '>'.$cat.'</option>';
  $cat = '- Business News'; $val = 'News::Business News'; echo '<option value="'.$val.'"'; echo ($series_cat == $val) ? ' selected' : ''; echo '>'.$cat.'</option>';
  $cat = '- Daily News'; $val = 'News::Daily News'; echo '<option value="'.$val.'"'; echo ($series_cat == $val) ? ' selected' : ''; echo '>'.$cat.'</option>';
  $cat = '- Entertainment News'; $val = 'News::Entertainment News'; echo '<option value="'.$val.'"'; echo ($series_cat == $val) ? ' selected' : ''; echo '>'.$cat.'</option>';
  $cat = '- News Commentary'; $val = 'News::News Commentary'; echo '<option value="'.$val.'"'; echo ($series_cat == $val) ? ' selected' : ''; echo '>'.$cat.'</option>';
  $cat = '- Politics'; $val = 'News::Politics'; echo '<option value="'.$val.'"'; echo ($series_cat == $val) ? ' selected' : ''; echo '>'.$cat.'</option>';
  $cat = '- Sports News'; $val = 'News::Sports News'; echo '<option value="'.$val.'"'; echo ($series_cat == $val) ? ' selected' : ''; echo '>'.$cat.'</option>';
  $cat = '- Tech News'; $val = 'News::Tech News'; echo '<option value="'.$val.'"'; echo ($series_cat == $val) ? ' selected' : ''; echo '>'.$cat.'</option>';
  $cat = 'Religion &amp; Spirituality'; $val = 'Religion &amp; Spirituality'; echo '<option value="'.$val.'"'; echo ($series_cat == $val) ? ' selected' : ''; echo '>'.$cat.'</option>';
  $cat = '- Buddhism'; $val = 'Religion &amp; Spirituality::Buddhism'; echo '<option value="'.$val.'"'; echo ($series_cat == $val) ? ' selected' : ''; echo '>'.$cat.'</option>';
  $cat = '- Christianity'; $val = 'Religion &amp; Spirituality::Christianity'; echo '<option value="'.$val.'"'; echo ($series_cat == $val) ? ' selected' : ''; echo '>'.$cat.'</option>';
  $cat = '- Hinduism'; $val = 'Religion &amp; Spirituality::Hinduism'; echo '<option value="'.$val.'"'; echo ($series_cat == $val) ? ' selected' : ''; echo '>'.$cat.'</option>';
  $cat = '- Islam'; $val = 'Religion &amp; Spirituality::Islam'; echo '<option value="'.$val.'"'; echo ($series_cat == $val) ? ' selected' : ''; echo '>'.$cat.'</option>';
  $cat = '- Judiasm'; $val = 'Religion &amp; Spirituality::Judiasm'; echo '<option value="'.$val.'"'; echo ($series_cat == $val) ? ' selected' : ''; echo '>'.$cat.'</option>';
  $cat = '- Religion'; $val = 'Religion &amp; Spirituality::Religion'; echo '<option value="'.$val.'"'; echo ($series_cat == $val) ? ' selected' : ''; echo '>'.$cat.'</option>';
  $cat = '- Spirituality'; $val = 'Religion &amp; Spirituality::Spirituality '; echo '<option value="'.$val.'"'; echo ($series_cat == $val) ? ' selected' : ''; echo '>'.$cat.'</option>';
  $cat = 'Science'; $val = 'Science'; echo '<option value="'.$val.'"'; echo ($series_cat == $val) ? ' selected' : ''; echo '>'.$cat.'</option>';
  $cat = '- Astronomy'; $val = 'Science::Astronomy'; echo '<option value="'.$val.'"'; echo ($series_cat == $val) ? ' selected' : ''; echo '>'.$cat.'</option>';
  $cat = '- Chemistry'; $val = 'Science::Chemistry'; echo '<option value="'.$val.'"'; echo ($series_cat == $val) ? ' selected' : ''; echo '>'.$cat.'</option>';
  $cat = '- Earth Sciences'; $val = 'Science::Earth Sciences'; echo '<option value="'.$val.'"'; echo ($series_cat == $val) ? ' selected' : ''; echo '>'.$cat.'</option>';
  $cat = '- Life Sciences'; $val = 'Science::Life Sciences'; echo '<option value="'.$val.'"'; echo ($series_cat == $val) ? ' selected' : ''; echo '>'.$cat.'</option>';
  $cat = '- Mathematics'; $val = 'Science::Mathematics'; echo '<option value="'.$val.'"'; echo ($series_cat == $val) ? ' selected' : ''; echo '>'.$cat.'</option>';
  $cat = '- Natural Sciences'; $val = 'Science::Natural Sciences'; echo '<option value="'.$val.'"'; echo ($series_cat == $val) ? ' selected' : ''; echo '>'.$cat.'</option>';
  $cat = '- Nature'; $val = 'Science::Nature'; echo '<option value="'.$val.'"'; echo ($series_cat == $val) ? ' selected' : ''; echo '>'.$cat.'</option>';
  $cat = '- Physics'; $val = 'Science::Physics'; echo '<option value="'.$val.'"'; echo ($series_cat == $val) ? ' selected' : ''; echo '>'.$cat.'</option>';
  $cat = '- Social Sciences'; $val = 'Science::Social Sciences'; echo '<option value="'.$val.'"'; echo ($series_cat == $val) ? ' selected' : ''; echo '>'.$cat.'</option>';
  $cat = 'Society &amp; Culture'; $val = 'Society &amp; Culture'; echo '<option value="'.$val.'"'; echo ($series_cat == $val) ? ' selected' : ''; echo '>'.$cat.'</option>';
  $cat = '- Documentary'; $val = 'Society &amp; Culture::Documentary'; echo '<option value="'.$val.'"'; echo ($series_cat == $val) ? ' selected' : ''; echo '>'.$cat.'</option>';
  $cat = '- Personal Journals'; $val = 'Society &amp; Culture::Personal Journals'; echo '<option value="'.$val.'"'; echo ($series_cat == $val) ? ' selected' : ''; echo '>'.$cat.'</option>';
  $cat = '- Philosophy'; $val = 'Society &amp; Culture::Philosophy'; echo '<option value="'.$val.'"'; echo ($series_cat == $val) ? ' selected' : ''; echo '>'.$cat.'</option>';
  $cat = '- Places &amp; Travel'; $val = 'Society &amp; Culture::Places &amp; Travel'; echo '<option value="'.$val.'"'; echo ($series_cat == $val) ? ' selected' : ''; echo '>'.$cat.'</option>';
  $cat = '- Relationships'; $val = 'Society &amp; Culture::Relationships'; echo '<option value="'.$val.'"'; echo ($series_cat == $val) ? ' selected' : ''; echo '>'.$cat.'</option>';
  $cat = 'Sports'; $val = 'Sports'; echo '<option value="'.$val.'"'; echo ($series_cat == $val) ? ' selected' : ''; echo '>'.$cat.'</option>';
  $cat = '- Baseball'; $val = 'Sports::Baseball'; echo '<option value="'.$val.'"'; echo ($series_cat == $val) ? ' selected' : ''; echo '>'.$cat.'</option>';
  $cat = '- Basketball'; $val = 'Sports::Basketball'; echo '<option value="'.$val.'"'; echo ($series_cat == $val) ? ' selected' : ''; echo '>'.$cat.'</option>';
  $cat = '- Cricket'; $val = 'Sports::Cricket'; echo '<option value="'.$val.'"'; echo ($series_cat == $val) ? ' selected' : ''; echo '>'.$cat.'</option>';
  $cat = '- Fantasy Sports'; $val = 'Sports::Fantasy Sports'; echo '<option value="'.$val.'"'; echo ($series_cat == $val) ? ' selected' : ''; echo '>'.$cat.'</option>';
  $cat = '- Football'; $val = 'Sports::Football'; echo '<option value="'.$val.'"'; echo ($series_cat == $val) ? ' selected' : ''; echo '>'.$cat.'</option>';
  $cat = '- Golf'; $val = 'Sports::Golf'; echo '<option value="'.$val.'"'; echo ($series_cat == $val) ? ' selected' : ''; echo '>'.$cat.'</option>';
  $cat = '- Hockey'; $val = 'Sports::Hockey'; echo '<option value="'.$val.'"'; echo ($series_cat == $val) ? ' selected' : ''; echo '>'.$cat.'</option>';
  $cat = '- Rugby'; $val = 'Sports::Rugby'; echo '<option value="'.$val.'"'; echo ($series_cat == $val) ? ' selected' : ''; echo '>'.$cat.'</option>';
  $cat = '- Soccer'; $val = 'Sports::Soccer'; echo '<option value="'.$val.'"'; echo ($series_cat == $val) ? ' selected' : ''; echo '>'.$cat.'</option>';
  $cat = '- Swimming'; $val = 'Sports::Swimming'; echo '<option value="'.$val.'"'; echo ($series_cat == $val) ? ' selected' : ''; echo '>'.$cat.'</option>';
  $cat = '- Tennis'; $val = 'Sports::Tennis'; echo '<option value="'.$val.'"'; echo ($series_cat == $val) ? ' selected' : ''; echo '>'.$cat.'</option>';
  $cat = '- Volleyball'; $val = 'Sports::Volleyball'; echo '<option value="'.$val.'"'; echo ($series_cat == $val) ? ' selected' : ''; echo '>'.$cat.'</option>';
  $cat = '- Wilderness'; $val = 'Sports::Wilderness'; echo '<option value="'.$val.'"'; echo ($series_cat == $val) ? ' selected' : ''; echo '>'.$cat.'</option>';
  $cat = '- Wrestling'; $val = 'Sports::Wrestling'; echo '<option value="'.$val.'"'; echo ($series_cat == $val) ? ' selected' : ''; echo '>'.$cat.'</option>';
  $cat = 'Technology'; $val = 'Technology'; echo '<option value="'.$val.'"'; echo ($series_cat == $val) ? ' selected' : ''; echo '>'.$cat.'</option>';
  $cat = 'True Crime'; $val = 'True Crime'; echo '<option value="'.$val.'"'; echo ($series_cat == $val) ? ' selected' : ''; echo '>'.$cat.'</option>';
  $cat = 'TV &amp; Film'; $val = 'TV &amp; Film'; echo '<option value="'.$val.'"'; echo ($series_cat == $val) ? ' selected' : ''; echo '>'.$cat.'</option>';
  $cat = '- After Shows'; $val = 'TV &amp; Film::After Shows'; echo '<option value="'.$val.'"'; echo ($series_cat == $val) ? ' selected' : ''; echo '>'.$cat.'</option>';
  $cat = '- Film History'; $val = 'TV &amp; Film::Film History'; echo '<option value="'.$val.'"'; echo ($series_cat == $val) ? ' selected' : ''; echo '>'.$cat.'</option>';
  $cat = '- Film Interviews'; $val = 'TV &amp; Film::Film Interviews'; echo '<option value="'.$val.'"'; echo ($series_cat == $val) ? ' selected' : ''; echo '>'.$cat.'</option>';
  $cat = '- Film Reviews'; $val = 'TV &amp; Film::Film Reviews'; echo '<option value="'.$val.'"'; echo ($series_cat == $val) ? ' selected' : ''; echo '>'.$cat.'</option>';
  $cat = '- TV Reviews'; $val = 'TV &amp; Film::TV Reviews'; echo '<option value="'.$val.'"'; echo ($series_cat == $val) ? ' selected' : ''; echo '>'.$cat.'</option>';
}

// Check & validate for what we need
if ( ($_SERVER['REQUEST_METHOD'] === 'POST') && (!empty($_POST['u_id'])) && (filter_var($_POST['u_id'], FILTER_VALIDATE_INT)) && (isset($_SESSION['user_id'])) && (($_POST['u_id']) == (isset($_SESSION['user_id']))) ) {

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
  && (isset($_POST['series_descrpt']))
  && (isset($_POST['series_summary']))
  && (isset($_POST['series_owner']))
  && (isset($_POST['series_email']))
  && (isset($_POST['series_keywords']))
  && (isset($_POST['series_explicit']))
  && (isset($_POST['series_cat1']))
  && (isset($_POST['series_cat2']))
  && (isset($_POST['series_cat3']))
  && (isset($_POST['series_cat4']))
  && (isset($_POST['series_cat5']))
  && (isset($_POST['series_copy']))
  && (isset($_POST['series_credit'])) ) {

    // Assign the media ID and sanitize as the same time
    $s_id = preg_replace("/[^0-9]/"," ", $_POST['s_id']);

    // Set our reponse ID
    $ajax_response['s_id'] = $s_id;

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
              $upload_rss_success = true;
            } else {
              $ajax_response['message'] = '<p class="red">path:'.$pro_rss_path.' RSS image upload unknown failure.</p>';
              $ajax_response['change'] = 'nochange';
              $ajax_response['upload'] = 'failed';
              $ajax_response['new_podcast'] = 'notnew';
              $ajax_response['new_rss'] = 'notnew';
              // We're done here
              $json_response = json_encode($ajax_response, JSON_FORCE_OBJECT);
              echo $json_response;
              exit ();
            }

          } else {
            $ajax_response['message'] = '<p class="red">Logo is wrong size. Must be square and 144 pixels wide and high.</p>';
            $ajax_response['change'] = 'nochange';
            $ajax_response['upload'] = 'failed';
            $ajax_response['new_podcast'] = 'notnew';
            $ajax_response['new_rss'] = 'notnew';
            // We're done here
            $json_response = json_encode($ajax_response, JSON_FORCE_OBJECT);
            echo $json_response;
            exit ();
          }

        } else {
          $ajax_response['message'] = '<p class="red">RSS image is wrong formatt. Allowed: JPEG, PNG, GIF</p>';
          // We're done here
          $json_response = json_encode($ajax_response, JSON_FORCE_OBJECT);
          echo $json_response;
          exit ();
        }

      } else {
        $ajax_response['message'] = '<p class="red">RSS image file size is too big. Limit is 1MB.</p>';
        // We're done here
        $json_response = json_encode($ajax_response, JSON_FORCE_OBJECT);
        echo $json_response;
        exit ();
      }
    } elseif ((isset($_POST['pro-delete'])) && ($_POST['pro-delete'] == 'delete')) {
      $delete_rss_image = true;
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
              $upload_podcast_success = true;
            } else {
              $ajax_response['message'] =  '<p class="red">path:'.$pro_podcast_path.' Podcast image upload unknown failure.</p>';
              $ajax_response['change'] = 'nochange';
              $ajax_response['upload'] = 'failed';
              $ajax_response['new_podcast'] = 'notnew';
              $ajax_response['new_rss'] = 'notnew';
              // We're done here
              $json_response = json_encode($ajax_response, JSON_FORCE_OBJECT);
              echo $json_response;
              exit ();
            }

          } else {
            $ajax_response['message'] =  '<p class="red">Podcast image is wrong size. Must be square and 3000 pixels wide and high.</p>';
            $ajax_response['change'] = 'nochange';
            $ajax_response['upload'] = 'failed';
            $ajax_response['new_podcast'] = 'notnew';
            $ajax_response['new_rss'] = 'notnew';
            // We're done here
            $json_response = json_encode($ajax_response, JSON_FORCE_OBJECT);
            echo $json_response;
            exit ();
          }

        } else {
          $ajax_response['message'] =  '<p class="red">Podcast image is wrong formatt. Allowed: JPEG, PNG, GIF</p>';
          $ajax_response['change'] = 'nochange';
          $ajax_response['upload'] = 'failed';
          $ajax_response['new_podcast'] = 'notnew';
          $ajax_response['new_rss'] = 'notnew';
          // We're done here
          $json_response = json_encode($ajax_response, JSON_FORCE_OBJECT);
          echo $json_response;
          exit ();
        }

      } else {
        $ajax_response['message'] =  '<p class="red">Podcast image file size is too big. Limit is 1MB.</p>';
        $ajax_response['change'] = 'nochange';
        $ajax_response['upload'] = 'failed';
        $ajax_response['new_podcast'] = 'notnew';
        $ajax_response['new_rss'] = 'notnew';
        // We're done here
        $json_response = json_encode($ajax_response, JSON_FORCE_OBJECT);
        echo $json_response;
        exit ();
      }
    } elseif ((isset($_POST['pro-delete'])) && ($_POST['pro-delete'] == 'delete')) {
      $delete_podcast_image = true;
    }
    // End pro image uploads

    // No uploads
    // Series slug
    if (preg_replace('/\s+/', '', $_POST['series_slug']) != '') {
      $regex_replace = "/[^a-zA-Z0-9-]/";
      $result = filter_var($_POST['series_slug'], FILTER_SANITIZE_STRING); // Remove any HTML tags
      $result = strtolower(preg_replace($regex_replace,"-", $result)); // Lowercase, all non-alnum to hyphen
      $result = substr($result, 0, 90); // Limit to 90 characters
      $clean_slug = $result;
      $clean_slug_trim = DB::trimspace($clean_slug);
      $query = $database->prepare("SELECT id FROM series WHERE slug=:slug AND NOT id=:id");
      $query->bindParam(':slug', $clean_slug_trim);
      $query->bindParam(':id', $s_id);
      $rows = $pdo->exec_($query);
      if ($pdo->numrows > 0) {
        $ajax_response['message'] = '<span class="error notehide">Slug already in use!</span>';
        // We're done here
        $json_response = json_encode($ajax_response, JSON_FORCE_OBJECT);
        echo $json_response;
        exit ();
      }
    } else {
      $ajax_response['message'] = '<span class="error notehide">Must enter a series slug!</span>';
      // We're done here
      $json_response = json_encode($ajax_response, JSON_FORCE_OBJECT);
      echo $json_response;
      exit ();
    }
    // Series name
    if (preg_replace('/\s+/', '', $_POST['series_name']) != '') {
      $result = filter_var($_POST['series_name'], FILTER_SANITIZE_STRING); // Remove any HTML tags
      $result = preg_replace('/([0-9]$)+-+([0-9])/','$1–$2',$result); // to en-dash
      $result = str_replace(' -- ',' – ',$result); // to en-dash
      $result = str_replace('---','—',$result); // to em-dash
      $result = str_replace('--','—',$result); // to em-dash
      $result = substr($result, 0, 90); // Limit to 90 characters
      $series_name = $result;
      $series_name_trim = DB::trimspace($series_name);

      $query = $database->prepare("SELECT id FROM series WHERE name=:name AND NOT id=:id");
      $query->bindParam(':name', $series_name_trim);
      $query->bindParam(':id', $s_id);
      $rows = $pdo->exec_($query);
      if ($pdo->numrows > 0) {
        $ajax_response['message'] = '<span class="error notehide">Series name already in use!</span>';
        // We're done here
        $json_response = json_encode($ajax_response, JSON_FORCE_OBJECT);
        echo $json_response;
        exit ();
      }

    } else {
      $ajax_response['message'] = '<span class="error notehide">Must enter a series name!</span>';
      // We're done here
      $json_response = json_encode($ajax_response, JSON_FORCE_OBJECT);
      echo $json_response;
      exit ();
    }
    // Series XXXX
    if (preg_replace('/\s+/', '', $_POST['series_name']) != '') {
      $result = filter_var($_POST['series_name'], FILTER_SANITIZE_STRING); // Remove any HTML tags
      $result = preg_replace('/([0-9]$)+-+([0-9])/','$1–$2',$result); // to en-dash
      $result = str_replace(' -- ',' – ',$result); // to en-dash
      $result = str_replace('---','—',$result); // to em-dash
      $result = str_replace('--','—',$result); // to em-dash
      $result = substr($result, 0, 90); // Limit to 90 characters
      $series_name = $result;
      $series_name_trim = DB::trimspace($series_name);

      $query = $database->prepare("SELECT id FROM series WHERE name=:name AND NOT id=:id");
      $query->bindParam(':name', $series_name_trim);
      $query->bindParam(':id', $s_id);
      $rows = $pdo->exec_($query);
      if ($pdo->numrows > 0) {
        $ajax_response['message'] = '<span class="error notehide">Series name already in use!</span>';
        // We're done here
        $json_response = json_encode($ajax_response, JSON_FORCE_OBJECT);
        echo $json_response;
        exit ();
      }

    } else {
      $ajax_response['message'] = '<span class="error notehide">Must enter a series name!</span>';
      // We're done here
      $json_response = json_encode($ajax_response, JSON_FORCE_OBJECT);
      echo $json_response;
      exit ();
    }
    // Series XXXX
    if (preg_replace('/\s+/', '', $_POST['series_name']) != '') {
      $result = filter_var($_POST['series_name'], FILTER_SANITIZE_STRING); // Remove any HTML tags
      $result = preg_replace('/([0-9]$)+-+([0-9])/','$1–$2',$result); // to en-dash
      $result = str_replace(' -- ',' – ',$result); // to en-dash
      $result = str_replace('---','—',$result); // to em-dash
      $result = str_replace('--','—',$result); // to em-dash
      $result = substr($result, 0, 90); // Limit to 90 characters
      $series_name = $result;
      $series_name_trim = DB::trimspace($series_name);

      $query = $database->prepare("SELECT id FROM series WHERE name=:name AND NOT id=:id");
      $query->bindParam(':name', $series_name_trim);
      $query->bindParam(':id', $s_id);
      $rows = $pdo->exec_($query);
      if ($pdo->numrows > 0) {
        $ajax_response['message'] = '<span class="error notehide">Series name already in use!</span>';
        // We're done here
        $json_response = json_encode($ajax_response, JSON_FORCE_OBJECT);
        echo $json_response;
        exit ();
      }

    } else {
      $ajax_response['message'] = '<span class="error notehide">Must enter a series name!</span>';
      // We're done here
      $json_response = json_encode($ajax_response, JSON_FORCE_OBJECT);
      echo $json_response;
      exit ();
    }
    // Series XXXX
    if (preg_replace('/\s+/', '', $_POST['series_name']) != '') {
      $result = filter_var($_POST['series_name'], FILTER_SANITIZE_STRING); // Remove any HTML tags
      $result = preg_replace('/([0-9]$)+-+([0-9])/','$1–$2',$result); // to en-dash
      $result = str_replace(' -- ',' – ',$result); // to en-dash
      $result = str_replace('---','—',$result); // to em-dash
      $result = str_replace('--','—',$result); // to em-dash
      $result = substr($result, 0, 90); // Limit to 90 characters
      $series_name = $result;
      $series_name_trim = DB::trimspace($series_name);

      $query = $database->prepare("SELECT id FROM series WHERE name=:name AND NOT id=:id");
      $query->bindParam(':name', $series_name_trim);
      $query->bindParam(':id', $s_id);
      $rows = $pdo->exec_($query);
      if ($pdo->numrows > 0) {
        $ajax_response['message'] = '<span class="error notehide">Series name already in use!</span>';
        // We're done here
        $json_response = json_encode($ajax_response, JSON_FORCE_OBJECT);
        echo $json_response;
        exit ();
      }

    } else {
      $ajax_response['message'] = '<span class="error notehide">Must enter a series name!</span>';
      // We're done here
      $json_response = json_encode($ajax_response, JSON_FORCE_OBJECT);
      echo $json_response;
      exit ();
    }
    // Series XXXX
    if (preg_replace('/\s+/', '', $_POST['series_name']) != '') {
      $result = filter_var($_POST['series_name'], FILTER_SANITIZE_STRING); // Remove any HTML tags
      $result = preg_replace('/([0-9]$)+-+([0-9])/','$1–$2',$result); // to en-dash
      $result = str_replace(' -- ',' – ',$result); // to en-dash
      $result = str_replace('---','—',$result); // to em-dash
      $result = str_replace('--','—',$result); // to em-dash
      $result = substr($result, 0, 90); // Limit to 90 characters
      $series_name = $result;
      $series_name_trim = DB::trimspace($series_name);

      $query = $database->prepare("SELECT id FROM series WHERE name=:name AND NOT id=:id");
      $query->bindParam(':name', $series_name_trim);
      $query->bindParam(':id', $s_id);
      $rows = $pdo->exec_($query);
      if ($pdo->numrows > 0) {
        $ajax_response['message'] = '<span class="error notehide">Series name already in use!</span>';
        // We're done here
        $json_response = json_encode($ajax_response, JSON_FORCE_OBJECT);
        echo $json_response;
        exit ();
      }

    } else {
      $ajax_response['message'] = '<span class="error notehide">Must enter a series name!</span>';
      // We're done here
      $json_response = json_encode($ajax_response, JSON_FORCE_OBJECT);
      echo $json_response;
      exit ();
    }
    // Series XXXX
    if (preg_replace('/\s+/', '', $_POST['series_name']) != '') {
      $result = filter_var($_POST['series_name'], FILTER_SANITIZE_STRING); // Remove any HTML tags
      $result = preg_replace('/([0-9]$)+-+([0-9])/','$1–$2',$result); // to en-dash
      $result = str_replace(' -- ',' – ',$result); // to en-dash
      $result = str_replace('---','—',$result); // to em-dash
      $result = str_replace('--','—',$result); // to em-dash
      $result = substr($result, 0, 90); // Limit to 90 characters
      $series_name = $result;
      $series_name_trim = DB::trimspace($series_name);

      $query = $database->prepare("SELECT id FROM series WHERE name=:name AND NOT id=:id");
      $query->bindParam(':name', $series_name_trim);
      $query->bindParam(':id', $s_id);
      $rows = $pdo->exec_($query);
      if ($pdo->numrows > 0) {
        $ajax_response['message'] = '<span class="error notehide">Series name already in use!</span>';
        // We're done here
        $json_response = json_encode($ajax_response, JSON_FORCE_OBJECT);
        echo $json_response;
        exit ();
      }

    } else {
      $ajax_response['message'] = '<span class="error notehide">Must enter a series name!</span>';
      // We're done here
      $json_response = json_encode($ajax_response, JSON_FORCE_OBJECT);
      echo $json_response;
      exit ();
    }
    // Series XXXX
    if (preg_replace('/\s+/', '', $_POST['series_name']) != '') {
      $result = filter_var($_POST['series_name'], FILTER_SANITIZE_STRING); // Remove any HTML tags
      $result = preg_replace('/([0-9]$)+-+([0-9])/','$1–$2',$result); // to en-dash
      $result = str_replace(' -- ',' – ',$result); // to en-dash
      $result = str_replace('---','—',$result); // to em-dash
      $result = str_replace('--','—',$result); // to em-dash
      $result = substr($result, 0, 90); // Limit to 90 characters
      $series_name = $result;
      $series_name_trim = DB::trimspace($series_name);

      $query = $database->prepare("SELECT id FROM series WHERE name=:name AND NOT id=:id");
      $query->bindParam(':name', $series_name_trim);
      $query->bindParam(':id', $s_id);
      $rows = $pdo->exec_($query);
      if ($pdo->numrows > 0) {
        $ajax_response['message'] = '<span class="error notehide">Series name already in use!</span>';
        // We're done here
        $json_response = json_encode($ajax_response, JSON_FORCE_OBJECT);
        echo $json_response;
        exit ();
      }

    } else {
      $ajax_response['message'] = '<span class="error notehide">Must enter a series name!</span>';
      // We're done here
      $json_response = json_encode($ajax_response, JSON_FORCE_OBJECT);
      echo $json_response;
      exit ();
    }
    // Series XXXX
    if (preg_replace('/\s+/', '', $_POST['series_name']) != '') {
      $result = filter_var($_POST['series_name'], FILTER_SANITIZE_STRING); // Remove any HTML tags
      $result = preg_replace('/([0-9]$)+-+([0-9])/','$1–$2',$result); // to en-dash
      $result = str_replace(' -- ',' – ',$result); // to en-dash
      $result = str_replace('---','—',$result); // to em-dash
      $result = str_replace('--','—',$result); // to em-dash
      $result = substr($result, 0, 90); // Limit to 90 characters
      $series_name = $result;
      $series_name_trim = DB::trimspace($series_name);

      $query = $database->prepare("SELECT id FROM series WHERE name=:name AND NOT id=:id");
      $query->bindParam(':name', $series_name_trim);
      $query->bindParam(':id', $s_id);
      $rows = $pdo->exec_($query);
      if ($pdo->numrows > 0) {
        $ajax_response['message'] = '<span class="error notehide">Series name already in use!</span>';
        // We're done here
        $json_response = json_encode($ajax_response, JSON_FORCE_OBJECT);
        echo $json_response;
        exit ();
      }

    } else {
      $ajax_response['message'] = '<span class="error notehide">Must enter a series name!</span>';
      // We're done here
      $json_response = json_encode($ajax_response, JSON_FORCE_OBJECT);
      echo $json_response;
      exit ();
    }
    // Series XXXX
    if (preg_replace('/\s+/', '', $_POST['series_name']) != '') {
      $result = filter_var($_POST['series_name'], FILTER_SANITIZE_STRING); // Remove any HTML tags
      $result = preg_replace('/([0-9]$)+-+([0-9])/','$1–$2',$result); // to en-dash
      $result = str_replace(' -- ',' – ',$result); // to en-dash
      $result = str_replace('---','—',$result); // to em-dash
      $result = str_replace('--','—',$result); // to em-dash
      $result = substr($result, 0, 90); // Limit to 90 characters
      $series_name = $result;
      $series_name_trim = DB::trimspace($series_name);

      $query = $database->prepare("SELECT id FROM series WHERE name=:name AND NOT id=:id");
      $query->bindParam(':name', $series_name_trim);
      $query->bindParam(':id', $s_id);
      $rows = $pdo->exec_($query);
      if ($pdo->numrows > 0) {
        $ajax_response['message'] = '<span class="error notehide">Series name already in use!</span>';
        // We're done here
        $json_response = json_encode($ajax_response, JSON_FORCE_OBJECT);
        echo $json_response;
        exit ();
      }

    } else {
      $ajax_response['message'] = '<span class="error notehide">Must enter a series name!</span>';
      // We're done here
      $json_response = json_encode($ajax_response, JSON_FORCE_OBJECT);
      echo $json_response;
      exit ();
    }
    // Series XXXX
    if (preg_replace('/\s+/', '', $_POST['series_name']) != '') {
      $result = filter_var($_POST['series_name'], FILTER_SANITIZE_STRING); // Remove any HTML tags
      $result = preg_replace('/([0-9]$)+-+([0-9])/','$1–$2',$result); // to en-dash
      $result = str_replace(' -- ',' – ',$result); // to en-dash
      $result = str_replace('---','—',$result); // to em-dash
      $result = str_replace('--','—',$result); // to em-dash
      $result = substr($result, 0, 90); // Limit to 90 characters
      $series_name = $result;
      $series_name_trim = DB::trimspace($series_name);

      $query = $database->prepare("SELECT id FROM series WHERE name=:name AND NOT id=:id");
      $query->bindParam(':name', $series_name_trim);
      $query->bindParam(':id', $s_id);
      $rows = $pdo->exec_($query);
      if ($pdo->numrows > 0) {
        $ajax_response['message'] = '<span class="error notehide">Series name already in use!</span>';
        // We're done here
        $json_response = json_encode($ajax_response, JSON_FORCE_OBJECT);
        echo $json_response;
        exit ();
      }

    } else {
      $ajax_response['message'] = '<span class="error notehide">Must enter a series name!</span>';
      // We're done here
      $json_response = json_encode($ajax_response, JSON_FORCE_OBJECT);
      echo $json_response;
      exit ();
    }
    // Series XXXX
    if (preg_replace('/\s+/', '', $_POST['series_name']) != '') {
      $result = filter_var($_POST['series_name'], FILTER_SANITIZE_STRING); // Remove any HTML tags
      $result = preg_replace('/([0-9]$)+-+([0-9])/','$1–$2',$result); // to en-dash
      $result = str_replace(' -- ',' – ',$result); // to en-dash
      $result = str_replace('---','—',$result); // to em-dash
      $result = str_replace('--','—',$result); // to em-dash
      $result = substr($result, 0, 90); // Limit to 90 characters
      $series_name = $result;
      $series_name_trim = DB::trimspace($series_name);

      $query = $database->prepare("SELECT id FROM series WHERE name=:name AND NOT id=:id");
      $query->bindParam(':name', $series_name_trim);
      $query->bindParam(':id', $s_id);
      $rows = $pdo->exec_($query);
      if ($pdo->numrows > 0) {
        $ajax_response['message'] = '<span class="error notehide">Series name already in use!</span>';
        // We're done here
        $json_response = json_encode($ajax_response, JSON_FORCE_OBJECT);
        echo $json_response;
        exit ();
      }

    } else {
      $ajax_response['message'] = '<span class="error notehide">Must enter a series name!</span>';
      // We're done here
      $json_response = json_encode($ajax_response, JSON_FORCE_OBJECT);
      echo $json_response;
      exit ();
    }
    // Series XXXX
    if (preg_replace('/\s+/', '', $_POST['series_name']) != '') {
      $result = filter_var($_POST['series_name'], FILTER_SANITIZE_STRING); // Remove any HTML tags
      $result = preg_replace('/([0-9]$)+-+([0-9])/','$1–$2',$result); // to en-dash
      $result = str_replace(' -- ',' – ',$result); // to en-dash
      $result = str_replace('---','—',$result); // to em-dash
      $result = str_replace('--','—',$result); // to em-dash
      $result = substr($result, 0, 90); // Limit to 90 characters
      $series_name = $result;
      $series_name_trim = DB::trimspace($series_name);

      $query = $database->prepare("SELECT id FROM series WHERE name=:name AND NOT id=:id");
      $query->bindParam(':name', $series_name_trim);
      $query->bindParam(':id', $s_id);
      $rows = $pdo->exec_($query);
      if ($pdo->numrows > 0) {
        $ajax_response['message'] = '<span class="error notehide">Series name already in use!</span>';
        // We're done here
        $json_response = json_encode($ajax_response, JSON_FORCE_OBJECT);
        echo $json_response;
        exit ();
      }

    } else {
      $ajax_response['message'] = '<span class="error notehide">Must enter a series name!</span>';
      // We're done here
      $json_response = json_encode($ajax_response, JSON_FORCE_OBJECT);
      echo $json_response;
      exit ();
    }
    // Series XXXX
    if (preg_replace('/\s+/', '', $_POST['series_name']) != '') {
      $result = filter_var($_POST['series_name'], FILTER_SANITIZE_STRING); // Remove any HTML tags
      $result = preg_replace('/([0-9]$)+-+([0-9])/','$1–$2',$result); // to en-dash
      $result = str_replace(' -- ',' – ',$result); // to en-dash
      $result = str_replace('---','—',$result); // to em-dash
      $result = str_replace('--','—',$result); // to em-dash
      $result = substr($result, 0, 90); // Limit to 90 characters
      $series_name = $result;
      $series_name_trim = DB::trimspace($series_name);

      $query = $database->prepare("SELECT id FROM series WHERE name=:name AND NOT id=:id");
      $query->bindParam(':name', $series_name_trim);
      $query->bindParam(':id', $s_id);
      $rows = $pdo->exec_($query);
      if ($pdo->numrows > 0) {
        $ajax_response['message'] = '<span class="error notehide">Series name already in use!</span>';
        // We're done here
        $json_response = json_encode($ajax_response, JSON_FORCE_OBJECT);
        echo $json_response;
        exit ();
      }

    } else {
      $ajax_response['message'] = '<span class="error notehide">Must enter a series name!</span>';
      // We're done here
      $json_response = json_encode($ajax_response, JSON_FORCE_OBJECT);
      echo $json_response;
      exit ();
    }
    // Series XXXX
    if (preg_replace('/\s+/', '', $_POST['series_name']) != '') {
      $result = filter_var($_POST['series_name'], FILTER_SANITIZE_STRING); // Remove any HTML tags
      $result = preg_replace('/([0-9]$)+-+([0-9])/','$1–$2',$result); // to en-dash
      $result = str_replace(' -- ',' – ',$result); // to en-dash
      $result = str_replace('---','—',$result); // to em-dash
      $result = str_replace('--','—',$result); // to em-dash
      $result = substr($result, 0, 90); // Limit to 90 characters
      $series_name = $result;
      $series_name_trim = DB::trimspace($series_name);

      $query = $database->prepare("SELECT id FROM series WHERE name=:name AND NOT id=:id");
      $query->bindParam(':name', $series_name_trim);
      $query->bindParam(':id', $s_id);
      $rows = $pdo->exec_($query);
      if ($pdo->numrows > 0) {
        $ajax_response['message'] = '<span class="error notehide">Series name already in use!</span>';
        // We're done here
        $json_response = json_encode($ajax_response, JSON_FORCE_OBJECT);
        echo $json_response;
        exit ();
      }

    } else {
      $ajax_response['message'] = '<span class="error notehide">Must enter a series name!</span>';
      // We're done here
      $json_response = json_encode($ajax_response, JSON_FORCE_OBJECT);
      echo $json_response;
      exit ();
    }
    // Series XXXX
    if (preg_replace('/\s+/', '', $_POST['series_name']) != '') {
      $result = filter_var($_POST['series_name'], FILTER_SANITIZE_STRING); // Remove any HTML tags
      $result = preg_replace('/([0-9]$)+-+([0-9])/','$1–$2',$result); // to en-dash
      $result = str_replace(' -- ',' – ',$result); // to en-dash
      $result = str_replace('---','—',$result); // to em-dash
      $result = str_replace('--','—',$result); // to em-dash
      $result = substr($result, 0, 90); // Limit to 90 characters
      $series_name = $result;
      $series_name_trim = DB::trimspace($series_name);

      $query = $database->prepare("SELECT id FROM series WHERE name=:name AND NOT id=:id");
      $query->bindParam(':name', $series_name_trim);
      $query->bindParam(':id', $s_id);
      $rows = $pdo->exec_($query);
      if ($pdo->numrows > 0) {
        $ajax_response['message'] = '<span class="error notehide">Series name already in use!</span>';
        // We're done here
        $json_response = json_encode($ajax_response, JSON_FORCE_OBJECT);
        echo $json_response;
        exit ();
      }

    } else {
      $ajax_response['message'] = '<span class="error notehide">Must enter a series name!</span>';
      // We're done here
      $json_response = json_encode($ajax_response, JSON_FORCE_OBJECT);
      echo $json_response;
      exit ();
    }
    // Series XXXX
    if (preg_replace('/\s+/', '', $_POST['series_name']) != '') {
      $result = filter_var($_POST['series_name'], FILTER_SANITIZE_STRING); // Remove any HTML tags
      $result = preg_replace('/([0-9]$)+-+([0-9])/','$1–$2',$result); // to en-dash
      $result = str_replace(' -- ',' – ',$result); // to en-dash
      $result = str_replace('---','—',$result); // to em-dash
      $result = str_replace('--','—',$result); // to em-dash
      $result = substr($result, 0, 90); // Limit to 90 characters
      $series_name = $result;
      $series_name_trim = DB::trimspace($series_name);

      $query = $database->prepare("SELECT id FROM series WHERE name=:name AND NOT id=:id");
      $query->bindParam(':name', $series_name_trim);
      $query->bindParam(':id', $s_id);
      $rows = $pdo->exec_($query);
      if ($pdo->numrows > 0) {
        $ajax_response['message'] = '<span class="error notehide">Series name already in use!</span>';
        // We're done here
        $json_response = json_encode($ajax_response, JSON_FORCE_OBJECT);
        echo $json_response;
        exit ();
      }

    } else {
      $ajax_response['message'] = '<span class="error notehide">Must enter a series name!</span>';
      // We're done here
      $json_response = json_encode($ajax_response, JSON_FORCE_OBJECT);
      echo $json_response;
      exit ();
    }
    // Series XXXX
    if (preg_replace('/\s+/', '', $_POST['series_name']) != '') {
      $result = filter_var($_POST['series_name'], FILTER_SANITIZE_STRING); // Remove any HTML tags
      $result = preg_replace('/([0-9]$)+-+([0-9])/','$1–$2',$result); // to en-dash
      $result = str_replace(' -- ',' – ',$result); // to en-dash
      $result = str_replace('---','—',$result); // to em-dash
      $result = str_replace('--','—',$result); // to em-dash
      $result = substr($result, 0, 90); // Limit to 90 characters
      $series_name = $result;
      $series_name_trim = DB::trimspace($series_name);

      $query = $database->prepare("SELECT id FROM series WHERE name=:name AND NOT id=:id");
      $query->bindParam(':name', $series_name_trim);
      $query->bindParam(':id', $s_id);
      $rows = $pdo->exec_($query);
      if ($pdo->numrows > 0) {
        $ajax_response['message'] = '<span class="error notehide">Series name already in use!</span>';
        // We're done here
        $json_response = json_encode($ajax_response, JSON_FORCE_OBJECT);
        echo $json_response;
        exit ();
      }

    } else {
      $ajax_response['message'] = '<span class="error notehide">Must enter a series name!</span>';
      // We're done here
      $json_response = json_encode($ajax_response, JSON_FORCE_OBJECT);
      echo $json_response;
      exit ();
    }

    // SQL
    $query = $database->prepare("UPDATE series SET name=:name, slug=:slug WHERE id=:id");
    $query->bindParam(':name', $series_name_trim);
    $query->bindParam(':slug', $clean_slug_trim);
    $query->bindParam(':id', $s_id);
    $pdo->exec_($query);
    if ($pdo->change) { // Successful change
      $ajax_response['message'] = '<span class="green notehide">Saved, refresh to see changes in Series lists</span>';
      $ajax_response['name'] = $series_name_trim;
      $ajax_response['slug'] = $clean_slug_trim;
      $ajax_response['change'] = 'change';
    } else { // No changes
      // Images?
      if ($upload_img_success == true) {
        $ajax_response['message'] = '<span class="green notehide">Image uploaded. Changes may not take effect until cache reloads.</span>';
        $ajax_response['name'] = $series_name_trim;
        $ajax_response['slug'] = $clean_slug_trim;
        $ajax_response['change'] = 'change';
        $ajax_response['upload'] = 'uploaded';
        $ajax_response['new_podcast'] = ($upload_podcast_success) ? 'newpodcast' : 'notnew';
        $ajax_response['new_rss'] = ($upload_rss_success) ? 'newrss' : 'notnew';
      // Deleting something?
      } else {
        // Delete the images?
        if (($delete_rss_image == true) && ($delete_podcast_image == true)) {
           unlink($pro_rss_path);
           unlink($pro_podcast_path);
           $ajax_response['message'] = '<span class="red notehide">Images deleted.</span>';
           $ajax_response['name'] = $series_name_trim;
           $ajax_response['slug'] = $clean_slug_trim;
           $ajax_response['change'] = 'nochange';
           $ajax_response['upload'] = 'delete';
           $ajax_response['new_podcast'] = 'notnew';
           $ajax_response['new_rss'] = 'notnew';
        // Delete series?
        } elseif ((isset($_POST['series-delete'])) && ($_POST['series-delete'] == 'delete')) {
          $queryp = $database->prepare("UPDATE pieces SET series=:newseries WHERE series=:oldseries");
          $queryp->bindParam(':newseries', $blog_default_series);
          $queryp->bindParam(':oldseries', $s_id);
          $pdo->exec_($queryp);
          $delokp = ($pdo->ok) ? true : false;
          $queryb = $database->prepare("UPDATE publications SET series=:newseries WHERE series=:oldseries");
          $queryb->bindParam(':newseries', $blog_default_series);
          $queryb->bindParam(':oldseries', $s_id);
          $pdo->exec_($queryb);
          $delokb = ($pdo->ok) ? true : false;
          $queryh = $database->prepare("UPDATE publication_history SET series=:newseries WHERE series=:oldseries");
          $queryh->bindParam(':newseries', $blog_default_series);
          $queryh->bindParam(':oldseries', $s_id);
          $pdo->exec_($queryh);
          $delokh = ($pdo->ok) ? true : false;
          if ($delokp && $delokb && $delokh) {
            $pdo->delete('series', 'id', $s_id);
            if ($pdo->ok) { // Series deleted
              $ajax_response['message'] = '<span class="red">Series deleted!</span>';
              $ajax_response['name'] = $series_name_trim;
              $ajax_response['slug'] = $clean_slug_trim;
              $ajax_response['change'] = 'delete';
              $ajax_response['upload'] = 'none';
              $ajax_response['new_podcast'] = 'notnew';
              $ajax_response['new_rss'] = 'notnew';
            } else { // Impossible fail to delete series
              $ajax_response['message'] = '<span class="error">Strange database trouble deleting series!</span>';
              $ajax_response['name'] = $series_name_trim;
              $ajax_response['slug'] = $clean_slug_trim;
              $ajax_response['change'] = 'nochange';
              $ajax_response['upload'] = 'none';
              $ajax_response['new_podcast'] = 'notnew';
              $ajax_response['new_rss'] = 'notnew';
            }
          } else { // Impossible fail to change affected pices to default series
            $ajax_response['message'] = '<span class="error">Strange database trouble preparing to delete series!</span>';
            $ajax_response['name'] = $series_name_trim;
            $ajax_response['slug'] = $clean_slug_trim;
            $ajax_response['change'] = 'nochange';
            $ajax_response['upload'] = 'none';
            $ajax_response['new_podcast'] = 'notnew';
            $ajax_response['new_rss'] = 'notnew';
          }
        } else { // Truly no changes at all
          $ajax_response['message'] = '<span class="orange notehide">No changes.</span>';
          $ajax_response['name'] = $series_name_trim;
          $ajax_response['slug'] = $clean_slug_trim;
          $ajax_response['change'] = 'nochange';
          $ajax_response['upload'] = 'none';
          $ajax_response['new_podcast'] = 'notnew';
          $ajax_response['new_rss'] = 'notnew';
        } // No changes
      } // Delete check
    } // Changes check

    // We're done here
    $json_response = json_encode($ajax_response, JSON_FORCE_OBJECT);
    echo $json_response;

  // Just getting the <form> from seriesEditor AJAX loader
} elseif ((isset($_POST['s_id'])) && (filter_var($_POST['s_id'], FILTER_VALIDATE_INT))) {

    $s_id = preg_replace("/[^0-9]/"," ", $_POST['s_id']);

    echo '<div id="series-details-container">';
    echo '<h2>Series Details</h2>';

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
        $series_credit = $row->series_credit;
        //XXXX Left off here
        //Assign values
        //Finish <form id="series-details-'.$series_id.'"
        //id= for form is correct
        //probably remove <table> elements
        //add more fields
        //check each field with "Series XXXX" above

        // File paths
        $pro_rss_path = $pro_path.$series_id.'-'.$pro_rss_name;
        $pro_podcast_path = $pro_path.$series_id.'-'.$pro_podcast_name;
        // Contents
        echo '
        <form id="series-details-'.$series_id.'" enctype="multipart/form-data">
        <input type="hidden" name="u_id" value="'.$user_id.'">
        <input type="hidden" name="s_id" value="'.$series_id.'">
        <button type="button" onclick="detailsSave('.$series_id.');">Save</button>&nbsp;
        <button type="button" onclick="seriesEditor('.$u_id.');">Cancel</button>
        </form>
        <br><br>
        <table class="contentlib"><tbody>
        <tr><td>
          <label for="input-name">Name: </label><br><br><input type="text" form="series-details-'.$series_id.'" id="input-name" name="series_name" value="'.$series_name.'">
        </td><td>
          <label for="input-slug">Slug: </label><br><br><input type="text" form="series-details-'.$series_id.'" id="input-slug" name="series_slug" value="'.$series_slug.'">
        </td><td class="settings-pro-image" id="pro-rss-upload">';
          // RSS feed
          echo '<b>RSS</b> <small>(JPEG 144x144)</small> <input type="file" name="pro-rss" id="pro-rss" form="series-edit-'.$series_id.'"><br><br>';
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
          echo '<b>Podcast</b> <small>(JPEG 3000x3000)</small> <input type="file" name="pro-podcast" id="pro-podcast" form="series-edit-'.$series_id.'"><br><br>';
          if (file_exists($pro_podcast_path)) {
            echo '<img id="podcast-image-'.$series_id.'" style="max-width:50px; max-height:50px; display:inline;" src="'.$pro_podcast_path.'">';
            echo '<code id="podcast-none-'.$series_id.'" style="max-width:50px; max-height:50px; display:none;" class="gray"><i>no image</i></code>';
          } else {
            echo '<img id="podcast-image-'.$series_id.'" style="max-width:50px; max-height:50px; display:none;" src="'.$pro_podcast_path.'">';
            echo '<code id="podcast-none-'.$series_id.'" style="max-width:50px; max-height:50px; display:inline;" class="gray"><i>no image</i></code>';
          }
        echo '</td></tr><tr>';

        echo '
        <td>
          <label for="input-name">Author: </label><br><br><input type="text" form="series-details-'.$series_id.'" id="input-author" name="series_author" value="'.$series_author.'">
        </td>
        <td>
          <label for="input-name">Owner: </label><br><br><input type="text" form="series-details-'.$series_id.'" id="input-owner" name="series_owner" value="'.$series_owner.'">
        </td>
        <td>
          <label for="input-name">Email: </label><br><br><input type="email" form="series-details-'.$series_id.'" id="input-email" name="series_email" value="'.$series_email.'">
        </td>
        <td>
          <label for="input-name">URL Link: </label><br><br><input type="url" form="series-details-'.$series_id.'" id="input-link" name="series_link" value="'.$series_link.'">
        </td>
        </tr>';

        echo '
        <tr>
        <td>';

        // Categories
        echo '<label for="input-cat-1">Category 1: </label><br><br>
        <select id="input-cat-1" name="series_cat1" form="series-details-'.$series_id.'">';
        iTunesCat($series_cat1);
        echo '</select><br><br>';
        echo '<label for="input-cat-2">Category 2: </label><br><br>
        <select id="input-cat-2" name="series_cat2" form="series-details-'.$series_id.'">';
        iTunesCat($series_cat2);
        echo '</select><br><br>';
        echo '<label for="input-cat-3">Category 3: </label><br><br>
        <select id="input-cat-3" name="series_cat3" form="series-details-'.$series_id.'">';
        iTunesCat($series_cat3);
        echo '</select><br><br>';
        echo '<label for="input-cat-4">Category 4: </label><br><br>
        <select id="input-cat-4" name="series_cat4" form="series-details-'.$series_id.'">';
        iTunesCat($series_cat4);
        echo '</select><br><br>';
        echo '<label for="input-cat-5">Category 5: </label><br><br>
        <select id="input-cat-5" name="series_cat5" form="series-details-'.$series_id.'">';
        iTunesCat($series_cat5);
        echo '</select>
        </td>';

        // Language
        echo '
        <td>
          <label for="input-lang">Language: </label><br><br>
          <select id="input-lang" name="series_lang">
            <option value="af"'; echo ($series_lang == "af") ? ' selected' : ''; echo '>Afrikaans</option>
            <option value="sq"'; echo ($series_lang == "sq") ? ' selected' : ''; echo '>Albanian</option>
            <option value="ar"'; echo ($series_lang == "ar") ? ' selected' : ''; echo '>Arabic</option>
            <option value="en"'; echo ($series_lang == "en") ? ' selected' : ''; echo '>English</option>
            <option value="bn"'; echo ($series_lang == "bn") ? ' selected' : ''; echo '>Bengali</option>
            <option value="cs"'; echo ($series_lang == "cs") ? ' selected' : ''; echo '>Czech</option>
            <option value="zh"'; echo ($series_lang == "zh") ? ' selected' : ''; echo '>Chinese</option>
            <option value="nl"'; echo ($series_lang == "nl") ? ' selected' : ''; echo '>Dutch</option>
            <option value="en"'; echo ($series_lang == "en") ? ' selected' : ''; echo '>English</option>
            <option value="fr"'; echo ($series_lang == "fr") ? ' selected' : ''; echo '>French</option>
            <option value="ka"'; echo ($series_lang == "ka") ? ' selected' : ''; echo '>Georgian</option>
            <option value="de"'; echo ($series_lang == "de") ? ' selected' : ''; echo '>German</option>
            <option value="el"'; echo ($series_lang == "el") ? ' selected' : ''; echo '>Greek</option>
            <option value="gu"'; echo ($series_lang == "gu") ? ' selected' : ''; echo '>Gujarati</option>
            <option value="ha"'; echo ($series_lang == "ha") ? ' selected' : ''; echo '>Hausa</option>
            <option value="he"'; echo ($series_lang == "he") ? ' selected' : ''; echo '>Hebrew</option>
            <option value="hi"'; echo ($series_lang == "hi") ? ' selected' : ''; echo '>Hindi</option>
            <option value="ga"'; echo ($series_lang == "ga") ? ' selected' : ''; echo '>Irish</option>
            <option value="id"'; echo ($series_lang == "id") ? ' selected' : ''; echo '>Indonesian</option>
            <option value="it"'; echo ($series_lang == "it") ? ' selected' : ''; echo '>Italian</option>
            <option value="ja"'; echo ($series_lang == "ja") ? ' selected' : ''; echo '>Japanese</option>
            <option value="jv"'; echo ($series_lang == "jv") ? ' selected' : ''; echo '>Javanese</option>
            <option value="ko"'; echo ($series_lang == "ko") ? ' selected' : ''; echo '>Korean</option>
            <option value="ml"'; echo ($series_lang == "ml") ? ' selected' : ''; echo '>Malay</option>
            <option value="mr"'; echo ($series_lang == "mr") ? ' selected' : ''; echo '>Marathi</option>
            <option value="nn"'; echo ($series_lang == "nn") ? ' selected' : ''; echo '>Norwegian</option>
            <option value="fa"'; echo ($series_lang == "fa") ? ' selected' : ''; echo '>Persian</option>
            <option value="pl"'; echo ($series_lang == "pl") ? ' selected' : ''; echo '>Polish</option>
            <option value="pt"'; echo ($series_lang == "pt") ? ' selected' : ''; echo '>Portuguese</option>
            <option value="pa"'; echo ($series_lang == "pa") ? ' selected' : ''; echo '>Punjabi</option>
            <option value="ro"'; echo ($series_lang == "ro") ? ' selected' : ''; echo '>Romanian</option>
            <option value="ru"'; echo ($series_lang == "ru") ? ' selected' : ''; echo '>Russian</option>
            <option value="sm"'; echo ($series_lang == "sm") ? ' selected' : ''; echo '>Samoan</option>
            <option value="sd"'; echo ($series_lang == "sd") ? ' selected' : ''; echo '>Sindhi</option>
            <option value="es"'; echo ($series_lang == "es") ? ' selected' : ''; echo '>Spanish</option>
            <option value="su"'; echo ($series_lang == "su") ? ' selected' : ''; echo '>Sundanese</option>
            <option value="sw"'; echo ($series_lang == "sw") ? ' selected' : ''; echo '>Swahili</option>
            <option value="ty"'; echo ($series_lang == "ty") ? ' selected' : ''; echo '>Tahitian</option>
            <option value="ta"'; echo ($series_lang == "ta") ? ' selected' : ''; echo '>Tamil</option>
            <option value="te"'; echo ($series_lang == "te") ? ' selected' : ''; echo '>Telugu</option>
            <option value="bo"'; echo ($series_lang == "bo") ? ' selected' : ''; echo '>Tibetan</option>
            <option value="th"'; echo ($series_lang == "th") ? ' selected' : ''; echo '>Thai</option>
            <option value="sk"'; echo ($series_lang == "sk") ? ' selected' : ''; echo '>Slovak</option>
            <option value="sv"'; echo ($series_lang == "sv") ? ' selected' : ''; echo '>Swedish</option>
            <option value="uk"'; echo ($series_lang == "uk") ? ' selected' : ''; echo '>Ukrainian</option>
            <option value="ur"'; echo ($series_lang == "ur") ? ' selected' : ''; echo '>Urdu</option>
            <option value="ug"'; echo ($series_lang == "ug") ? ' selected' : ''; echo '>Uyghur</option>
            <option value="vi"'; echo ($series_lang == "vi") ? ' selected' : ''; echo '>Vietnamese</option>
            <option value="yo"'; echo ($series_lang == "yo") ? ' selected' : ''; echo '>Yoruba</option>
            <option value="zu"'; echo ($series_lang == "zu") ? ' selected' : ''; echo '>Zulu</option>
          </select>
        <br><br>
          <label for="input-name">Keywords: </label><br><br><input type="text" form="series-details-'.$series_id.'" id="input-keywords" name="series_keywords" value="'.$series_keywords.'">
        <br><br>
          <label for="input-name">Copyright line: </label><br><br><input type="text" form="series-details-'.$series_id.'" id="input-copy" name="series_copy" value="'.$series_copy.'">
        <br><br>
          <label for="input-name">Credit: </label><br><br><input type="text" form="series-details-'.$series_id.'" id="input-credit" name="series_credit" value="'.$series_credit.'">
        <br><br>
        </td>';

        // Description & Summary
        echo
        '<td>
          <label for="input-descr">Description: </label><br><br>
          <textarea id="input-descr" name="series_descr" rows="4" cols="50">'.$series_descr.'</textarea>
        </td>
        <td>
          <label for="input-summary">Summary: </label><br><br>
          <textarea id="input-summary" name="series_summary" rows="4" cols="50">'.$series_summary.'</textarea>

        </td>
        </tr>';

        echo '
        </td>

        </tr>
        ';
        echo '</tbody></table>';

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
