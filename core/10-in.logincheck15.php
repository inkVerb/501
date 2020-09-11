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
    if ( (!isset($nologin_allowed)) || ($nologin_allowed != true) ) {
      // exit and redirect in one line
      exit(header("Location: blog.php"));
    }
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
      echo '<p class="error">SQL error!</p>';
      exit();
    }

// See if we are logged in by now
} elseif ( (isset($_SESSION['user_id'])) && (isset($_SESSION['user_name'])) ) {

  // Set our variables
  $user_id = $_SESSION['user_id'];
  $fullname = $_SESSION['user_name'];

} elseif ( (!isset($nologin_allowed)) || ($nologin_allowed != true) ) {
  exit(header("Location: blog.php"));
}

// We're still here, start the head
?>

<!DOCTYPE html>
<html>
<head>
  <!-- CSS file included as <link> -->
  <link href="style.css" rel="stylesheet" type="text/css" />

  <!-- One line of PHP with our <title> -->
  <title><?php echo $head_title.' :: 501 Blog'; ?></title>

  <!-- TinyMCE -->
  <?php if ($edit_page_yn == true) {
    ?>
    <script src='tinymce/tinymce.min.js'></script>
    <script type='text/javascript'>
      // Allow AJAX to read TinyMCE
      tinymce.init({
        setup: function (editor) {
          editor.on('change', function () {
            tinymce.triggerSave();
          });
        },
      // Make our Ctrl + S "Save draft" JS function work inside TinyMCE
      init_instance_callback: function (editor) {
        editor.addShortcut("ctrl+s", "Save draft", "custom_ctrl_s");
        editor.addCommand("custom_ctrl_s", function() {
            ajaxSaveDraft() // Run our "Save" AJAX
        });
      },

      selector: '.tinymce_editor',
      width: '100%',
      min_height: 500,
      max_height: 700,
      plugins: [
        'autoresize advlist autolink link image lists charmap print preview hr anchor pagebreak spellchecker',
        'searchreplace wordcount visualblocks visualchars code fullscreen insertdatetime media nonbreaking',
        'table emoticons template paste contextmenu styleprops',

      ],
      // This changes what is in the formatselect item
      block_formats: 'Paragraph=p; Heading 1=h1; Heading 2=h2; Heading 3=h3; Heading 4=h4; Heading 5=h5; Heading 6=h6; inline code=code; preformatted=pre',

      toolbar: 'formatselect | ' +
      'formatgroup paragraphgroup toolgroup ' + // These are defined below in toolbar_groups
      'bold italic ' +
      'pagebreak link unlink image media preview code fullscreen',

      toolbar_groups: {
          formatgroup: {
              icon: 'format',
              tooltip: 'Formatting',
              items: 'forecolor backcolor strikethrough subscript superscript removeformat pastetext'
          },
          paragraphgroup: {
              icon: 'paragraph',
              tooltip: 'Paragraph & Blocks',
              items: 'alignleft aligncenter alignright alignjustify | blockquote bullist numlist outdent indent table'
          },
          toolgroup: {
              icon: 'plus',
              tooltip: 'Insert',
              items: 'anchor insertdatetime charmap hr emoticons | searchreplace spellchecker print help'
          }
      },

      skin: 'oxide', // Default, dir: tinymce/skins alternative native skin is: oxide-dark
      icons: 'default', // Default, dir: tinymce/icons create your own:https://www.tiny.cloud/docs/advanced/creating-an-icon-pack/
      toolbar_location: 'bottom',
      menubar: false,
      paste_as_text: true,
      content_css: 'style.css',

    });
    </script>
    <?php
  } ?>
  <!-- TinyMCE end -->


</head>
<body>
  <header>

<?php
// Head finished

if ( (isset($user_id)) && (isset($fullname)) ) {
  echo '<p><b>501 Blog</b> :: Hi, '.$fullname.'! | <a href="blog.php">View blog</a> | <a href="edit.php"><b>+</b> Ink new</a> | <a href="pieces.php">Pieces</a> | <a href="medialibrary.php">Media</a> | <a href="account.php">Account Settings</a> | <a href="logout.php">Logout</a></p>';
}

// Title the page only if there is a $head_title argument
echo ( (isset($head_title)) && ($head_title != '') ) ? '<h1>'.$head_title.'</h1>' : false;

?>

</header>
