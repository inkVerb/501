<!DOCTYPE html>
<html>
<head>
  <!-- CSS file included as <link> -->
  <link href="style.css" rel="stylesheet" type="text/css" />

  <!-- One line of PHP with our <title> -->
  <title><?php echo $head_title; ?></title>

  <!-- TinyMCE -->
  <script src="tinymce/tinymce.min.js"></script>
  <script type="text/javascript">
  tinymce.init({
    selector: '.tinymce_editor', // This is the class for our "Content" <textarea>
    width: 600,
    height: 300,
    plugins: [
      'advlist autolink link image lists charmap print preview hr anchor pagebreak spellchecker',
      'searchreplace wordcount visualblocks visualchars code fullscreen insertdatetime media nonbreaking',
      'table emoticons template paste help'
    ],
    toolbar: 'undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | ' +
      'bullist numlist outdent indent | link image | print preview media fullpage | ' +
      'forecolor backcolor emoticons | help',
    menu: {
      favs: {title: 'My Favorites', items: 'code visualaid | searchreplace | spellchecker | emoticons'}
    },
    menubar: 'favs file edit view insert format tools table help',
    content_css: 'css/content.css'
  });
  </script>
  <!-- TinyMCE end -->


</head>
<body>
<h1>501 Blog</h1>

<?php

// See if we have a cookie
if (isset($_COOKIE['user_key'])) {
  // Assign the current time
  $time_now = date("Y-m-d H:i:s");

  // Get the user ID from the key strings table
  $user_key = $_COOKIE['user_key'];
  $user_key_sqlesc = escape_sql($user_key); // SQL escape to make sure hackers aren't messing with cookies to inject SQL
  $query = "SELECT userid FROM strings WHERE BINARY random_string='$user_key_sqlesc' AND usable='cookie_login' AND  date_expires > '$time_now'";
  $call = mysqli_query($database, $query);
  if (mysqli_num_rows($call) == 1) {
    // Assign the values
    $row = mysqli_fetch_array($call, MYSQLI_NUM);
      $user_id = "$row[0]";
  } else { // Destroy cookies, SESSION, and redirect
    $query = "UPDATE strings SET usable='dead' WHERE BINARY random_string='$user_key_sqlesc'";
    $call = mysqli_query($database, $query);
    if (!$call) { // It doesn't matter if the key is there or not, just that SQL is working
      echo '<p class="error">SQL key error!</p>';
    } else {
      $_SESSION = array(); // Reset the `_SESSION` array
      session_destroy();
      setcookie(session_name(), null, 86401); // Set any _SESSION cookies to expire in Jan 1970
      unset($_COOKIE['user_key']);
      setcookie('user_key', null, 86401);
    }
    // exit and redirect in one line
    exit(header("Location: webapp.php"));
  }

  // Get the user's info from the users table
  $query = "SELECT fullname FROM users WHERE id='$user_id'";
  $call = mysqli_query($database, $query);
  // Check to see that our SQL query returned exactly 1 row
  if (mysqli_num_rows($call) == 1) {
    // Assign the values
    $row = mysqli_fetch_array($call, MYSQLI_NUM);
      $fullname = "$row[0]";

      // Set the $_SESSION array
      $_SESSION['user_id'] = $user_id;
      $_SESSION['user_name'] = $fullname;

    } else {
      echo "Database error!";
      exit();
    }
}

// See if we are logged in by now
if ((isset($_SESSION['user_id'])) && (isset($_SESSION['user_name']))) {

  // Set our variables
  $user_id = $_SESSION['user_id'];
  $fullname = $_SESSION['user_name'];

echo '<p>Hi, '.$fullname.'! <a href="account.php">Account Settings</a> | <a href="logout.php">Logout</a></p>';

}

?>
