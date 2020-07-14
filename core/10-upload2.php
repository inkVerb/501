<?php

// Include our config (with SQL) up near the top of our PHP file
include ('./in.config.php');

// Include our pieces functions
include ('./in.metaeditfunctions.php');

// Include our login cluster
$head_title = 'Upload form example'; // Set a <title> name used next
$edit_page_yn = false; // Include JavaScript for TinyMCE?
$nologin_allowed = true; // Login requires this page
include ('./in.login_check.php');

// Process the upload
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

  // Was a file uploaded?
  if ($_FILES['upload_file']['size'] == 0) {
    echo '<p class="error">No file selected</p>';
  } else {

    $upload_dir = 'uploads/';
    $file_name = basename($_FILES['upload_file']['name']);
    $file_path_dest = $upload_dir.$file_name;
    $temp_file = $_FILES['upload_file']['tmp_name'];
    $file_mime = mime_content_type($temp_file);
    $errors = '';

    // Check for valid image by getting info
    if (getimagesize($temp_file)) {
      echo '<p class="blue">Image is real</p>';
    } else {
      $errors .= '<p class="error">Not an image</p>';
    }

    // Check if $no_errors is set to 0 by an error
    if ($errors != '') {
      $errors .= '<p class="error">File rejected</p>';
      // Show our $errors
      echo $errors;
    // Upload the file and check in one command
    } else {
      if (move_uploaded_file($temp_file, $file_path_dest)) {
        echo '<p class="blue">File uploaded: <code>'.$file_name.'</code><br>File type: <code>'.$file_mime.'</code></p>';
      } else {
        $errors .= '<p class="error">Upload error</p>';
        // Show our $errors
        echo $errors;
      }
    }

  } // End empty file check
} // End POST check

// The <form>
?>
<!DOCTYPE html>
<html>
<body>

<form action='upload.php' method='post' enctype='multipart/form-data'>
  Select image to upload:
  <input type='file' name='upload_file' id='upload_file'>
  <input type='submit' value='Upload' name='submit'>
</form>

</body>
</html>

<?php

// Footer
include ('./in.footer.php');
