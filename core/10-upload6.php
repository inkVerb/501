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

    $upload_dir = 'uploads/';
    $file_name = basename($_FILES['upload_file']['name']);
    $temp_file = $_FILES['upload_file']['tmp_name'];
    $file_mime = mime_content_type($temp_file);
    $file_extension = strtolower(pathinfo($file_name,PATHINFO_EXTENSION));
    $file_basename = basename($file_name,'.'.$file_extension); // Strip off the extension
    $file_name = $file_basename.'.'.$file_extension; // Reassign extension with no caps
    $file_size = $_FILES['upload_file']['size'];
    $size_limit = 5000000; // 5MB
    $errors = '';

    // Check file size
    if ($file_size > $size_limit) {
      $errors .= '<span class="error">File is too large. Size: '.$file_size.'</span><br><br>';
    } else {
      // This is a handy function to make file sizes in bytes readable by humans
      function human_file_size($size, $unit="") {
        if ( (!$unit && $size >= 1<<30) || ($unit == "GB") )
          return number_format($size/(1<<30),2)."GB";
        if ( (!$unit && $size >= 1<<20) || ($unit == "MB") )
          return number_format($size/(1<<20),2)."MB";
        if ( (!$unit && $size >= 1<<10) || ($unit == "KB") )
          return number_format($size/(1<<10),2)."KB";
        return number_format($size)." bytes";
      }
      // Use our handy function
      $file_size_pretty = human_file_size($file_size);
    }

    // Image formats
    if ( (($file_extension == 'jpg')  && ($file_mime == 'image/jpeg'))
    ||   (($file_extension == 'jpeg') && ($file_mime == 'image/jpeg'))
    ||   (($file_extension == 'png')  && ($file_mime == 'image/png'))
    ||   (($file_extension == 'gif')  && ($file_mime == 'image/gif')) ) {

      // Valid & accepted, get dimensions & mime type
      $imageinfo = getimagesize($temp_file); // We didn't assign this value until we were sure it worked
      $image_type = $imageinfo['mime'];
      $image_dimensions = $imageinfo[3];
      if (getimagesize($temp_file)) {
        echo '<p class="blue">Image type: <code>'.$file_mime.'</code><br>Dimensions: <code>'.$image_dimensions.'</code></p>';
      } else {
        $errors .= '<p class="error">Not an image</p>';
      }

    // SVG image
    } elseif (($file_extension == 'svg')  && ($file_mime == 'image/svg+xml')) {
      echo '<p class="blue">Image type: <code>'.$file_mime.'</code></p>';

    // Video formats
    } elseif ( (($file_extension == 'webm') && ($file_mime == 'video/webm'))
          ||   (($file_extension == 'ogg')  && ($file_mime == 'video/ogg'))
          ||   (($file_extension == 'ogg')  && ($file_mime == 'video/x-theora+ogg'))
          ||   (($file_extension == 'mp4')  && ($file_mime == 'video/mp4')) ) {
      echo '<p class="blue">Video type: <code>'.$file_mime.'</code></p>';

    // Audio formats
    } elseif ( (($file_extension == 'mp3') && ($file_mime == 'audio/mpeg'))
          ||   (($file_extension == 'ogg') && ($file_mime == 'audio/ogg'))
          ||   (($file_extension == 'wav') && ($file_mime == 'audio/x-wav')) // WAV files can have different interpretations of mime types
          ||   (($file_extension == 'wav') && ($file_mime == 'audio/wav')) ) {
      echo '<p class="blue">Audio type: <code>'.$file_mime.'</code></p>';

    // Document formats
    } elseif ( (($file_extension == 'txt')  && ($file_mime == 'text/plain'))
          ||   (($file_extension == 'md')   && ($file_mime == 'text/plain'))
          ||   (($file_extension == 'doc')  && ($file_mime == 'text/html')) // Standard HTML, not yet compiled
          ||   (($file_extension == 'doc')  && ($file_mime == 'application/msword')) // Compiled, from MS Word
          ||   (($file_extension == 'docx') && ($file_mime == 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'))
          ||   (($file_extension == 'odt')  && ($file_mime == 'application/vnd.oasis.opendocument.text'))
          ||   (($file_extension == 'pdf')  && ($file_mime == 'application/x-pdf')) // PDF files can have different interpretations of mime types
          ||   (($file_extension == 'pdf')  && ($file_mime == 'application/pdf')) ) {
      echo '<p class="blue">Document type: <code>'.$file_mime.'</code></p>';

    // Not allowed
    } else { // Not an accepted extension
      $errors .= '<p class="error">Allowed file types<br>
      Image:<code> .jpg, .jpeg, .png, .gif</code><br>
      Video:<code> .webm, .ogg, .mp4</code><br>
      Audio:<code> .mp3, .ogg, .wav</code><br>
      Docs:<code> .txt, .md, .doc, .docx, .odt, .pdf</code><br></p>';
    }

    // Check if $no_errors is set to 0 by an error
    if ($errors != '') {
      $errors .= '<p class="error">File rejected</p>';
      // Show our $errors
      echo $errors;

    // File checks out
    } else {
      // Check if file name already exists
      $file_path_dest = $upload_dir.$file_name;
      if (file_exists($file_path_dest)) {
        $append_int = 1;
        $new_file_basename = $file_basename.'-'.$append_int;
        $new_file_path_dest = $upload_dir.$new_file_basename.'.'.$file_extension;
        while (file_exists($new_file_path_dest)) {
          $new_file_basename = $file_basename.'-'.$append_int;
          $new_file_path_dest = $upload_dir.$new_file_basename.'.'.$file_extension;
          $append_int++; // Increment our appendage
        }
        // Reset our values
        $file_name = $new_file_basename.'.'.$file_extension;
        $file_path_dest = $upload_dir.$file_name;
      }
      // Upload the file and check in one command
      if (move_uploaded_file($temp_file, $file_path_dest)) {
        echo '<p class="blue">File size: <code>'.$file_size_pretty.'</code><br>File name: <code>'.$file_name.'</code></p>';
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
