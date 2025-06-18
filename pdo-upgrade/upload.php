<?php

// Include our config (with SQL) up near the top of our PHP file
include ('./in.db.php');

// Process the upload
if ( ($_SERVER['REQUEST_METHOD'] === 'POST') && (!empty($_FILES)) && ($_FILES['upload_file']['size'][0] != 0) && (isset($_SESSION['user_id'])) ) {

    $upload_dir = 'media/uploads/';
    $file_name = basename($_FILES['upload_file']['name'][0]);
    $temp_file = $_FILES['upload_file']['tmp_name'][0];
    $file_mime = mime_content_type($temp_file);
    $file_extension = strtolower(pathinfo($file_name,PATHINFO_EXTENSION));
    $file_basename = basename($file_name,'.'.$file_extension); // Strip off the extension
    $file_name = $file_basename.'.'.$file_extension; // Reassign extension with no caps
    $file_size = $_FILES['upload_file']['size'][0];
    $size_limit = 100000000; // 100MB
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
    ||   (($file_extension == 'gif')  && ($file_mime == 'image/gif'))
    // Allow bmp because we will convert it to web-friendly
    ||   (($file_extension == 'bmp')  && ($file_mime == 'image/bmp'))
    ||   (($file_extension == 'bmp')  && ($file_mime == 'image/x-windows-bmp'))
    ||   (($file_extension == 'bmp')  && ($file_mime == 'image/x-ms-bmp')) ) {

      // Valid & accepted
      $imageinfo = getimagesize($temp_file); // We didn't assign this value until we were sure it worked

      // Linux process prep
      list($img_w, $img_h) = $imageinfo; // Break-down this array, previously we used: $image_dimensions = $imageinfo[3];
        $img_size = $img_w.'x'.$img_h;
      if ($img_w == $img_h) {
        $img_orientation = 'squr';
        // Image size ratios
        $img_xs = ($img_w > 154) ? '154x154' : 'none';
        $img_sm = ($img_w > 484) ? '484x484' : 'none';
        $img_md = ($img_w > 800) ? '800x800' : 'none';
        $img_lg = ($img_w > 1280) ? '1280x1280' : 'none';
        $img_xl = ($img_w > 1920) ? '1920x1920' : 'none';

      } elseif ($img_w > $img_h) {
        $img_orientation = 'wide';
        // Image size ratios
        $img_xs = ($img_w > 154) ? '154x'.round(154*($img_h/$img_w)) : 'none';
        $img_sm = ($img_w > 484) ? '484x'.round(484*($img_h/$img_w)) : 'none';
        $img_md = ($img_w > 800) ? '800x'.round(800*($img_h/$img_w)) : 'none';
        $img_lg = ($img_w > 1280) ? '1280x'.round(1280*($img_h/$img_w)) : 'none';
        $img_xl = ($img_w > 1920) ? '1920x'.round(1920*($img_h/$img_w)) : 'none';

      } elseif ($img_w < $img_h) {
        $img_orientation = 'tall';
        // Image size ratios
        $img_xs = ($img_w > 154) ? round(154*($img_w/$img_h)).'x154' : 'none';
        $img_sm = ($img_w > 484) ? round(484*($img_w/$img_h)).'x484' : 'none';
        $img_md = ($img_w > 800) ? round(800*($img_w/$img_h)).'x800' : 'none';
        $img_lg = ($img_w > 1280) ? round(1280*($img_w/$img_h)).'x1280' : 'none';
        $img_xl = ($img_w > 1920) ? round(1920*($img_w/$img_h)).'x1920' : 'none';

      }

      // Get size & mime type
      //$image_type = $imageinfo['mime']; // We won't use this anymore, but keep it for reference
      //$image_dimensions = $imageinfo[3]; // We won't use this anymore, but keep it for reference
      $image_dimensions = $img_w.'x'.$img_h;
      if (getimagesize($temp_file)) {
        $info_message .= '<span class="upload-info">Image type: <code>'.$file_extension.'</code><br>Dimensions: <code>'.$image_dimensions.'</code></span><br>';
        $upload_type = 'img';
        $upload_location = 'images';
        $basic_type = 'IMAGE';
      } else {
        $errors .= '<span class="error">Not an image</span><br><br>';
      }

    // SVG image
    } elseif (($file_extension == 'svg')  && ($file_mime == 'image/svg+xml')) {
      $info_message .= '<span class="upload-info">Image type: <code>'.$file_extension.'</code></span><br><br>';
      $upload_type = 'svg';
      $upload_location = 'images';
      $basic_type = 'IMAGE';

    // Video formats
    } elseif ( (($file_extension == 'webm') && ($file_mime == 'video/webm'))
          ||   (($file_extension == 'ogg')  && ($file_mime == 'video/ogg'))
          ||   (($file_extension == 'ogg')  && ($file_mime == 'video/x-theora+ogg'))
          ||   (($file_extension == 'mp4')  && ($file_mime == 'video/mp4'))
          // Allow other mimetypes because we will convert them to web-playable
          ||   (($file_extension == 'flv') && ($file_mime == 'video/x-flv'))
          ||   (($file_extension == 'avi') && ($file_mime == 'video/x-msvideo'))
          ||   (($file_extension == 'mkv') && ($file_mime == 'video/x-matroska'))
          ||   (($file_extension == 'mov') && ($file_mime == 'video/quicktime')) ) {
      $info_message .= '<span class="upload-info">Video type: <code>'.$file_extension.'</code></span><br><br>';
      $upload_type = 'vid';
      $upload_location = 'video';
      $basic_type = 'VIDEO';

    // Audio formats
    } elseif ( (($file_extension == 'mp3') && ($file_mime == 'audio/mpeg'))
          ||   (($file_extension == 'ogg') && ($file_mime == 'audio/ogg'))
          ||   (($file_extension == 'wav') && ($file_mime == 'audio/x-wav')) // WAV files can have different interpretations of mime types
          ||   (($file_extension == 'wav') && ($file_mime == 'audio/wav'))
          ||   (($file_extension == 'flac') && ($file_mime == 'audio/x-flac')) // FLAC files can have different interpretations of mime types
          ||   (($file_extension == 'flac') && ($file_mime == 'audio/flac')) ) {
      $info_message .= '<span class="upload-info">Audio type: <code>'.$file_extension.'</code></span><br><br>';
      $upload_type = 'aud';
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
      $info_message .= '<span class="upload-info">Document type: <code>'.$file_extension.'</code></span><br><br>';
      $upload_type = 'doc';
      $upload_location = 'docs';
      $basic_type = 'DOCUMENT';

    // Not allowed
    } else { // Not an accepted extension
      $errors .= '<span class="error">Type: '.$file_extension.'<br><br>Allowed file types<br>
      Image:<code> .jpg, .jpeg, .png, .gif</code><br>
      Video:<code> .webm, .ogg, .mp4</code><br>
      Audio:<code> .mp3, .ogg, .wav, .flac</code><br>
      Docs:<code> .txt, .md, .htm, .html, .doc, .docx, .odt, .pdf</code><br>
      Converted:<code> .flv, .bmp</code></span><br><br>';
    }

    // Check if $no_errors is set to 0 by an error
    if ($errors != '') {
      $errors .= '<span class="error">File rejected</span><br><br>';
      // Show our $errors
      echo $errors;
    // File checks out
    } else {
      // Set final file extension for non-accepted convertable files
      $final_extension = $file_extension; // Normal circumstances default
      $final_extension = ($file_extension == 'bmp') ? 'png' : $final_extension;
      $final_extension = ($file_extension == 'flv') ? 'mp4' : $final_extension;
      $final_extension = ($file_extension == 'avi') ? 'mp4' : $final_extension;
      $final_extension = ($file_extension == 'mkv') ? 'mp4' : $final_extension;
      $final_extension = ($file_extension == 'mov') ? 'mp4' : $final_extension;
      // Most documents are or are converted to a .pdf, so check that for name conflicts
      $final_extension = ( ($basic_type == 'DOCUMENT') && ($file_extension != 'doc') && ($file_extension != 'txt') ) ? 'pdf' : $final_extension;
      // All audio is converted to .mp3, so check that for name conflicts
      $final_extension = ($basic_type == 'AUDIO') ? 'mp3' : $final_extension;
      // Check if file name already exists
      $upload_path_dest = 'media/'.$upload_location.'/'.$file_basename.'.'.$final_extension;;
      if (file_exists($upload_path_dest)) {
        $append_int = 1;
        $new_file_basename = $file_basename.'-'.$append_int;
        $new_file_path_dest = 'media/'.$upload_location.'/'.$new_file_basename.'.'.$final_extension;
        while (file_exists($new_file_path_dest)) {
          $new_file_basename = $file_basename.'-'.$append_int;
          $new_file_path_dest = 'media/'.$upload_location.'/'.$new_file_basename.'.'.$final_extension;
          $append_int++; // Increment our appendage
        }
        // Reset our values
        $file_basename = $new_file_basename;
        $file_name = $new_file_basename.'.'.$file_extension;
      }
      // Final file name
      $file_path_dest = $upload_dir.$file_name;
      // Upload the file and check in one command
      if (move_uploaded_file($temp_file, $file_path_dest)) {

        // Linux process
        switch ($upload_type) {
          case 'img':
            shell_exec('./bash.imageprocess.sh '.$file_basename.' '.$file_extension.' '.$img_orientation.' '.$img_xs.' '.$img_sm.' '.$img_md.' '.$img_lg.' '.$img_xl);
            // Correct non-accepted conversions
            $file_mime = ($file_extension == 'bmp') ? 'image/png' : $file_mime;
            $file_extension = ($file_extension == 'bmp') ? 'png' : $file_extension;
            // Duration
            $file_duration = '';

          break;
          case 'svg':
            shell_exec('./bash.imageprocess.sh '.$file_basename.' '.$file_extension.' svg');
            // Duration
            $file_duration = '';

          break;
          case 'vid':
            shell_exec('./bash.videoprocess.sh '.$file_basename.' '.$file_extension);
            // Correct non-accepted conversions
            $file_mime = ($file_extension == 'flv') ? 'video/mp4' : $file_mime;
            $file_extension = ($file_extension == 'flv') ? 'mp4' : $file_extension;
            $file_mime = ($file_extension == 'avi') ? 'video/mp4' : $file_mime;
            $file_extension = ($file_extension == 'avi') ? 'mp4' : $file_extension;
            $file_mime = ($file_extension == 'mkv') ? 'video/mp4' : $file_mime;
            $file_extension = ($file_extension == 'mkv') ? 'mp4' : $file_extension;
            $file_mime = ($file_extension == 'mov') ? 'video/mp4' : $file_mime;
            $file_extension = ($file_extension == 'mov') ? 'mp4' : $file_extension;
            // Duration
            $file_duration = shell_exec('./bash.duration.sh '.$file_basename.' '.$file_extension);
            $file_duration = trim($file_duration); // Remove a new line at the end of BASH output

          break;
          case 'aud':
            shell_exec('./bash.audioprocess.sh '.$file_basename.' '.$file_extension);
            // Duration
            $file_duration = shell_exec('./bash.duration.sh '.$file_basename.' '.$file_extension);
            $file_duration = trim($file_duration); // Remove a new line at the end of BASH output

          break;
          case 'doc':
            shell_exec('./bash.documprocess.sh '.$file_basename.' '.$file_extension.' pdf');
            // Duration
            $file_duration = '';

          break;
        }

        // Correct non-accepted conversions
        $file_mime = ($file_extension == 'bmp') ? 'image/png' : $file_mime;
        $file_extension = ($file_extension == 'bmp') ? 'png' : $file_extension;
        $file_mime = ($file_extension == 'flv') ? 'video/mp4' : $file_mime;
        $file_extension = ($file_extension == 'flv') ? 'mp4' : $file_extension;
        $file_mime = ($file_extension == 'avi') ? 'video/mp4' : $file_mime;
        $file_extension = ($file_extension == 'avi') ? 'mp4' : $file_extension;
        $file_mime = ($file_extension == 'mkv') ? 'video/mp4' : $file_mime;
        $file_extension = ($file_extension == 'mkv') ? 'mp4' : $file_extension;
        $file_mime = ($file_extension == 'mov') ? 'video/mp4' : $file_mime;
        $file_extension = ($file_extension == 'mov') ? 'mp4' : $file_extension;

        // SQL entry
        $query = $database->prepare("INSERT INTO media_library (size, mime_type, basic_type, location, file_base, file_extension, duration)
                VALUES (:size, :mime_type, :basic_type, :location, :file_base, :file_extension, :duration)");
        $query->bindParam(':size', $file_size);
        $query->bindParam(':mime_type', $file_mime);
        $query->bindParam(':basic_type', $basic_type);
        $query->bindParam(':location', $upload_location);
        $query->bindParam(':file_base', $file_basename);
        $query->bindParam(':file_extension', $file_extension);
        $query->bindParam(':duration', $file_duration);
        $pdo->exec_($query);
        if (!$pdo->ok) {
          $errors .= '<span class="error">SQL error</span><br><br>';
          // Show our $errors
          echo $errors;
        } else {
          // Get the new SQL entry ID
          $m_id  = $pdo->lastid;

          // Add images to the imgaes table
          if ($upload_type == 'img') {
            $query = $database->prepare("INSERT INTO media_library (m_id, orientation, width, height, xs, sm, md, lg, xl)
                    VALUES (:m_id, :orientation, :width, :height, :xs, :sm, :md, :lg, :xl)");
            $query->bindParam(':m_id', $m_id);
            $query->bindParam(':orientation', $img_orientation);
            $query->bindParam(':width', $img_w);
            $query->bindParam(':height', $img_h);
            $query->bindParam(':xs', $img_xs);
            $query->bindParam(':sm', $img_sm);
            $query->bindParam(':md', $img_md);
            $query->bindParam(':lg', $img_lg);
            $query->bindParam(':xl', $img_xl);
            $pdo->exec_($query);
            if (!$pdo->ok) {
              $errors .= '<span class="error">SQL image error</span><br><br>';
              // Show our $errors
              echo $errors;
            }
          }

          $info_message .= '<span class="upload-info">File size: <code>'.$file_size_pretty.'</code></span><br id="mediatype_'.$m_id.'">'; // We need id="mediatype_..." so JS has something to change and it doesn't break
          $info_message .= '<span class="upload-info">File name: <code id="filename_'.$m_id.'">'.$file_basename.'.'.$file_extension.'</code></span>';

          // Wrap the info in a paragraph
          $info_message = '<p id="upload_'.$m_id.'" class="blue">'.$info_message.'</p>';

          // AJAX mediaEdit button (calls a JS function already loaded by medialibrary.php, can't load that JS here)
          $edit_form = '
          <form id="mediaEdit_'.$m_id.'">
            <input type="hidden" value="'.$m_id.'" name="m_id">
            <button type="button" class="postform link-button inline orange" onclick="mediaEdit(\'mediaEdit_'.$m_id.'\', \'ajax.mediainfo.php\', \'media-editor\');">edit</button>
          </form>';

          // AJAX-send the success message and edit link
          echo $edit_form.$info_message.'<hr>';
        }

      } else {
        $errors .= '<span class="error">Upload error</span><hr>';
        // Show our $errors
        echo $errors;
      }
    }

} elseif ( ($_SERVER['REQUEST_METHOD'] === 'POST') && (isset($_SESSION['user_id'])) ) {
  $errors = '<span class="error">Upload error, files may be empty, too large, or in raw data format, such as raw FLAC audio files.</span><hr>';
  // Show our $errors
  echo $errors;
} else { // End POST check
  exit ();
}

?>
