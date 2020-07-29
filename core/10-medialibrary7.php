<?php

// Include our config (with SQL) up near the top of our PHP file
include ('./in.config.php');

// Include our login cluster
$head_title = 'Media Library'; // Set a <title> name used next
$edit_page_yn = false; // Include JavaScript for TinyMCE?
include ('./in.login_check.php');

?>

<script src="dropzone.min.js"></script>

<form class="dropzone ml" action='upload.php' method='post' enctype='multipart/form-data'></form>

<?php

// Footer
include ('./in.footer.php');
