<?php

// Include our config (with SQL) up near the top of our PHP file
include ('./in.config.php');

// Include our login cluster
$head_title = 'Upload form example'; // Set a <title> name used next
$edit_page_yn = false; // Include JavaScript for TinyMCE?
include ('./in.logincheck.php');
include ('./in.head.php');

// Process the upload
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

  // Was a file uploaded?
  if ($_FILES['upload_file']['size'] == 0) {
    echo '<p class="error">No file selected</p>';
  } else {

    // Set some basics
    $upload_dir = 'uploads/';
    $file_name = basename($_FILES['upload_file']['name']);
    $file_path_dest = $upload_dir.$file_name;
    $temp_file = $_FILES['upload_file']['tmp_name'];
    $file_mime = mime_content_type($temp_file);

    // Upload the file and check in one command
    if (move_uploaded_file($temp_file, $file_path_dest)) {
      echo '<p class="blue">File uploaded: <code>'.$file_name.'</code><br>File type: <code>'.$file_mime.'</code></p>';
    } else {
      echo '<p class="error">Upload error</p>';
    }

  } // End empty file check
} // End POST check

// The <form>
?>
<!DOCTYPE html>
<html>
<body>

<form action='upload.php' method='post' enctype='multipart/form-data'>
  Select a file to upload:
  <input type='file' name='upload_file' id='upload_file'>
  <input type='submit' value='Upload' name='submit'>
</form>

</body>
</html>

<?php

// Footer
include ('./in.footer.php');
