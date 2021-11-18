<?php

// Include our config (with SQL) up near the top of our PHP file
include ('./in.config.php');

// Process the upload
if ( ($_SERVER['REQUEST_METHOD'] === 'POST') && (!empty($_FILES)) && ($_FILES['upload_file']['size'][0] != 0) && (isset($_SESSION['user_id'])) ) {

    $upload_dir_base = 'media/';
    $file_name = basename($_FILES['upload_file']['name'][0]);
    $temp_file = $_FILES['upload_file']['tmp_name'][0];
    $file_mime = mime_content_type($temp_file);
    $file_extension = strtolower(pathinfo($file_name,PATHINFO_EXTENSION));
    $file_basename = basename($file_name,'.'.$file_extension); // Strip off the extension
    $file_name = $file_basename.'.'.$file_extension; // Reassign extension with no caps
    $file_size = $_FILES['upload_file']['size'][0];
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

    // Create our $info_message
    $info_message = '';

    // Image formats
    if ( (($file_extension == 'jpg')  && ($file_mime == 'image/jpeg'))
    ||   (($file_extension == 'jpeg') && ($file_mime == 'image/jpeg'))
    ||   (($file_extension == 'png')  && ($file_mime == 'image/png'))
    ||   (($file_extension == 'gif')  && ($file_mime == 'image/gif')) ) {

      // Valid & accepted, get size & mime type
      $imageinfo = getimagesize($temp_file); // We didn't assign this value until we were sure it worked
      $image_type = $imageinfo['mime'];
      $image_dimensions = $imageinfo[3];
      if (getimagesize($temp_file)) {
        $info_message .= '<span class="upload-info">Image type: <code>'.$image_type.'</code><br>Dimensions:<br><code>'.$image_dimensions.'</code></span><br>';
        $upload_location = 'images';
        $basic_type = 'IMAGE';
      } else {
        $errors .= '<span class="error">Not an image</span><br><br>';
      }

    // SVG image
    } elseif (($file_extension == 'svg')  && ($file_mime == 'image/svg+xml')) {
      $info_message .= '<span class="upload-info">Image type: <code>'.$file_mime.'</code></span><br><br>';
      $upload_location = 'images';
      $basic_type = 'IMAGE';

    // Video formats
    } elseif ( (($file_extension == 'webm') && ($file_mime == 'video/webm'))
          ||   (($file_extension == 'ogg')  && ($file_mime == 'video/ogg'))
          ||   (($file_extension == 'ogg')  && ($file_mime == 'video/x-theora+ogg'))
          ||   (($file_extension == 'mp4')  && ($file_mime == 'video/mp4')) ) {
      $info_message .= '<span class="upload-info">Video type: <code>'.$file_mime.'</code></span><br><br>';
      $upload_location = 'video';
      $basic_type = 'VIDEO';

    // Audio formats
    } elseif ( (($file_extension == 'mp3') && ($file_mime == 'audio/mpeg'))
          ||   (($file_extension == 'ogg') && ($file_mime == 'audio/ogg'))
          ||   (($file_extension == 'wav') && ($file_mime == 'audio/x-wav')) // WAV files can have different interpretations of mime types
          ||   (($file_extension == 'wav') && ($file_mime == 'audio/wav')) ) {
      $info_message .= '<span class="upload-info">Audio type: <code>'.$file_mime.'</code></span><br><br>';
      $upload_location = 'audio';
      $basic_type = 'AUDIO';

    // Document formats
    } elseif ( (($file_extension == 'txt')  && ($file_mime == 'text/plain'))
          ||   (($file_extension == 'md')   && ($file_mime == 'text/plain'))
          ||   (($file_extension == 'htm')  && ($file_mime == 'text/html'))
          ||   (($file_extension == 'html') && ($file_mime == 'text/html'))
          ||   (($file_extension == 'doc')  && ($file_mime == 'text/html')) // Standard HTML, not yet compiled
          ||   (($file_extension == 'doc')  && ($file_mime == 'application/msword')) // Compiled, from MS Word
          ||   (($file_extension == 'docx') && ($file_mime == 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'))
          ||   (($file_extension == 'odt')  && ($file_mime == 'application/vnd.oasis.opendocument.text'))
          ||   (($file_extension == 'pdf')  && ($file_mime == 'application/x-pdf')) // PDF files can have different interpretations of mime types
          ||   (($file_extension == 'pdf')  && ($file_mime == 'application/pdf')) ) {
      $info_message .= '<span class="upload-info">Document type: <code>'.$file_mime.'</code></span><br><br>';
      $upload_location = 'docs';
      $basic_type = 'DOCUMENT';

    // Not allowed
    } else { // Not an accepted extension
      $errors .= '<span class="error">Type: '.$file_mime.' Allowed file types<br>
      Image:<code> .jpg, .jpeg, .png, .gif</code><br>
      Video:<code> .webm, .ogg, .mp4</code><br>
      Audio:<code> .mp3, .ogg, .wav</code><br>
      Docs:<code> .txt, .md, .htm, .html, .doc, .docx, .odt, .pdf</code><br></span><br><br>';
    }

    // Check if $no_errors is set to 0 by an error
    if ($errors != '') {
      $errors .= '<span class="error">File rejected</span><br><br>';
      // Show our $errors
      echo $errors;
    // File checks out
    } else {
      // Check if file name already exists
      $upload_dir = $upload_dir_base.$upload_location.'/';
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
        $file_basename = $new_file_basename;
        $file_name = $new_file_basename.'.'.$file_extension;
        $file_path_dest = $upload_dir.$file_name;
      }
      // Upload the file and check in one command
      if (move_uploaded_file($temp_file, $file_path_dest)) {

        // SQL entry
        $query = "INSERT INTO media_library (size, mime_type, basic_type, location, file_base, file_extension)
                  VALUES ('$file_size', '$file_mime', '$basic_type', '$upload_location', '$file_basename', '$file_extension')";
        $call = mysqli_query($database, $query);
        if (!$call) {
          $errors .= '<span class="error">SQL error</span><br><br>';
          // Show our $errors
          echo $errors;
        } else {
          // Get the new SQL entry ID
          $m_id  = $database->insert_id;

          $info_message .= '<span class="upload-info">File size: <code>'.$file_size_pretty.'</code></span><br id="mediatype_'.$m_id.'">'; // We need id="mediatype_..." so JS has something to change and it doesn't break
          $info_message .= '<span class="upload-info">File name: <code id="filename_'.$m_id.'">'.$file_name.'</code></span><br><br>';

          // Wrap the info in a paragraph
          $info_message = '<p id="upload_'.$m_id.'" class="blue">'.$info_message.'</p>';

          // AJAX mediaEdit button (calls a JS function already loaded by medialibrary.php, can't load that JS here)
          $edit_form = '
          <form id="mediaEdit_'.$m_id.'">
            <input type="hidden" value="'.$m_id.'" name="m_id">
            <button type="button" class="postform link-button inline orange" onclick="mediaEdit(\'mediaEdit_'.$m_id.'\', \'ajax.mediainfo.php\', \'media-editor\');" style="float: right;">edit</button>
          </form><br>';

          // AJAX-send the success message and edit link
          echo $edit_form.$info_message.'<hr>';
        }

      } else {
        $errors .= '<span class="error">Upload error</span><br><br>';
        // Show our $errors
        echo $errors;
      }
    }

} else { // End POST check
  exit ();
}

?>
