<?php

// Include our config (with SQL) up near the top of our PHP file
include ('./in.db.php');

// Include our functions
include ('./in.functions.php');

// Include our login cluster
$head_title = "Blog Settings"; // Set a <title> name used next
$edit_page_yn = false; // Include JavaScript for TinyMCE?
$blog_editor_yn = true; // Series editor
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

// Function for iTunes categories
function iTunesCat($blog_cat) {
  $cat = 'None'; $val = ''; echo '<option value="'.$val.'"'; echo ($blog_cat == $val) ? ' selected' : ''; echo '>'.$cat.'</option>';
  $cat = 'Arts'; $val = 'Arts'; echo '<option value="'.$val.'"'; echo ($blog_cat == $val) ? ' selected' : ''; echo '>'.$cat.'</option>';
  $cat = '- Books'; $val = 'Arts::Books'; echo '<option value="'.$val.'"'; echo ($blog_cat == $val) ? ' selected' : ''; echo '>'.$cat.'</option>';
  $cat = '- Design'; $val = 'Arts::Design'; echo '<option value="'.$val.'"'; echo ($blog_cat == $val) ? ' selected' : ''; echo '>'.$cat.'</option>';
  $cat = '- Fashion &amp; Beauty'; $val = 'Arts::Fashion &amp; Beauty'; echo '<option value="'.$val.'"'; echo ($blog_cat == $val) ? ' selected' : ''; echo '>'.$cat.'</option>';
  $cat = '- Food'; $val = 'Arts::Food'; echo '<option value="'.$val.'"'; echo ($blog_cat == $val) ? ' selected' : ''; echo '>'.$cat.'</option>';
  $cat = '- Performing Arts'; $val = 'Arts::Performing Arts'; echo '<option value="'.$val.'"'; echo ($blog_cat == $val) ? ' selected' : ''; echo '>'.$cat.'</option>';
  $cat = '- Visual Arts'; $val = 'Arts::Visual Arts'; echo '<option value="'.$val.'"'; echo ($blog_cat == $val) ? ' selected' : ''; echo '>'.$cat.'</option>';
  $cat = 'Business'; $val = 'Business'; echo '<option value="'.$val.'"'; echo ($blog_cat == $val) ? ' selected' : ''; echo '>'.$cat.'</option>';
  $cat = '- Careers'; $val = 'Business::Careers'; echo '<option value="'.$val.'"'; echo ($blog_cat == $val) ? ' selected' : ''; echo '>'.$cat.'</option>';
  $cat = '- Entrepreneurship'; $val = 'Business::Entrepreneurship'; echo '<option value="'.$val.'"'; echo ($blog_cat == $val) ? ' selected' : ''; echo '>'.$cat.'</option>';
  $cat = '- Investing'; $val = 'Business::Investing'; echo '<option value="'.$val.'"'; echo ($blog_cat == $val) ? ' selected' : ''; echo '>'.$cat.'</option>';
  $cat = '- Management'; $val = 'Business::Management'; echo '<option value="'.$val.'"'; echo ($blog_cat == $val) ? ' selected' : ''; echo '>'.$cat.'</option>';
  $cat = '- Marketing'; $val = 'Business::Marketing'; echo '<option value="'.$val.'"'; echo ($blog_cat == $val) ? ' selected' : ''; echo '>'.$cat.'</option>';
  $cat = '- Non-Profit'; $val = 'Business::Non-Profit'; echo '<option value="'.$val.'"'; echo ($blog_cat == $val) ? ' selected' : ''; echo '>'.$cat.'</option>';
  $cat = 'Comedy'; $val = 'Comedy'; echo '<option value="'.$val.'"'; echo ($blog_cat == $val) ? ' selected' : ''; echo '>'.$cat.'</option>';
  $cat = '- Comedy Interviews'; $val = 'Comedy::Comedy Interviews'; echo '<option value="'.$val.'"'; echo ($blog_cat == $val) ? ' selected' : ''; echo '>'.$cat.'</option>';
  $cat = '- Improv'; $val = 'Comedy::Improv'; echo '<option value="'.$val.'"'; echo ($blog_cat == $val) ? ' selected' : ''; echo '>'.$cat.'</option>';
  $cat = '- Stand-Up'; $val = 'Comedy::Stand-Up'; echo '<option value="'.$val.'"'; echo ($blog_cat == $val) ? ' selected' : ''; echo '>'.$cat.'</option>';
  $cat = 'Education'; $val = 'Education'; echo '<option value="'.$val.'"'; echo ($blog_cat == $val) ? ' selected' : ''; echo '>'.$cat.'</option>';
  $cat = '- Courses'; $val = 'Education::Courses'; echo '<option value="'.$val.'"'; echo ($blog_cat == $val) ? ' selected' : ''; echo '>'.$cat.'</option>';
  $cat = '- How To'; $val = 'Education::How To'; echo '<option value="'.$val.'"'; echo ($blog_cat == $val) ? ' selected' : ''; echo '>'.$cat.'</option>';
  $cat = '- Language Learning'; $val = 'Education::Language Learning'; echo '<option value="'.$val.'"'; echo ($blog_cat == $val) ? ' selected' : ''; echo '>'.$cat.'</option>';
  $cat = '- Self-Improvement'; $val = 'Education::Self-Improvement'; echo '<option value="'.$val.'"'; echo ($blog_cat == $val) ? ' selected' : ''; echo '>'.$cat.'</option>';
  $cat = 'Fiction'; $val = 'Fiction'; echo '<option value="'.$val.'"'; echo ($blog_cat == $val) ? ' selected' : ''; echo '>'.$cat.'</option>';
  $cat = '- Comedy Fiction'; $val = 'Fiction::Comedy Fiction'; echo '<option value="'.$val.'"'; echo ($blog_cat == $val) ? ' selected' : ''; echo '>'.$cat.'</option>';
  $cat = '- Drama'; $val = 'Fiction::Drama'; echo '<option value="'.$val.'"'; echo ($blog_cat == $val) ? ' selected' : ''; echo '>'.$cat.'</option>';
  $cat = '- Science Fiction'; $val = 'Fiction::Science Fiction'; echo '<option value="'.$val.'"'; echo ($blog_cat == $val) ? ' selected' : ''; echo '>'.$cat.'</option>';
  $cat = 'Government'; $val = 'Government'; echo '<option value="'.$val.'"'; echo ($blog_cat == $val) ? ' selected' : ''; echo '>'.$cat.'</option>';
  $cat = 'History'; $val = 'History'; echo '<option value="'.$val.'"'; echo ($blog_cat == $val) ? ' selected' : ''; echo '>'.$cat.'</option>';
  $cat = 'Health &amp; Fitness'; $val = 'Health &amp; Fitness'; echo '<option value="'.$val.'"'; echo ($blog_cat == $val) ? ' selected' : ''; echo '>'.$cat.'</option>';
  $cat = '- Alternative Health'; $val = 'Health &amp; Fitness::Alternative Health'; echo '<option value="'.$val.'"'; echo ($blog_cat == $val) ? ' selected' : ''; echo '>'.$cat.'</option>';
  $cat = '- Fitness'; $val = 'Health &amp; Fitness::Fitness'; echo '<option value="'.$val.'"'; echo ($blog_cat == $val) ? ' selected' : ''; echo '>'.$cat.'</option>';
  $cat = '- Medicine'; $val = 'Health &amp; Fitness::Medicine'; echo '<option value="'.$val.'"'; echo ($blog_cat == $val) ? ' selected' : ''; echo '>'.$cat.'</option>';
  $cat = '- Mental Health'; $val = 'Health &amp; Fitness::Mental Health'; echo '<option value="'.$val.'"'; echo ($blog_cat == $val) ? ' selected' : ''; echo '>'.$cat.'</option>';
  $cat = '- Nutrition'; $val = 'Health &amp; Fitness::Nutrition'; echo '<option value="'.$val.'"'; echo ($blog_cat == $val) ? ' selected' : ''; echo '>'.$cat.'</option>';
  $cat = '- Sexuality'; $val = 'Health &amp; Fitness::Sexuality'; echo '<option value="'.$val.'"'; echo ($blog_cat == $val) ? ' selected' : ''; echo '>'.$cat.'</option>';
  $cat = 'Kids &amp; Family'; $val = 'Kids &amp; Family'; echo '<option value="'.$val.'"'; echo ($blog_cat == $val) ? ' selected' : ''; echo '>'.$cat.'</option>';
  $cat = '- Education for Kids'; $val = 'Kids &amp; Family::Education for Kids'; echo '<option value="'.$val.'"'; echo ($blog_cat == $val) ? ' selected' : ''; echo '>'.$cat.'</option>';
  $cat = '- Parenting'; $val = 'Kids &amp; Family::Parenting'; echo '<option value="'.$val.'"'; echo ($blog_cat == $val) ? ' selected' : ''; echo '>'.$cat.'</option>';
  $cat = '- Pets &amp; Animals'; $val = 'Kids &amp; Family::Pets &amp; Animals'; echo '<option value="'.$val.'"'; echo ($blog_cat == $val) ? ' selected' : ''; echo '>'.$cat.'</option>';
  $cat = '- Stories for Kids'; $val = 'Kids &amp; Family::Stories for Kids'; echo '<option value="'.$val.'"'; echo ($blog_cat == $val) ? ' selected' : ''; echo '>'.$cat.'</option>';
  $cat = 'Leisure'; $val = 'Leisure'; echo '<option value="'.$val.'"'; echo ($blog_cat == $val) ? ' selected' : ''; echo '>'.$cat.'</option>';
  $cat = '- Animation &amp; Manga'; $val = 'Leisure::Animation &amp; Manga'; echo '<option value="'.$val.'"'; echo ($blog_cat == $val) ? ' selected' : ''; echo '>'.$cat.'</option>';
  $cat = '- Automotive'; $val = 'Leisure::Automotive'; echo '<option value="'.$val.'"'; echo ($blog_cat == $val) ? ' selected' : ''; echo '>'.$cat.'</option>';
  $cat = '- Aviation'; $val = 'Leisure::Aviation'; echo '<option value="'.$val.'"'; echo ($blog_cat == $val) ? ' selected' : ''; echo '>'.$cat.'</option>';
  $cat = '- Crafts'; $val = 'Leisure::Crafts'; echo '<option value="'.$val.'"'; echo ($blog_cat == $val) ? ' selected' : ''; echo '>'.$cat.'</option>';
  $cat = '- Games'; $val = 'Leisure::Games'; echo '<option value="'.$val.'"'; echo ($blog_cat == $val) ? ' selected' : ''; echo '>'.$cat.'</option>';
  $cat = '- Hobbies'; $val = 'Leisure::Hobbies'; echo '<option value="'.$val.'"'; echo ($blog_cat == $val) ? ' selected' : ''; echo '>'.$cat.'</option>';
  $cat = '- Home &amp; Garden'; $val = 'Leisure::Home &amp; Garden'; echo '<option value="'.$val.'"'; echo ($blog_cat == $val) ? ' selected' : ''; echo '>'.$cat.'</option>';
  $cat = '- Video Games'; $val = 'Leisure::Video Games'; echo '<option value="'.$val.'"'; echo ($blog_cat == $val) ? ' selected' : ''; echo '>'.$cat.'</option>';
  $cat = 'Music'; $val = 'Music'; echo '<option value="'.$val.'"'; echo ($blog_cat == $val) ? ' selected' : ''; echo '>'.$cat.'</option>';
  $cat = '- Music Commentary'; $val = 'Music::Music Commentary'; echo '<option value="'.$val.'"'; echo ($blog_cat == $val) ? ' selected' : ''; echo '>'.$cat.'</option>';
  $cat = '- Music History'; $val = 'Music::Music History'; echo '<option value="'.$val.'"'; echo ($blog_cat == $val) ? ' selected' : ''; echo '>'.$cat.'</option>';
  $cat = '- Music Interviews'; $val = 'Music::Music Interviews'; echo '<option value="'.$val.'"'; echo ($blog_cat == $val) ? ' selected' : ''; echo '>'.$cat.'</option>';
  $cat = 'News'; $val = 'News'; echo '<option value="'.$val.'"'; echo ($blog_cat == $val) ? ' selected' : ''; echo '>'.$cat.'</option>';
  $cat = '- Business News'; $val = 'News::Business News'; echo '<option value="'.$val.'"'; echo ($blog_cat == $val) ? ' selected' : ''; echo '>'.$cat.'</option>';
  $cat = '- Daily News'; $val = 'News::Daily News'; echo '<option value="'.$val.'"'; echo ($blog_cat == $val) ? ' selected' : ''; echo '>'.$cat.'</option>';
  $cat = '- Entertainment News'; $val = 'News::Entertainment News'; echo '<option value="'.$val.'"'; echo ($blog_cat == $val) ? ' selected' : ''; echo '>'.$cat.'</option>';
  $cat = '- News Commentary'; $val = 'News::News Commentary'; echo '<option value="'.$val.'"'; echo ($blog_cat == $val) ? ' selected' : ''; echo '>'.$cat.'</option>';
  $cat = '- Politics'; $val = 'News::Politics'; echo '<option value="'.$val.'"'; echo ($blog_cat == $val) ? ' selected' : ''; echo '>'.$cat.'</option>';
  $cat = '- Sports News'; $val = 'News::Sports News'; echo '<option value="'.$val.'"'; echo ($blog_cat == $val) ? ' selected' : ''; echo '>'.$cat.'</option>';
  $cat = '- Tech News'; $val = 'News::Tech News'; echo '<option value="'.$val.'"'; echo ($blog_cat == $val) ? ' selected' : ''; echo '>'.$cat.'</option>';
  $cat = 'Religion &amp; Spirituality'; $val = 'Religion &amp; Spirituality'; echo '<option value="'.$val.'"'; echo ($blog_cat == $val) ? ' selected' : ''; echo '>'.$cat.'</option>';
  $cat = '- Buddhism'; $val = 'Religion &amp; Spirituality::Buddhism'; echo '<option value="'.$val.'"'; echo ($blog_cat == $val) ? ' selected' : ''; echo '>'.$cat.'</option>';
  $cat = '- Christianity'; $val = 'Religion &amp; Spirituality::Christianity'; echo '<option value="'.$val.'"'; echo ($blog_cat == $val) ? ' selected' : ''; echo '>'.$cat.'</option>';
  $cat = '- Hinduism'; $val = 'Religion &amp; Spirituality::Hinduism'; echo '<option value="'.$val.'"'; echo ($blog_cat == $val) ? ' selected' : ''; echo '>'.$cat.'</option>';
  $cat = '- Islam'; $val = 'Religion &amp; Spirituality::Islam'; echo '<option value="'.$val.'"'; echo ($blog_cat == $val) ? ' selected' : ''; echo '>'.$cat.'</option>';
  $cat = '- Judiasm'; $val = 'Religion &amp; Spirituality::Judiasm'; echo '<option value="'.$val.'"'; echo ($blog_cat == $val) ? ' selected' : ''; echo '>'.$cat.'</option>';
  $cat = '- Religion'; $val = 'Religion &amp; Spirituality::Religion'; echo '<option value="'.$val.'"'; echo ($blog_cat == $val) ? ' selected' : ''; echo '>'.$cat.'</option>';
  $cat = '- Spirituality'; $val = 'Religion &amp; Spirituality::Spirituality '; echo '<option value="'.$val.'"'; echo ($blog_cat == $val) ? ' selected' : ''; echo '>'.$cat.'</option>';
  $cat = 'Science'; $val = 'Science'; echo '<option value="'.$val.'"'; echo ($blog_cat == $val) ? ' selected' : ''; echo '>'.$cat.'</option>';
  $cat = '- Astronomy'; $val = 'Science::Astronomy'; echo '<option value="'.$val.'"'; echo ($blog_cat == $val) ? ' selected' : ''; echo '>'.$cat.'</option>';
  $cat = '- Chemistry'; $val = 'Science::Chemistry'; echo '<option value="'.$val.'"'; echo ($blog_cat == $val) ? ' selected' : ''; echo '>'.$cat.'</option>';
  $cat = '- Earth Sciences'; $val = 'Science::Earth Sciences'; echo '<option value="'.$val.'"'; echo ($blog_cat == $val) ? ' selected' : ''; echo '>'.$cat.'</option>';
  $cat = '- Life Sciences'; $val = 'Science::Life Sciences'; echo '<option value="'.$val.'"'; echo ($blog_cat == $val) ? ' selected' : ''; echo '>'.$cat.'</option>';
  $cat = '- Mathematics'; $val = 'Science::Mathematics'; echo '<option value="'.$val.'"'; echo ($blog_cat == $val) ? ' selected' : ''; echo '>'.$cat.'</option>';
  $cat = '- Natural Sciences'; $val = 'Science::Natural Sciences'; echo '<option value="'.$val.'"'; echo ($blog_cat == $val) ? ' selected' : ''; echo '>'.$cat.'</option>';
  $cat = '- Nature'; $val = 'Science::Nature'; echo '<option value="'.$val.'"'; echo ($blog_cat == $val) ? ' selected' : ''; echo '>'.$cat.'</option>';
  $cat = '- Physics'; $val = 'Science::Physics'; echo '<option value="'.$val.'"'; echo ($blog_cat == $val) ? ' selected' : ''; echo '>'.$cat.'</option>';
  $cat = '- Social Sciences'; $val = 'Science::Social Sciences'; echo '<option value="'.$val.'"'; echo ($blog_cat == $val) ? ' selected' : ''; echo '>'.$cat.'</option>';
  $cat = 'Society &amp; Culture'; $val = 'Society &amp; Culture'; echo '<option value="'.$val.'"'; echo ($blog_cat == $val) ? ' selected' : ''; echo '>'.$cat.'</option>';
  $cat = '- Documentary'; $val = 'Society &amp; Culture::Documentary'; echo '<option value="'.$val.'"'; echo ($blog_cat == $val) ? ' selected' : ''; echo '>'.$cat.'</option>';
  $cat = '- Personal Journals'; $val = 'Society &amp; Culture::Personal Journals'; echo '<option value="'.$val.'"'; echo ($blog_cat == $val) ? ' selected' : ''; echo '>'.$cat.'</option>';
  $cat = '- Philosophy'; $val = 'Society &amp; Culture::Philosophy'; echo '<option value="'.$val.'"'; echo ($blog_cat == $val) ? ' selected' : ''; echo '>'.$cat.'</option>';
  $cat = '- Places &amp; Travel'; $val = 'Society &amp; Culture::Places &amp; Travel'; echo '<option value="'.$val.'"'; echo ($blog_cat == $val) ? ' selected' : ''; echo '>'.$cat.'</option>';
  $cat = '- Relationships'; $val = 'Society &amp; Culture::Relationships'; echo '<option value="'.$val.'"'; echo ($blog_cat == $val) ? ' selected' : ''; echo '>'.$cat.'</option>';
  $cat = 'Sports'; $val = 'Sports'; echo '<option value="'.$val.'"'; echo ($blog_cat == $val) ? ' selected' : ''; echo '>'.$cat.'</option>';
  $cat = '- Baseball'; $val = 'Sports::Baseball'; echo '<option value="'.$val.'"'; echo ($blog_cat == $val) ? ' selected' : ''; echo '>'.$cat.'</option>';
  $cat = '- Basketball'; $val = 'Sports::Basketball'; echo '<option value="'.$val.'"'; echo ($blog_cat == $val) ? ' selected' : ''; echo '>'.$cat.'</option>';
  $cat = '- Cricket'; $val = 'Sports::Cricket'; echo '<option value="'.$val.'"'; echo ($blog_cat == $val) ? ' selected' : ''; echo '>'.$cat.'</option>';
  $cat = '- Fantasy Sports'; $val = 'Sports::Fantasy Sports'; echo '<option value="'.$val.'"'; echo ($blog_cat == $val) ? ' selected' : ''; echo '>'.$cat.'</option>';
  $cat = '- Football'; $val = 'Sports::Football'; echo '<option value="'.$val.'"'; echo ($blog_cat == $val) ? ' selected' : ''; echo '>'.$cat.'</option>';
  $cat = '- Golf'; $val = 'Sports::Golf'; echo '<option value="'.$val.'"'; echo ($blog_cat == $val) ? ' selected' : ''; echo '>'.$cat.'</option>';
  $cat = '- Hockey'; $val = 'Sports::Hockey'; echo '<option value="'.$val.'"'; echo ($blog_cat == $val) ? ' selected' : ''; echo '>'.$cat.'</option>';
  $cat = '- Rugby'; $val = 'Sports::Rugby'; echo '<option value="'.$val.'"'; echo ($blog_cat == $val) ? ' selected' : ''; echo '>'.$cat.'</option>';
  $cat = '- Soccer'; $val = 'Sports::Soccer'; echo '<option value="'.$val.'"'; echo ($blog_cat == $val) ? ' selected' : ''; echo '>'.$cat.'</option>';
  $cat = '- Swimming'; $val = 'Sports::Swimming'; echo '<option value="'.$val.'"'; echo ($blog_cat == $val) ? ' selected' : ''; echo '>'.$cat.'</option>';
  $cat = '- Tennis'; $val = 'Sports::Tennis'; echo '<option value="'.$val.'"'; echo ($blog_cat == $val) ? ' selected' : ''; echo '>'.$cat.'</option>';
  $cat = '- Volleyball'; $val = 'Sports::Volleyball'; echo '<option value="'.$val.'"'; echo ($blog_cat == $val) ? ' selected' : ''; echo '>'.$cat.'</option>';
  $cat = '- Wilderness'; $val = 'Sports::Wilderness'; echo '<option value="'.$val.'"'; echo ($blog_cat == $val) ? ' selected' : ''; echo '>'.$cat.'</option>';
  $cat = '- Wrestling'; $val = 'Sports::Wrestling'; echo '<option value="'.$val.'"'; echo ($blog_cat == $val) ? ' selected' : ''; echo '>'.$cat.'</option>';
  $cat = 'Technology'; $val = 'Technology'; echo '<option value="'.$val.'"'; echo ($blog_cat == $val) ? ' selected' : ''; echo '>'.$cat.'</option>';
  $cat = 'True Crime'; $val = 'True Crime'; echo '<option value="'.$val.'"'; echo ($blog_cat == $val) ? ' selected' : ''; echo '>'.$cat.'</option>';
  $cat = 'TV &amp; Film'; $val = 'TV &amp; Film'; echo '<option value="'.$val.'"'; echo ($blog_cat == $val) ? ' selected' : ''; echo '>'.$cat.'</option>';
  $cat = '- After Shows'; $val = 'TV &amp; Film::After Shows'; echo '<option value="'.$val.'"'; echo ($blog_cat == $val) ? ' selected' : ''; echo '>'.$cat.'</option>';
  $cat = '- Film History'; $val = 'TV &amp; Film::Film History'; echo '<option value="'.$val.'"'; echo ($blog_cat == $val) ? ' selected' : ''; echo '>'.$cat.'</option>';
  $cat = '- Film Interviews'; $val = 'TV &amp; Film::Film Interviews'; echo '<option value="'.$val.'"'; echo ($blog_cat == $val) ? ' selected' : ''; echo '>'.$cat.'</option>';
  $cat = '- Film Reviews'; $val = 'TV &amp; Film::Film Reviews'; echo '<option value="'.$val.'"'; echo ($blog_cat == $val) ? ' selected' : ''; echo '>'.$cat.'</option>';
  $cat = '- TV Reviews'; $val = 'TV &amp; Film::TV Reviews'; echo '<option value="'.$val.'"'; echo ($blog_cat == $val) ? ' selected' : ''; echo '>'.$cat.'</option>';
}

// POSTed form?
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

  // Include our POST checks
  include ('./in.checks.php');

  // Default Series
  $p_series = (filter_var($_POST['p_series'], FILTER_VALIDATE_INT)) ? filter_var($_POST['p_series'], FILTER_VALIDATE_INT) : false;

  // Podcast Details
  if ( (isset($_POST['blog_lang'])) // Double checking, redundant
    && (isset($_POST['blog_link']))
    && (isset($_POST['blog_author']))
    && (isset($_POST['blog_descr']))
    && (isset($_POST['blog_summary']))
    && (isset($_POST['blog_owner']))
    && (isset($_POST['blog_email']))
    && (isset($_POST['blog_copy']))
    && (isset($_POST['blog_keywords']))
    && (isset($_POST['blog_explicit']))
    && (isset($_POST['blog_cat1']))
    && (isset($_POST['blog_cat2']))
    && (isset($_POST['blog_cat3']))
    && (isset($_POST['blog_cat4']))
    && (isset($_POST['blog_cat5'])) ) {

      // Series language
      if (preg_replace('/\s+/', '', $_POST['blog_lang']) != '') {
        $regex_replace = "/[^a-zA-Z0-9-]/";
        $result = filter_var($_POST['blog_lang'], FILTER_SANITIZE_STRING); // Remove any HTML tags
        $result = strtolower(preg_replace($regex_replace,"-", $result)); // Lowercase, remove non-accepted characters
        $result = substr($result, 0, 90); // Limit to 90 characters
        $blog_lang = $result;
        $blog_lang = DB::trimspace($blog_lang);

      } else {
        $podcast_empty_field_warning = true;
      }
      // Series link
      if (filter_var($_POST['blog_link'], FILTER_VALIDATE_URL)) {
        $result = filter_var($_POST['blog_link'], FILTER_VALIDATE_URL); // Real URL
        $blog_link = $result;
        $blog_link = DB::trimspace($blog_link);

      } else {
        $podcast_empty_field_warning = true;
      }
      // Series author
      if (preg_replace('/\s+/', '', $_POST['blog_author']) != '') {
        $result = filter_var($_POST['blog_author'], FILTER_SANITIZE_STRING); // Remove any HTML tags
        $result = substr($result, 0, 255); // Limit to 255 characters
        $blog_author = $result;
        $blog_author = DB::trimspace($blog_author);

      } else {
        $podcast_empty_field_warning = true;
      }
      // Series description
      if (preg_replace('/\s+/', '', $_POST['blog_descr']) != '') {
        $result = filter_var($_POST['blog_descr'], FILTER_SANITIZE_STRING); // Remove any HTML tags
        $result = substr($result, 0, 255); // Limit to 255 characters
        $blog_descr = $result;
        $blog_descr = DB::trimspace($blog_descr);

      } else {
        $podcast_empty_field_warning = true;
      }
      // Series summary
      if (preg_replace('/\s+/', '', $_POST['blog_summary']) != '') {
        $result = filter_var($_POST['blog_summary'], FILTER_SANITIZE_STRING); // Remove any HTML tags
        $result = substr($result, 0, 255); // Limit to 255 characters
        $blog_summary = $result;
        $blog_summary = DB::trimspace($blog_summary);

      } else {
        $podcast_empty_field_warning = true;
      }
      // Series owner
      if (preg_replace('/\s+/', '', $_POST['blog_owner']) != '') {
        $result = filter_var($_POST['blog_owner'], FILTER_SANITIZE_STRING); // Remove any HTML tags
        $result = substr($result, 0, 255); // Limit to 255 characters
        $blog_owner = $result;
        $blog_owner = DB::trimspace($blog_owner);

      } else {
        $podcast_empty_field_warning = true;
      }
      // Series email
      if (filter_var($_POST['blog_email'], FILTER_VALIDATE_EMAIL)) {
        $result = filter_var($_POST['blog_email'], FILTER_VALIDATE_EMAIL); // Real email
        $result = substr($result, 0, 255); // Limit to 255 characters
        $blog_email = $result;
        $blog_email = DB::trimspace($blog_email);

      } else {
        $podcast_empty_field_warning = true;
      }
      // Series keywords
      if (preg_replace('/\s+/', '', $_POST['blog_keywords']) != '') {
        $regex_replace = "/[^a-zA-Z0-9-, ]/";
        $result = filter_var($_POST['blog_keywords'], FILTER_SANITIZE_STRING); // Remove any HTML tags
        $result = preg_replace($regex_replace,"", $result); // Remove non-accepted characters
        $result = substr($result, 0, 255); // Limit to 255 characters
        $blog_keywords = $result;
        $blog_keywords = DB::trimspace($blog_keywords);

      } else {
        $podcast_empty_field_warning = true;
      }
      // Series explicit
      if ((preg_replace('/\s+/', '', $_POST['blog_explicit']) != '')
      && ($_POST['blog_explicit'] == 'true')
      || ($_POST['blog_explicit'] == 'false')) {
        $regex_replace = "/[^truefals]/";
        $result = filter_var($_POST['blog_explicit'], FILTER_SANITIZE_STRING); // Remove any HTML tags
        $result = preg_replace($regex_replace,"", $result); // Remove non-accepted characters
        $result = substr($result, 0, 5); // Limit to 5 characters
        $blog_explicit = $result;
        $blog_explicit = DB::trimspace($blog_explicit);

      } else {
        $podcast_empty_field_warning = true;
      }
      // Series copyright
      if (preg_replace('/\s+/', '', $_POST['blog_copy']) != '') {
        $result = filter_var($_POST['blog_copy'], FILTER_SANITIZE_STRING); // Remove any HTML tags
        $result = substr($result, 0, 255); // Limit to 255 characters
        $blog_copy = $result;
        $blog_copy = DB::trimspace($blog_copy);

      } else {
        $podcast_empty_field_warning = true;
      }
      // Series category 1
      if (isset($_POST['blog_cat1'])) {
        $regex_replace = "/[^a-zA-Z-&;: ]/";
        $result = filter_var($_POST['blog_cat1'], FILTER_SANITIZE_STRING); // Remove any HTML tags
        $result = preg_replace($regex_replace,"-", $result); // Remove non-accepted characters
        $result = substr($result, 0, 255); // Limit to 255 characters
        $blog_cat1 = $result;
        $blog_cat1 = DB::trimspace($blog_cat1);

      } else {
        $podcast_empty_field_warning = true;
      }
      // Series category 2
      if (isset($_POST['blog_cat2'])) {
        $regex_replace = "/[^a-zA-Z-&;: ]/";
        $result = filter_var($_POST['blog_cat2'], FILTER_SANITIZE_STRING); // Remove any HTML tags
        $result = preg_replace($regex_replace,"-", $result); // Remove non-accepted characters
        $result = substr($result, 0, 255); // Limit to 255 characters
        $blog_cat2 = $result;
        $blog_cat2 = DB::trimspace($blog_cat2);

      } else {
        $podcast_empty_field_warning = true;
      }
      // Series category 3
      if (isset($_POST['blog_cat3'])) {
        $regex_replace = "/[^a-zA-Z-&;: ]/";
        $result = filter_var($_POST['blog_cat3'], FILTER_SANITIZE_STRING); // Remove any HTML tags
        $result = preg_replace($regex_replace,"-", $result); // Remove non-accepted characters
        $result = substr($result, 0, 255); // Limit to 255 characters
        $blog_cat3 = $result;
        $blog_cat3 = DB::trimspace($blog_cat3);

      } else {
        $podcast_empty_field_warning = true;
      }
      // Series category 4
      if (isset($_POST['blog_cat4'])) {
        $regex_replace = "/[^a-zA-Z-&;: ]/";
        $result = filter_var($_POST['blog_cat4'], FILTER_SANITIZE_STRING); // Remove any HTML tags
        $result = preg_replace($regex_replace,"-", $result); // Remove non-accepted characters
        $result = substr($result, 0, 255); // Limit to 255 characters
        $blog_cat4 = $result;
        $blog_cat4 = DB::trimspace($blog_cat4);

      } else {
        $podcast_empty_field_warning = true;
      }
      // Series category 5
      if (isset($_POST['blog_cat5'])) {
        $regex_replace = "/[^a-zA-Z-&;: ]/";
        $result = filter_var($_POST['blog_cat5'], FILTER_SANITIZE_STRING); // Remove any HTML tags
        $result = preg_replace($regex_replace,"-", $result); // Remove non-accepted characters
        $result = substr($result, 0, 255); // Limit to 255 characters
        $blog_cat5 = $result;
        $blog_cat5 = DB::trimspace($blog_cat5);

      } else {
        $podcast_empty_field_warning = true;
      }
      $podcast_empty_field_warning = (isset($podcast_empty_field_warning)) ? " <p class='red'>Empty podcast field(s)!</p>" : false;

      // SQL
      $query = $database->prepare("UPDATE blog_settings SET
        blog_lang=:blog_lang,
        blog_link=:blog_link,
        blog_author=:blog_author,
        blog_descr=:blog_descr,
        blog_summary=:blog_summary,
        blog_owner=:blog_owner,
        blog_email=:blog_email,
        blog_copy=:blog_copy,
        blog_keywords=:blog_keywords,
        blog_explicit=:blog_explicit,
        blog_cat1=:blog_cat1,
        blog_cat2=:blog_cat2,
        blog_cat3=:blog_cat3,
        blog_cat4=:blog_cat4,
        blog_cat5=:blog_cat5");
      $query->bindParam(':blog_lang', $blog_lang);
      $query->bindParam(':blog_link', $blog_link);
      $query->bindParam(':blog_author', $blog_author);
      $query->bindParam(':blog_descr', $blog_descr);
      $query->bindParam(':blog_summary', $blog_summary);
      $query->bindParam(':blog_owner', $blog_owner);
      $query->bindParam(':blog_email', $blog_email);
      $query->bindParam(':blog_copy', $blog_copy);
      $query->bindParam(':blog_keywords', $blog_keywords);
      $query->bindParam(':blog_explicit', $blog_explicit);
      $query->bindParam(':blog_cat1', $blog_cat1);
      $query->bindParam(':blog_cat2', $blog_cat2);
      $query->bindParam(':blog_cat3', $blog_cat3);
      $query->bindParam(':blog_cat4', $blog_cat4);
      $query->bindParam(':blog_cat5', $blog_cat5);
      $pdo->exec_($query);
      if ($pdo->change) { // Successful change
        $blog_details_change = 'change';
      } else { // No changes
        $blog_details_change = 'nochange';
      } // Changes check

    }

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
    if (($pdo->change) || ($blog_details_change = 'change')) {
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
    // Empty fields?
    echo (isset($podcast_empty_field_warning)) ? $podcast_empty_field_warning : false;
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
  echo 'Tagline: '.formInput('blog_tagline', $new_blog_tagline, $check_err).' (1-100 security question)<br><br>';
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
  $series_form = 'blog_settings'; // 'edit_piece' or 'blog_settings'
  include ('./in.series.php');

  echo '</p>';

  // Edit series button
  include ('./in.editseriesbutton.php');

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

// Blog Podcast Details

// Fetch the information from blog_settings
$query = $database->prepare("SELECT
  blog_lang,
  blog_link,
  blog_author,
  blog_descr,
  blog_summary,
  blog_owner,
  blog_email,
  blog_copy,
  blog_keywords,
  blog_explicit,
  blog_cat1,
  blog_cat2,
  blog_cat3,
  blog_cat4,
  blog_cat5
  FROM blog_settings");
$rows = $pdo->exec_($query);
if ($pdo->numrows > 0) {

  foreach ($rows as $row) {
    $blog_lang = $row->blog_lang;
    $blog_link = $row->blog_link;
    $blog_author = $row->blog_author;
    $blog_descr = $row->blog_descr;
    $blog_summary = $row->blog_summary;
    $blog_owner = $row->blog_owner;
    $blog_email = $row->blog_email;
    $blog_keywords = $row->blog_keywords;
    $blog_explicit = $row->blog_explicit;
    $blog_cat1 = $row->blog_cat1;
    $blog_cat2 = $row->blog_cat2;
    $blog_cat3 = $row->blog_cat3;
    $blog_cat4 = $row->blog_cat4;
    $blog_cat5 = $row->blog_cat5;
    $blog_copy = $row->blog_copy;
  }

  // Start the webform
  echo '<h2>Blog Podcast Details</h2>';

  // Three-column table
  echo '<table class="contentlib"><tbody><tr>
  <td>';

  // Categories
  echo '<label for="input-cat-1">Category 1: </label><br><br>
  <select id="input-cat-1" name="blog_cat1" form="blog_settings">';
  iTunesCat($blog_cat1);
  echo '</select><br><br>';
  echo '<label for="input-cat-2">Category 2: </label><br><br>
  <select id="input-cat-2" name="blog_cat2" form="blog_settings">';
  iTunesCat($blog_cat2);
  echo '</select><br><br>';
  echo '<label for="input-cat-3">Category 3: </label><br><br>
  <select id="input-cat-3" name="blog_cat3" form="blog_settings">';
  iTunesCat($blog_cat3);
  echo '</select><br><br>';
  echo '<label for="input-cat-4">Category 4: </label><br><br>
  <select id="input-cat-4" name="blog_cat4" form="blog_settings">';
  iTunesCat($blog_cat4);
  echo '</select><br><br>';
  echo '<label for="input-cat-5">Category 5: </label><br><br>
  <select id="input-cat-5" name="blog_cat5" form="blog_settings">';
  iTunesCat($blog_cat5);
  echo '</select>
  </td>';

  echo '
  <td>
    <label for="input-author">Author: </label><br><br><input type="text" form="blog_settings" id="input-author" name="blog_author" value="'.$blog_author.'">
  <br><br>
    <label for="input-owner">Owner: </label><br><br><input type="text" form="blog_settings" id="input-owner" name="blog_owner" value="'.$blog_owner.'">
  <br><br>
    <label for="input-email">Email: </label><br><br><input type="email" form="blog_settings" id="input-email" name="blog_email" value="'.$blog_email.'">
  <br><br>
    <label for="input-link">URL Link: </label><br><br><input type="url" form="blog_settings" id="input-link" name="blog_link" value="'.$blog_link.'">
  <br><br>
    <label for="input-copy">Copyright line: </label><br><br><input type="text" form="blog_settings" id="input-copy" name="blog_copy" value="'.$blog_copy.'">
    <br><br>
  </td>';

  // Subtitle & Summary
  echo
  '<td>
    <label for="input-descr">Subtitle/Description: </label><br><br>
    <textarea id="input-descr" name="blog_descr" form="blog_settings" rows="4" cols="50">'.$blog_descr.'</textarea>
  <br><br>
    <label for="input-summary">Summary: </label><br><br>
    <textarea id="input-summary" name="blog_summary" form="blog_settings" rows="4" cols="50">'.$blog_summary.'</textarea>
  <br><br>';

  // Keywords & Language
  echo '
      <label for="input-keywords">Keywords: (comma-separated list) </label><br><br><input type="text" form="blog_settings" id="input-keywords" name="blog_keywords" value="'.$blog_keywords.'">
    <br><br>
      <label for="input-lang">Language: </label>
      <select id="input-lang" name="blog_lang" form="blog_settings">
        <option value="af"'; echo ($blog_lang == "af") ? ' selected' : ''; echo '>Afrikaans</option>
        <option value="sq"'; echo ($blog_lang == "sq") ? ' selected' : ''; echo '>Albanian</option>
        <option value="ar"'; echo ($blog_lang == "ar") ? ' selected' : ''; echo '>Arabic</option>
        <option value="en"'; echo ($blog_lang == "en") ? ' selected' : ''; echo '>English</option>
        <option value="bn"'; echo ($blog_lang == "bn") ? ' selected' : ''; echo '>Bengali</option>
        <option value="cs"'; echo ($blog_lang == "cs") ? ' selected' : ''; echo '>Czech</option>
        <option value="zh"'; echo ($blog_lang == "zh") ? ' selected' : ''; echo '>Chinese</option>
        <option value="nl"'; echo ($blog_lang == "nl") ? ' selected' : ''; echo '>Dutch</option>
        <option value="en"'; echo ($blog_lang == "en") ? ' selected' : ''; echo '>English</option>
        <option value="fr"'; echo ($blog_lang == "fr") ? ' selected' : ''; echo '>French</option>
        <option value="ka"'; echo ($blog_lang == "ka") ? ' selected' : ''; echo '>Georgian</option>
        <option value="de"'; echo ($blog_lang == "de") ? ' selected' : ''; echo '>German</option>
        <option value="el"'; echo ($blog_lang == "el") ? ' selected' : ''; echo '>Greek</option>
        <option value="gu"'; echo ($blog_lang == "gu") ? ' selected' : ''; echo '>Gujarati</option>
        <option value="ha"'; echo ($blog_lang == "ha") ? ' selected' : ''; echo '>Hausa</option>
        <option value="he"'; echo ($blog_lang == "he") ? ' selected' : ''; echo '>Hebrew</option>
        <option value="hi"'; echo ($blog_lang == "hi") ? ' selected' : ''; echo '>Hindi</option>
        <option value="ga"'; echo ($blog_lang == "ga") ? ' selected' : ''; echo '>Irish</option>
        <option value="id"'; echo ($blog_lang == "id") ? ' selected' : ''; echo '>Indonesian</option>
        <option value="it"'; echo ($blog_lang == "it") ? ' selected' : ''; echo '>Italian</option>
        <option value="ja"'; echo ($blog_lang == "ja") ? ' selected' : ''; echo '>Japanese</option>
        <option value="jv"'; echo ($blog_lang == "jv") ? ' selected' : ''; echo '>Javanese</option>
        <option value="ko"'; echo ($blog_lang == "ko") ? ' selected' : ''; echo '>Korean</option>
        <option value="ml"'; echo ($blog_lang == "ml") ? ' selected' : ''; echo '>Malay</option>
        <option value="mr"'; echo ($blog_lang == "mr") ? ' selected' : ''; echo '>Marathi</option>
        <option value="nn"'; echo ($blog_lang == "nn") ? ' selected' : ''; echo '>Norwegian</option>
        <option value="fa"'; echo ($blog_lang == "fa") ? ' selected' : ''; echo '>Persian</option>
        <option value="pl"'; echo ($blog_lang == "pl") ? ' selected' : ''; echo '>Polish</option>
        <option value="pt"'; echo ($blog_lang == "pt") ? ' selected' : ''; echo '>Portuguese</option>
        <option value="pa"'; echo ($blog_lang == "pa") ? ' selected' : ''; echo '>Punjabi</option>
        <option value="ro"'; echo ($blog_lang == "ro") ? ' selected' : ''; echo '>Romanian</option>
        <option value="ru"'; echo ($blog_lang == "ru") ? ' selected' : ''; echo '>Russian</option>
        <option value="sm"'; echo ($blog_lang == "sm") ? ' selected' : ''; echo '>Samoan</option>
        <option value="sd"'; echo ($blog_lang == "sd") ? ' selected' : ''; echo '>Sindhi</option>
        <option value="es"'; echo ($blog_lang == "es") ? ' selected' : ''; echo '>Spanish</option>
        <option value="su"'; echo ($blog_lang == "su") ? ' selected' : ''; echo '>Sundanese</option>
        <option value="sw"'; echo ($blog_lang == "sw") ? ' selected' : ''; echo '>Swahili</option>
        <option value="ty"'; echo ($blog_lang == "ty") ? ' selected' : ''; echo '>Tahitian</option>
        <option value="ta"'; echo ($blog_lang == "ta") ? ' selected' : ''; echo '>Tamil</option>
        <option value="te"'; echo ($blog_lang == "te") ? ' selected' : ''; echo '>Telugu</option>
        <option value="bo"'; echo ($blog_lang == "bo") ? ' selected' : ''; echo '>Tibetan</option>
        <option value="th"'; echo ($blog_lang == "th") ? ' selected' : ''; echo '>Thai</option>
        <option value="sk"'; echo ($blog_lang == "sk") ? ' selected' : ''; echo '>Slovak</option>
        <option value="sv"'; echo ($blog_lang == "sv") ? ' selected' : ''; echo '>Swedish</option>
        <option value="uk"'; echo ($blog_lang == "uk") ? ' selected' : ''; echo '>Ukrainian</option>
        <option value="ur"'; echo ($blog_lang == "ur") ? ' selected' : ''; echo '>Urdu</option>
        <option value="ug"'; echo ($blog_lang == "ug") ? ' selected' : ''; echo '>Uyghur</option>
        <option value="vi"'; echo ($blog_lang == "vi") ? ' selected' : ''; echo '>Vietnamese</option>
        <option value="yo"'; echo ($blog_lang == "yo") ? ' selected' : ''; echo '>Yoruba</option>
        <option value="zu"'; echo ($blog_lang == "zu") ? ' selected' : ''; echo '>Zulu</option>
      </select>
      &nbsp;
      <label for="explicit-true"><input type="radio" id="explicit-true" name="blog_explicit" value="true" form="blog_settings"'; echo ($blog_explicit == "true") ? ' checked' : ''; echo '> explicit</label>
      <label for="explicit-false"><input type="radio" id="explicit-false" name="blog_explicit" value="false" form="blog_settings"'; echo ($blog_explicit == "false") ? ' checked' : ''; echo '> clean</label>
    ';

  // Finish table
  echo '</td></tr></tbody></table>';
}

// Series edit JavaScript
include ('./in.editseries.php');

// Footer
include ('./in.footer.php');
