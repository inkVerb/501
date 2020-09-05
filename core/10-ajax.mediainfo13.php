<?php

// Include our config (with SQL) up near the top of our PHP file
include ('./in.config.php');

// Check & validate for what we need
if ( ($_SERVER['REQUEST_METHOD'] === 'POST') && (!empty($_POST['m_id'])) && (filter_var($_POST['m_id'], FILTER_VALIDATE_INT)) && (isset($_SESSION['user_id'])) ) {

  // Assign the media ID and sanitize as the same time
  $m_id = preg_replace("/[^0-9]/"," ", $_POST['m_id']);

  // File name change
  if ( (isset($_POST['name_change'])) && (isset($_POST['save_file_name'])) ) {

    // Create our AJAX response array
    $ajax_response = array();

    // Get the old file name
    $query = "SELECT file_base, file_extension, location FROM media_library WHERE id='$m_id'";
    $call = mysqli_query($database, $query);
    // Shoule be 1 row
    if (mysqli_num_rows($call) == 1) {
      // Assign the values
      $row = mysqli_fetch_array($call, MYSQLI_NUM);
        $m_old_file_base = "$row[0]";
        $m_old_file_extension = "$row[1]";
        $m_file_location = "$row[2]";
        $m_old_file_name = $m_old_file_base.'.'.$m_old_file_extension;
      }

    if (!file_exists("media/$m_file_location/$m_old_file_name")) {
      $ajax_response['message'] = '<span class="error notehide">Error!</span>';

      // We're done here
      $json_response = json_encode($ajax_response, JSON_FORCE_OBJECT);
      echo $json_response;
      exit();
    }

    // Assign and sanitize
    $regex_replace = "/[^a-zA-Z0-9-_]/";
    $m_new_file_base = preg_replace($regex_replace,"-", $_POST['save_file_name']); // Lowercase, all non-alnum to hyphen

    // SQL
    $m_new_file_base_sqlesc = escape_sql($m_new_file_base);
    $query = "UPDATE media_library SET file_base='$m_new_file_base_sqlesc', date_updated=NOW() WHERE id='$m_id'";
    $call = mysqli_query($database, $query);
    if ($call) {
      $ajax_response['message'] = '<span class="green notehide">Saved</span>';
      $ajax_response['file_name'] = "$m_new_file_base.$m_old_file_extension";

      // Change the actual file name
      rename("media/$m_file_location/$m_old_file_name","media/$m_file_location/$m_new_file_base.$m_old_file_extension");

    } else {
      $ajax_response['message'] = '<span class="error notehide">Error!</span>';
    }

    // We're done here
    $json_response = json_encode($ajax_response, JSON_FORCE_OBJECT);
    echo $json_response;

  // Save media info
  } elseif ( (isset($_POST['media_edit_save'])) && (isset($_POST['title_text'])) && (isset($_POST['alt_text'])) ) {

    // Create our AJAX response array
    $ajax_response = array();

    // Assign and sanitize img attributes
    $title_text = htmlspecialchars($_POST['title_text']);
    $alt_text = htmlspecialchars($_POST['alt_text']);

    // SQL
    $title_text_sqlesc = escape_sql($title_text);
    $alt_text_sqlesc = escape_sql($alt_text);
    $query = "UPDATE media_library SET title_text='$title_text_sqlesc', alt_text='$alt_text_sqlesc', date_updated=NOW() WHERE id='$m_id'";
    $call = mysqli_query($database, $query);
    if ($call) {
      $ajax_response['message'] = '<span class="green notehide">Saved</span>';
    } else {
      $ajax_response['message'] = '<span class="error notehide">Error!</span>';
    }

    // We're done here
    $json_response = json_encode($ajax_response, JSON_FORCE_OBJECT);
    echo $json_response;

  // mediaEdit AJAX loader
  } else {

    // Get the media item info from the database
    $query = "SELECT size, mime_type, basic_type, file_base, file_extension, title_text, alt_text FROM media_library WHERE id='$m_id'";
    $call = mysqli_query($database, $query);
    // Shoule be 1 row
    if (mysqli_num_rows($call) == 1) {
      // Assign the values
      $row = mysqli_fetch_array($call, MYSQLI_NUM);
        $m_size = "$row[0]";
        $m_mime_type = "$row[1]";
        $m_basic_type = "$row[2]";
        $m_file_base = "$row[3]";
        $m_file_extension = "$row[4]";
        $m_title_text = "$row[5]";
        $m_alt_text = "$row[6]";
    } else {
      echo '<h1 id="media-editor-content" class="error">Error!</h1>
      <div id="media-editor-closer" onclick="mediaEditorClose();" title="close">&#xd7;</div>
      <p class="error">Database error: Media item not found.</p>';
      exit();
    }

    // Assign a "pretty" value for basic media type
    switch ($m_basic_type) {
      case 'IMAGE':
        $media_type_pretty = 'Image: ';
        break;
      case 'VIDEO':
        $media_type_pretty = 'Video: ';
        break;
      case 'AUDIO':
        $media_type_pretty = 'Audio: ';
        break;
      case 'DOCUMENT':
        $media_type_pretty = 'Document: ';
        break;
      default:
        echo '<h1 id="media-editor-content" class="error">Error!</h1>
        <div id="media-editor-closer" onclick="mediaEditorClose();" title="close">&#xd7;</div>
        <p class="error">Database error: Media type is impossible.</p>';
        exit();
    }

    // Assign a "pretty" value for specific media mime type
    switch ($m_mime_type) {
      case 'image/jpeg':
        $media_type_pretty .= 'JPEG';
        break;
      case 'image/png':
        $media_type_pretty .= 'PNG';
        break;
      case 'image/gif':
        $media_type_pretty .= 'GIF';
        break;
      case 'image/svg+xml':
        $media_type_pretty .= 'SVG';
        break;
      case 'video/webm':
        $media_type_pretty .= 'WebM';
        break;
      case 'video/ogg':
        $media_type_pretty .= 'Ogg';
        break;
      case 'video/mp4':
        $media_type_pretty .= 'MP4';
        break;
      case 'audio/mpeg':
        $media_type_pretty .= 'MP3';
        break;
      case 'audio/ogg':
        $media_type_pretty .= 'Ogg';
        break;
      case 'audio/x-wav':
        $media_type_pretty .= 'Waveform';
        break;
      case 'audio/wav':
        $media_type_pretty .= 'Waveform';
        break;
      case 'text/plain':
        $media_type_pretty .= ($m_file_extension == 'md') ? 'Markdown' : 'Text';
        break;
      case 'text/html':
        $media_type_pretty .= 'Raw DOC';
        break;
      case 'application/msword':
        $media_type_pretty .= 'MS Word DOC';
        break;
      case 'application/vnd.openxmlformats-officedocument.wordprocessingml.document':
        $media_type_pretty .= 'MS Word DOCX';
        break;
      case 'application/vnd.oasis.opendocument.text':
        $media_type_pretty .= 'Open Document';
        break;
      case 'application/x-pdf':
        $media_type_pretty .= 'PDF';
        break;
      case 'application/pdf':
        $media_type_pretty .= 'PDF';
        break;
      default:
        echo '<h1 id="media-editor-content" class="error">Error!</h1>
        <div id="media-editor-closer" onclick="mediaEditorClose();" title="close">&#xd7;</div>
        <p class="error">Database error: Media mime type is impossible.</p>';
        exit();
    }

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

    $file_name_pre = '<pre onclick="changeFileName(\''.$m_id.'\', \''.$m_file_base.'\', \''.$m_file_extension.'\');" class="postform blue" title="change file name">'.$m_file_base.'.'.$m_file_extension.'</pre>';

    echo '
      <h1 id="media-editor-content">'.$media_type_pretty.'</h1>
      <div id="media-editor-closer" onclick="mediaEditorClose();" title="close">&#xd7;</div>
      <pre>'.human_file_size($m_size).' ('.$m_size.' bytes)</pre>
      <div id="change-file-name">'.$file_name_pre.'</div>

      <form id="media-edit-form">
        <input type="hidden" value="'.$m_id.'" name="m_id">
        <input type="hidden" value="'.$m_id.'" name="media_edit_save">
        <p>Title: <input type="text" name="title_text" value="'.$m_title_text.'"></p>
        <p>Alt: <input type="text" name="alt_text" value="'.$m_alt_text.'"></p>
        <button type="button" onclick="mediaSave('.$m_id.');">Save</button>
      </form>
    ';

  } // mediaEdit AJAX loader

} else { // End POST check
  header("Location: webapp.php");
  exit();
}

?>
