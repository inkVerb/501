<?php

// Include our config (with SQL) up near the top of our PHP file
include ('./in.config.php');

// Include our login cluster
$head_title = 'Upload form example: select images only'; // Set a <title> name used next
$edit_page_yn = false; // Include JavaScript for TinyMCE?
include ('./in.logincheck.php');
include ('./in.head.php');

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
    $file_extension = strtolower(pathinfo($file_name,PATHINFO_EXTENSION));
    $errors = '';

    // Allowedd formats
    if ( (($file_extension == 'jpg')  && ($file_mime == 'image/jpeg'))
    ||   (($file_extension == 'jpeg') && ($file_mime == 'image/jpeg'))
    ||   (($file_extension == 'png')  && ($file_mime == 'image/png'))
    ||   (($file_extension == 'gif')  && ($file_mime == 'image/gif')) ) {

      // Valid & accepted, get dimensions & mime type
      $imageinfo = getimagesize($temp_file);
      $image_dimensions = $imageinfo[3];
      if (getimagesize($temp_file)) {
        echo '<p class="blue">Image type: <code>'.$file_mime.'</code><br>Dimensions: <code>'.$image_dimensions.'</code></p>';
      } else {
        $errors .= '<p class="error">Not an image</p>';
      }

    } else { // Not an accepted extension
      $errors .= '<p class="error">Only .jpg, .jpeg, .png & .gif files allowed</p>';
    }

    // Check if $no_errors is set to 0 by an error
    if ($errors != '') {
      $errors .= '<p class="error">File rejected</p>';
      // Show our $errors
      echo $errors;
    // Upload the file and check in one command
    } else {
      if (move_uploaded_file($temp_file, $file_path_dest)) {
        echo '<p class="blue">File uploaded: <code>'.$file_name.'</code></p>';
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
