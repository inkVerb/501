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

    // Write our database connection file with:
    //// 1. A heredoc
    //// 2. the file_put_contents() function

    // Heredoc:
    $sqlConfigFile = <<<EOF
<?php
DEFINE ('DB_NAME', '$db_name');
DEFINE ('DB_USER', '$db_user');
DEFINE ('DB_PASSWORD', '$db_pass');
DEFINE ('DB_HOST', '$db_host');
EOF;

    // Write the file:
    file_put_contents('./in.sql.php', $sqlConfigFile);

    // Include our config file (which includes the newly-written SQL config) if it exists
    if (!file_exists('./in.sql.php')) {
      echo '<p>Could not create the database config file, quitting.</p>';
      exit ();
    } else {
      require_once ('./in.config.php');
    } // Now we have a database connection and we can begin making queries


    // Set the character settings in the database
    $query = "ALTER DATABASE webapp_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci";
    $call = mysqli_query($database, $query);
    if (!$call) {
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
    $call = mysqli_query($database, $query);
    if (!$call) {
      echo '<p>Could not create the necessary database tables, quitting.</p>';
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

    // Add the first admin user

    // Prepare our database values for entry
    $password_hashed = password_hash($password, PASSWORD_BCRYPT);
    // mysqli_real_escape_string() prepares it for security
    $fullname_sqlesc = mysqli_real_escape_string($database, $fullname);
    $username_sqlesc = mysqli_real_escape_string($database, $username);
    // Our config function escape_sql() does the same thing, but better
    $email_sqlesc = escape_sql($email);
    $favnumber_sqlesc = escape_sql($favnumber);

    // Run the query
    $query = "INSERT INTO users (fullname, username, email, favnumber, pass, type)
    VALUES ('$fullname_sqlesc', '$username_sqlesc', '$email_sqlesc', '$favnumber_sqlesc', '$password_hashed', 'admin')";
		// inline password hash: $query = "INSERT INTO users (name, username, email, pass, type) VALUES ('$fullname', '$username', '$email', '"  .  password_hash($password, PASSWORD_BCRYPT) .  "', 'admin')";
    $call = mysqli_query($database, $query);
    if (mysqli_affected_rows($database) == 1) {
      echo '<h1>All set!</h1>';
      echo '<p>Everything is ready for you to login!</p>
      <p>Username: '.$username.'</p>
      <p>Password: <i>(Whatever password you just used)</i></p>';
      exit (); // Finish

    } else {
      echo '<p>Could not run the installer.</p>';
      exit ();
    }

  } else {
    echo '<p class="error">Error.</p>';
  }

} // Finish POST if


// Our actual signup page

echo '<h1>Admin signup</h1>';
echo '
<form action="install.php" method="post">';

echo '<b>Database info</b><br><br>
Database name: <input type="text" name="db_name"><br><br>
Database username: <input type="text" name="db_user"><br><br>
Database password: <input type="text" name="db_pass"><br><br>
Database host: <input type="text" name="db_host" value="localhost"> (leave as <i>localhost</i> unless told otherwise)<br><br>
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
