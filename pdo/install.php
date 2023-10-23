<!DOCTYPE html>
<html>
<head>
  <!-- CSS file included as <link> -->
  <link href="style.css" rel="stylesheet" type="text/css" />
</head>
<body>

<?php


// Include our functions
include ('./in.functions.php');

// POSTed form?
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

  // Include our POST checks
  include ('./in.checks.php');

  // One-time database checks

  $db_name = (preg_match('/[a-zA-Z0-9_]{2,64}$/i', $_POST['db_name']))
  ? preg_replace("/[^a-zA-Z0-9_]/","", $_POST['db_name']) : '';
  if ($db_name == '') {
    echo '<p class="error">Not a valid database name!</p>';
    $no_db_cred_errors = false;
  }

  $db_user = (preg_match('/[a-zA-Z0-9_]{2,32}$/i', $_POST['db_user']))
  ? preg_replace("/[^a-zA-Z0-9_]/","", $_POST['db_user']) : '';
  if ($db_user == '') {
    echo '<p class="error">Not a valid database username!</p>';
    $no_db_cred_errors = false;
  }

  // Fancy ReGex that allows for all allowable characters in a MySQL database password, for these special characters... '/&*=]\[<>;,.:^?+$%-‘~!@#)(}{_  (and space)
  // Order of these special characters matters in a RegEx!
  $db_pass = (preg_match('/[A-Za-z0-9 \'\/&\*=\]\|[<>;,\.:\^\?\+\$%-‘~!@#)(}{_ ]{6,32}$/', $_POST['db_pass']))
  ? preg_replace("/[^A-Za-z0-9 \'\/&\*=\]\|[<>;,\.:\^\?\+\$%-‘~!@#)(}{_ ]/","", $_POST['db_pass']) : '';
  if ($db_pass == '') {
    echo '<p class="error">Not a valid database password!</p>';
    $no_db_cred_errors = false;
  }

  // This test (on two lines to make is easy to read) checks for either a valid URL starting with https:// or 'localhost'
  $db_host =
    ( ((filter_var($_POST['db_host'],FILTER_VALIDATE_URL)) && (substr($_POST['db_host'], 0, 8) === "https://"))
    || ($_POST['db_host'] == 'localhost') )
  ? $_POST['db_host'] : '';
  if ($db_user == '') {
    echo '<p class="error">Not a valid database host!</p>';
    $no_db_cred_errors = false;
  }

  // No errors, all ready
  if (($no_form_errors == true) && (!isset($no_db_cred_errors))) {

    // Web base URL
    $page = '/install.php';
    $protocol = stripos($_SERVER['SERVER_PROTOCOL'],'https') === 0 ? 'https://' : 'http://';
    $web_base = $protocol . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    $web_base = preg_replace('/'. preg_quote($page, '/') . '$/', '', $web_base);

    // Heredoc:
    $configFile = <<<EOF
<?php
\$db_name = '$db_name';
\$db_user = '$db_user';
\$db_pass = '$db_pass';
\$db_host = '$db_host';
\$blog_web_base = '$web_base';
EOF;

    // Write the file:
    file_put_contents('./in.conf.php', $configFile);

    // Include our config file (which includes the newly-written SQL config) if it exists
    if (!file_exists('./in.conf.php')) {
      echo '<p>Could not create the database config file, quitting.</p>';
      exit ();
    } else {
      require_once ('./in.db.php');
    } // Now we have a database connection and we can begin making queries

    // Set the character settings in the database
    $query = "ALTER DATABASE $db_name CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci";
    $statement = $database->query($query);
    if ($statement) {
      $installrun = true;
    } else {
      echo '<p>Could not update the database, quitting.</p>';
      exit ();
    }

    // Create our tables
    $query = "CREATE TABLE IF NOT EXISTS `users` (
      `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
      `fullname` VARCHAR(90) NOT NULL,
      `username` VARCHAR(90) NOT NULL,
      `email` VARCHAR(128) NOT NULL,
      `website` VARCHAR(128) DEFAULT NULL,
      `favnumber` TINYINT DEFAULT NULL,
      `pass` VARCHAR(255) DEFAULT NULL,
      `type` ENUM('member', 'contributor', 'writer', 'editor', 'admin') NOT NULL,
      `date_updated` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
      PRIMARY KEY (`id`)
    ) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4";
    $statement = $database->query($query);
    if ($statement) {
      $installrun = true;
    } else {
      echo '<p>Could not create the users database table, quitting.</p>';
      exit ();
    }
    $query = "CREATE TABLE IF NOT EXISTS `strings` (
      `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
      `userid` INT UNSIGNED NOT NULL,
      `random_string` VARCHAR(255) DEFAULT NULL,
      `usable` ENUM('live', 'cookie_login', 'dead') NOT NULL,
      `date_expires` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
      PRIMARY KEY (`id`)
    ) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4";
    $statement = $database->query($query);
    if ($statement) {
      $installrun = true;
    } else {
      echo '<p>Could not create the strings database table, quitting.</p>';
      exit ();
    }
    $query = "CREATE TABLE IF NOT EXISTS `pieces` (
      `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
      `type` ENUM('post', 'page', 'template') NOT NULL,
      `status` ENUM('live', 'dead') NOT NULL,
      `pub_yn` BOOLEAN NOT NULL DEFAULT false,
      `title` VARCHAR(90) NOT NULL,
      `subtitle` VARCHAR(90) DEFAULT NULL,
      `slug` VARCHAR(100) NOT NULL,
      `content` LONGTEXT DEFAULT NULL,
      `after` TINYTEXT DEFAULT NULL,
      `excerpt` TEXT DEFAULT NULL,
      `series` INT UNSIGNED DEFAULT 1,
      `tags` LONGTEXT DEFAULT NULL,
      `links` LONGTEXT DEFAULT NULL,
      `feat_img` INT UNSIGNED NOT NULL DEFAULT 0,
      `feat_aud` INT UNSIGNED NOT NULL DEFAULT 0,
      `feat_vid` INT UNSIGNED NOT NULL DEFAULT 0,
      `feat_doc` INT UNSIGNED NOT NULL DEFAULT 0,
      `date_live` TIMESTAMP NULL,
      `date_created` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
      `date_updated` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
      PRIMARY KEY (`id`)
    ) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4";
    $statement = $database->query($query);
    if ($statement) {
      $installrun = true;
    } else {
      echo '<p>Could not create the pieces database table, quitting.</p>';
      exit ();
    }
    $query = "CREATE TABLE IF NOT EXISTS `publications` (
      `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
      `piece_id` INT UNSIGNED NOT NULL,
      `type` ENUM('page', 'post') NOT NULL,
      `status` ENUM('live', 'dead') NOT NULL,
      `pubstatus` ENUM('published', 'redrafting') NOT NULL,
      `title` VARCHAR(90) NOT NULL,
      `subtitle` VARCHAR(90) DEFAULT NULL,
      `slug` VARCHAR(100) NOT NULL,
      `content` LONGTEXT DEFAULT NULL,
      `after` TINYTEXT DEFAULT NULL,
      `excerpt` TEXT DEFAULT NULL,
      `series` INT UNSIGNED DEFAULT 1,
      `aggregated` INT UNSIGNED DEFAULT 0,
      `duration` VARCHAR(12) DEFAULT 0,
      `guid` TEXT NOT NULL DEFAULT 0,
      `tags` LONGTEXT DEFAULT NULL,
      `links` LONGTEXT DEFAULT NULL,
      `feat_img` TEXT NOT NULL DEFAULT 0,
      `feat_aud` TEXT NOT NULL DEFAULT 0,
      `feat_vid` TEXT NOT NULL DEFAULT 0,
      `feat_doc` TEXT NOT NULL DEFAULT 0,
      `feat_img_mime` TEXT NOT NULL DEFAULT 0,
      `feat_aud_mime` TEXT NOT NULL DEFAULT 0,
      `feat_vid_mime` TEXT NOT NULL DEFAULT 0,
      `feat_doc_mime` TEXT NOT NULL DEFAULT 0,
      `feat_img_length` TEXT NOT NULL DEFAULT 0,
      `feat_aud_length` TEXT NOT NULL DEFAULT 0,
      `feat_vid_length` TEXT NOT NULL DEFAULT 0,
      `feat_doc_length` TEXT NOT NULL DEFAULT 0,
      `date_live` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
      `date_updated` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
      PRIMARY KEY (`id`)
    ) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4";
    $statement = $database->query($query);
    if ($statement) {
      $installrun = true;
    } else {
      echo '<p>Could not create the publications database table, quitting.</p>';
      exit ();
    }
    $query = "CREATE TABLE IF NOT EXISTS `publication_history` (
      `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
      `piece_id` INT UNSIGNED NOT NULL,
      `type` ENUM('page', 'post') NOT NULL,
      `title` VARCHAR(90) NOT NULL,
      `subtitle` VARCHAR(90) DEFAULT NULL,
      `slug` VARCHAR(100) NOT NULL,
      `content` LONGTEXT DEFAULT NULL,
      `after` TINYTEXT DEFAULT NULL,
      `excerpt` TEXT DEFAULT NULL,
      `series` INT UNSIGNED DEFAULT 1,
      `tags` LONGTEXT DEFAULT NULL,
      `links` LONGTEXT DEFAULT NULL,
      `feat_img` TEXT NOT NULL DEFAULT 0,
      `feat_aud` TEXT NOT NULL DEFAULT 0,
      `feat_vid` TEXT NOT NULL DEFAULT 0,
      `feat_doc` TEXT NOT NULL DEFAULT 0,
      `date_live` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
      `date_updated` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
      PRIMARY KEY (`id`)
    ) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4";
    $statement = $database->query($query);
    if ($statement) {
      $installrun = true;
    } else {
      echo '<p>Could not create the publication_history database table, quitting.</p>';
      exit ();
    }
    $query = "CREATE TABLE IF NOT EXISTS `series` (
      `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
      `name` VARCHAR(90) NOT NULL,
      `slug` VARCHAR(100) NOT NULL,
      `template` INT UNSIGNED DEFAULT NULL,
      `series_lang` VARCHAR(8) NOT NULL DEFAULT 'en',
      `series_link` TEXT DEFAULT NULL,
      `series_author` VARCHAR(90) DEFAULT NULL,
      `series_descr` VARCHAR(255) DEFAULT NULL,
      `series_summary` VARCHAR(255) DEFAULT NULL,
      `series_owner` VARCHAR(255) DEFAULT NULL,
      `series_email` VARCHAR(255) DEFAULT NULL,
      `series_copy` VARCHAR(90) DEFAULT NULL,
      `series_keywords` TEXT DEFAULT NULL,
      `series_explicit` VARCHAR(5) NOT NULL DEFAULT 'false',
      `series_cat1` VARCHAR(255) DEFAULT NULL,
      `series_cat2` VARCHAR(255) DEFAULT NULL,
      `series_cat3` VARCHAR(255) DEFAULT NULL,
      `series_cat4` VARCHAR(255) DEFAULT NULL,
      `series_cat5` VARCHAR(255) DEFAULT NULL,
      PRIMARY KEY (`id`)
    ) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4";
    $statement = $database->query($query);
    if ($statement) {
      $installrun = true;
    } else {
      echo '<p>Could not create the series database table, quitting.</p>';
      exit ();
    }
    $query = "INSERT INTO series (name, slug) VALUES ('Blog', 'blog')";
    $statement = $database->query($query);
    if ($statement) {
      $installrun = true;
    } else {
      echo '<p>Could not create the series database table, quitting.</p>';
      exit ();
    }
    $query = "CREATE TABLE IF NOT EXISTS `aggregation` (
      `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
      `name` VARCHAR(90) NOT NULL,
      `source` TEXT DEFAULT NULL,
      `series` INT UNSIGNED DEFAULT 1,
      `description` TINYTEXT DEFAULT NULL,
      `update_interval` TINYTEXT DEFAULT '15',
      `status` ENUM('active', 'dormant', 'problematic', 'deleting') NOT NULL,
      `on_delete` ENUM('convert', 'erase') NOT NULL,
      `last_updated` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
      `date_added` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
      PRIMARY KEY (`id`)
    ) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4";
    $statement = $database->query($query);
    if ($statement) {
      $installrun = true;
    } else {
      echo '<p>Could not create the aggregations database table, quitting.</p>';
      exit ();
    }
    $query = "CREATE TABLE IF NOT EXISTS `media_library` (
      `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
      `size` BIGINT UNSIGNED DEFAULT 1,
      `mime_type` VARCHAR(128) NOT NULL,
      `basic_type` VARCHAR(12) NOT NULL,
      `location` VARCHAR(255) NOT NULL,
      `file_base` VARCHAR(255) NOT NULL,
      `file_extension` VARCHAR(52) NOT NULL,
      `title_text` VARCHAR(255) DEFAULT NULL,
      `alt_text` VARCHAR(255) DEFAULT NULL,
      `duration` VARCHAR(12) DEFAULT NULL,
      `date_created` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
      `date_updated` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
      PRIMARY KEY (`id`)
    ) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4";
    $statement = $database->query($query);
    if ($statement) {
      $installrun = true;
    } else {
      echo '<p>Could not create the media_library database table, quitting.</p>';
      exit ();
    }
    $query = "CREATE TABLE IF NOT EXISTS `media_images` (
      `m_id` INT UNSIGNED NOT NULL,
      `orientation` VARCHAR(4) NOT NULL,
      `width` VARCHAR(4) NOT NULL,
      `height` VARCHAR(4) NOT NULL,
      `xs` VARCHAR(9) NOT NULL,
      `sm` VARCHAR(9) NOT NULL,
      `md` VARCHAR(9) NOT NULL,
      `lg` VARCHAR(9) NOT NULL,
      `xl` VARCHAR(9) NOT NULL,
      PRIMARY KEY (`m_id`)
    ) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4";
    $statement = $database->query($query);
    if ($statement) {
      $installrun = true;
    } else {
      echo '<p>Could not create the media_images database table, quitting.</p>';
      exit ();
    }

    // Add our new tables for the later app upgrade
    $query = "CREATE TABLE IF NOT EXISTS `blog_settings` (
      `web_base` VARCHAR(2048) NOT NULL, -- May be redundant from in.conf.php, but coult be useful for future development
      `public` BOOLEAN NOT NULL DEFAULT true,
      `title` VARCHAR(90) DEFAULT '501 Blog',
      `tagline` VARCHAR(120) DEFAULT 'Where code stacks',
      `description` LONGTEXT DEFAULT 'Long, poetic explanations of blog contents are useful in search engines, podcasts, and other places on the interwebs.',
      `keywords` LONGTEXT DEFAULT NULL,
      `summary_words` INT UNSIGNED DEFAULT 50,
      `piece_items` INT UNSIGNED DEFAULT 10,
      `feed_items` INT UNSIGNED DEFAULT 20,
      `default_series` INT UNSIGNED DEFAULT 1,
      `crawler_index` ENUM('index', 'noindex') DEFAULT 'index',
      `blog_lang` VARCHAR(8) NOT NULL DEFAULT 'en',
      `blog_link` TEXT DEFAULT NULL,
      `blog_author` VARCHAR(90) DEFAULT NULL,
      `blog_descr` VARCHAR(255) DEFAULT NULL,
      `blog_summary` VARCHAR(255) DEFAULT NULL,
      `blog_owner` VARCHAR(255) DEFAULT NULL,
      `blog_email` VARCHAR(255) DEFAULT NULL,
      `blog_copy` VARCHAR(90) DEFAULT NULL,
      `blog_keywords` TEXT DEFAULT NULL,
      `blog_explicit` VARCHAR(5) NOT NULL DEFAULT 'false',
      `blog_cat1` VARCHAR(255) DEFAULT NULL,
      `blog_cat2` VARCHAR(255) DEFAULT NULL,
      `blog_cat3` VARCHAR(255) DEFAULT NULL,
      `blog_cat4` VARCHAR(255) DEFAULT NULL,
      `blog_cat5` VARCHAR(255) DEFAULT NULL
    ) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4";
    $statement = $database->query($query);
    if ($statement) {
      $installrun = true;
    } else {
      echo '<p>Could not create the blog_settings database table, quitting.</p>';
      exit ();
    }
    $query = $database->prepare("INSERT INTO blog_settings (web_base, title, tagline, description, summary_words, piece_items, feed_items, crawler_index) VALUES (:web_base, '501 Blog', 'Where code stacks', 'Long, poetic explanations of blog contents are useful in search engines, podcasts, and other places on the interwebs.', 50, 10, 20, 'index')");
    $query->bindParam(':web_base', $web_base);
    try {
      $query->execute();
      $installrun = true;
    } catch (PDOException $error) {
      echo '<p>Could not create the blog_settings database table, quitting.</p>';
    }

    // Add the first admin user

    // Prepare our database values for entry
    $password_hashed = password_hash($password, PASSWORD_BCRYPT);
    $fullname_trim = DB::trimspace($fullname);
    $username_trim = DB::trimspace($username);
    $email_trim = DB::trimspace($email);
    $favnumber_trim = DB::trimspace($favnumber);

    // Run the query
    $query = "INSERT INTO users (fullname, username, email, favnumber, pass, type)
    VALUES ('$fullname_trim', '$username_trim', '$email_trim', '$favnumber_trim', '$password_hashed', 'admin')";
		// inline password hash: $query = "INSERT INTO users (name, username, email, pass, type) VALUES ('$fullname', '$username', '$email', '"  .  password_hash($password, PASSWORD_BCRYPT) .  "', 'admin')";
    $statement = $database->query($query);
    if ($statement) {
      echo '<h1>All set!</h1>';
      echo '<p>Everything is ready for you to login!</p>
      <p>Username: '.$username.'</p>
      <p>Password: <i>(Whatever password you just used)</i></p>
      <p><b><a href="webapp.php">Login</a></b></p>';
      exit (); // Finish

    } else {
      echo '<p>Could not run the installer.</p>';
      exit ();
    }

  } else {
    echo '<p>Serious error.</p>';
    exit ();
  }

} // Finish POST if

// Installing

// Database options already set?
if (file_exists('./in.conf.php')) {
  include ('./in.conf.php');
}

// Our actual signup page
echo '<h1>Admin signup</h1>';
echo '
<form action="install.php" method="post">';

echo '<b>Database info</b><br><br>
Database name: <input type="text" name="db_name" value="'.$db_name.'"><br><br>
Database username: <input type="text" name="db_user" value="'.$db_user.'"><br><br>
Database password: <input type="text" name="db_pass" value="'.$db_pass.'"><br><br>
Database host: <input type="text" name="db_host" value="localhost" value="'.$db_host.'"> (leave as <i>localhost</i> unless told otherwise)<br><br>
<br><br>
<b>Admin user</b><br><br>';

echo 'Name: '.formInput('fullname', $name, $check_err).'<br><br>';
echo 'Username: '.formInput('username', $username, $check_err).'<br><br>';
echo 'Email: '.formInput('email', $email, $check_err).'<br><br>';
echo 'Favorite number: '.formInput('favnumber', $favnumber, $check_err).' (1-100 security question)<br><br>';
echo 'Password: '.formInput('password', $password, $check_err).'<br><br>';
echo 'Confirm password: '.formInput('password2', $password2, $check_err).'<br><br>';

echo '
  <input type="submit" value="Install web app">
</form>
';

?>

</body>
</html>
