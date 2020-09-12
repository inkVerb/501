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
    tinymce.init({
      selector: '.tinymce_editor',
      width: 600,
      height: 300,
      plugins: [
        'advlist autolink link image lists charmap print preview hr anchor pagebreak spellchecker',
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
  echo '<p><b>501 Blog</b> :: Hi, '.$fullname.'! | <a href="blog.php">View blog</a> | <a href="edit.php"><b>+</b> Ink new</a> | <a href="pieces.php">Pieces</a> | <a href="account.php">Account Settings</a> | <a href="logout.php">Logout</a></p>';
}

// Title the page so we know where we are
echo '<h1>'.$head_title.'</h1>';

?>

</header>
