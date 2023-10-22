<!DOCTYPE html>
<html>
<head>
  <!-- CSS file included as <link> -->
  <link href="<?php echo $blog_web_base;?>/style.css" rel="stylesheet" type="text/css" />

  <!-- One line of PHP with our <title> -->
  <title><?php
    $browser_title = ($head_title == $blog_title) ? $blog_title : $head_title . ' | ' . $blog_title;
    echo ( (isset($piece_title)) && ($piece_title != '') ) ? $p_title . ' :: ' . $p_series . ' | ' . $blog_title : $browser_title;
  ?></title>

  <!-- SEO -->
  <?php
  if ((isset($seo_inf)) && ($seo_inf == true)) {
    $media_base = "$blog_web_base/media/pro/";
    $seo_image = (file_exists("$media_base/pro-seo.jpg")) ? "pro-seo.jpg" : "" ;
    $favicon = (file_exists("$media_base/pro-favicon.png")) ? "pro-favicon.png" : "" ;
    echo <<<EOF
    <link href="$blog_web_base/" rel="canonical" />
    <link rel="shortcut icon" type="image/png" href="$media_base/$favicon" />
    <meta name="robots" content="$blog_crawler_index, nofollow" />
    <meta name="description" content="$blog_description" />
    <meta property="og:url" content="$blog_web_base/" />
    <meta property="og:title" content="$blog_title" />
    <meta property="og:image" content="$media_base/$seo_image" />
    <meta property="og:type" content="website" />
    <meta http-equiv="content-type" content="text/html; charset=utf-8" />
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
EOF;
  }
  ?>

  <!-- TinyMCE -->
  <?php if ((isset($edit_page_yn)) && ($edit_page_yn == true)) {
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
// Head links

if ( (isset($user_id)) && (isset($fullname)) ) {
  echo '<div id="page_head"><b>'.$blog_title.'</b> Hi, '.$fullname.'! | <a href="'.$blog_web_base.'/">View blog</a> | <a href="'.$blog_web_base.'/edit.php"><b>+</b> Ink new</a> | <a href="'.$blog_web_base.'/pieces.php">Pieces</a> | <a href="'.$blog_web_base.'/medialibrary.php">Media</a> | <a href="'.$blog_web_base.'/settings.php">Settings</a> | <a href="'.$blog_web_base.'/account.php">Account</a> | <a href="'.$blog_web_base.'/logout.php">Logout</a></div>';
}

// Series editor?
if ((isset(($series_editor_yn))) && ($series_editor_yn)) {
  include ('./in.editseriesdiv.php');
}

// Title the page only if there is a $heading argument
$heading = (isset($heading)) ? $heading : $head_title;
echo ( (isset($heading)) && ($heading != '') ) ? '<h1>'.$heading.'</h1>' : false;
echo ($feed_link == true) ? '<p><small><a target="_blank" href="'.$blog_web_base.$feed_path.'">RSS</a></small></p>' : false;
?>

</header>
