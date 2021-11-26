<?php

// Include our config (with SQL) up near the top of our PHP file
include ('./in.config.php');
include ('./in.logincheck.php');

// Check & validate for what we need
if ( ($_SERVER['REQUEST_METHOD'] === 'POST') && (!empty($_POST['m_id'])) && (filter_var($_POST['m_id'], FILTER_VALIDATE_INT)) && (isset($_SESSION['user_id'])) ) {

  // Assign the media ID and sanitize as the same time
  $m_id = preg_replace("/[^0-9]/"," ", $_POST['m_id']);

  // File name change
  if ( (isset($_POST['name_change'])) && (isset($_POST['save_file_name'])) ) {

    // Create our AJAX response array
    $ajax_response = array();

    // Get the old file name
    $rows = $pdo->select('media_library', 'id', $m_id, 'file_base, basic_type, file_extension, location');
    // Shoule be 1 row
    if ($pdo->numrows == 1) {
      foreach ($rows as $row){
        // Assign the values
        $m_file_base = "$row->file_base";
        $m_basic_type = "$row->basic_type";
        $m_file_extension = "$row->file_extension";
        $m_location = "$row->location";
      }
    }

    if (!file_exists("media/$m_location/$m_old_file_name")) {
      $ajax_response['message'] = '<span class="error notehide">Error!</span>';

      // We're done here
      $json_response = json_encode($ajax_response, JSON_FORCE_OBJECT);
      echo $json_response;
      exit ();
    }

    // Assign and sanitize
    $regex_replace = "/[^a-zA-Z0-9-_]/";
    $m_file_base_new = preg_replace($regex_replace,"-", $_POST['save_file_name']); // Lowercase, all non-alnum to hyphen

    // SQL
    $m_file_base_new_trim = DB::trimspace($m_file_base_new);
    $query = $database->prepare("UPDATE media_library SET file_base=:file_base, date_updated=NOW() WHERE id=:id");
    $query->bindParam(':file_base', $m_file_base_new_trim);
    $query->bindParam(':id', $m_id);
    $pdo->exec_($query);
    if ($pdo->change) {
      $ajax_response['message'] = '<span class="green notehide">Saved</span>';
      $ajax_response['file_name'] = "$m_file_base_new.$m_file_extension";

      // File & conversion links
      $basepath = 'media/';
      $origpath = 'media/original/';
      $renamed = true; // Start our tests out right
      switch ($m_basic_type) {
        case 'IMAGE':
          if ($m_file_extension == 'svg') {
            // Old name
            $thumb = $basepath.$m_location.'/'.$m_file_base.'_thumb_svg.'.$m_file_extension;
            $img_svg = $basepath.$m_location.'/'.$m_file_base.'.'.$m_file_extension;
            // New name
            $thumb_new = $basepath.$m_location.'/'.$m_file_base_new.'_thumb_svg.'.$m_file_extension;
            $img_svg_new = $basepath.$m_location.'/'.$m_file_base_new.'.'.$m_file_extension;

            // Rename & set variable accordingly, one error and it will read false
            rename($thumb, $thumb_new); $renamed = ( (file_exists($thumb_new)) && ($renamed != false) ) ? true : false;
            rename($img_svg, $img_svg_new); $renamed = ( (file_exists($img_svg_new)) && ($renamed != false) ) ? true : false;
          } else {
            // Old name
            $img_th = $basepath.$m_location.'/'.$m_file_base.'_thumb.'.$m_file_extension;
            $img_xs = $basepath.$m_location.'/'.$m_file_base.'_154.'.$m_file_extension;
            $img_sm = $basepath.$m_location.'/'.$m_file_base.'_484.'.$m_file_extension;
            $img_md = $basepath.$m_location.'/'.$m_file_base.'_800.'.$m_file_extension;
            $img_lg = $basepath.$m_location.'/'.$m_file_base.'_1280.'.$m_file_extension;
            $img_xl = $basepath.$m_location.'/'.$m_file_base.'_1920.'.$m_file_extension;
            $img_fl = $basepath.$m_location.'/'.$m_file_base.'.'.$m_file_extension;
            $img_or = $origpath.$m_location.'/'.$m_file_base.'.'.$m_file_extension;
            // New name
            $img_th_new = $basepath.$m_location.'/'.$m_file_base_new.'_thumb.'.$m_file_extension;
            $img_xs_new = $basepath.$m_location.'/'.$m_file_base_new.'_154.'.$m_file_extension;
            $img_sm_new = $basepath.$m_location.'/'.$m_file_base_new.'_484.'.$m_file_extension;
            $img_md_new = $basepath.$m_location.'/'.$m_file_base_new.'_800.'.$m_file_extension;
            $img_lg_new = $basepath.$m_location.'/'.$m_file_base_new.'_1280.'.$m_file_extension;
            $img_xl_new = $basepath.$m_location.'/'.$m_file_base_new.'_1920.'.$m_file_extension;
            $img_fl_new = $basepath.$m_location.'/'.$m_file_base_new.'.'.$m_file_extension;
            $img_or_new = $origpath.$m_location.'/'.$m_file_base_new.'.'.$m_file_extension;

            // Some of these might not necessarily exist, depending on image size, smaller first
            if (file_exists($img_th)) {rename($img_th, $img_th_new); $renamed = ( (file_exists($img_th_new)) && ($renamed != false) ) ? true : false;}
            if (file_exists($img_xs)) {rename($img_xs, $img_xs_new); $renamed = ( (file_exists($img_xs_new)) && ($renamed != false) ) ? true : false;}
            if (file_exists($img_sm)) {rename($img_sm, $img_sm_new); $renamed = ( (file_exists($img_sm_new)) && ($renamed != false) ) ? true : false;}
            if (file_exists($img_md)) {rename($img_md, $img_md_new); $renamed = ( (file_exists($img_md_new)) && ($renamed != false) ) ? true : false;}
            if (file_exists($img_lg)) {rename($img_lg, $img_lg_new); $renamed = ( (file_exists($img_lg_new)) && ($renamed != false) ) ? true : false;}
            if (file_exists($img_xl)) {rename($img_xl, $img_xl_new); $renamed = ( (file_exists($img_xl_new)) && ($renamed != false) ) ? true : false;}

            // Delete & set variable accordingly, one error and it will read false
            rename($img_fl, $img_fl_new); $renamed = ( (file_exists($img_fl_new)) && ($renamed != false) ) ? true : false;
            rename($img_or, $img_or_new); $renamed = ( (file_exists($img_or_new)) && ($renamed != false) ) ? true : false;
          }
        break;
        case 'VIDEO':
          // Old name
          $vid_web = $basepath.$m_location.'/'.$m_file_base.'.'.$m_file_extension;
          $vid_ori = $origpath.$m_location.'/'.$m_file_base.'.'.$m_file_extension;
          // New name
          $vid_web_new = $basepath.$m_location.'/'.$m_file_base_new.'.'.$m_file_extension;
          $vid_ori_new = $origpath.$m_location.'/'.$m_file_base_new.'.'.$m_file_extension;

          // Delete & set variable accordingly, one error and it will read false
          rename($vid_web, $vid_web_new); $renamed = ( (file_exists($vid_web_new)) && ($renamed != false) ) ? true : false;
          rename($vid_ori, $vid_ori_new); $renamed = ( (file_exists($vid_ori_new)) && ($renamed != false) ) ? true : false;
        break;
        case 'AUDIO':
          // Old name
          $aud_web = $basepath.$m_location.'/'.$m_file_base.'.mp3';
          $aud_ori = $origpath.$m_location.'/'.$m_file_base.'.'.$m_file_extension;
          // New name
          $aud_web_new = $basepath.$m_location.'/'.$m_file_base_new.'.mp3';
          $aud_ori_new = $origpath.$m_location.'/'.$m_file_base_new.'.'.$m_file_extension;

          // Delete & set variable accordingly, one error and it will read false
          rename($aud_web, $aud_web_new); $renamed = ( (file_exists($aud_web_new)) && ($renamed != false) ) ? true : false;
          rename($aud_ori, $aud_ori_new); $renamed = ( (file_exists($aud_ori_new)) && ($renamed != false) ) ? true : false;
        break;
        case 'DOCUMENT':
          if ( ($m_file_extension == 'txt') || ($m_file_extension == 'doc') ) {
            // Old name
            $doc_web = $basepath.$m_location.'/'.$m_file_base.'.'.$m_file_extension;
            // New name
            $doc_web_new = $basepath.$m_location.'/'.$m_file_base_new.'.'.$m_file_extension;

            // Delete & set variable accordingly, one error and it will read false
            rename($doc_web, $doc_web_new); $renamed = ( (file_exists($doc_web_new)) && ($renamed != false) ) ? true : false;
          } else {
            // Old name
            $doc_web = $basepath.$m_location.'/'.$m_file_base.'.pdf';
            $doc_ori = $origpath.$m_location.'/'.$m_file_base_new.'.'.$m_file_extension;
            // New name
            $doc_web_new = $basepath.$m_location.'/'.$m_file_base.'.pdf';
            $doc_ori_new = $origpath.$m_location.'/'.$m_file_base_new.'.'.$m_file_extension;

            // Delete & set variable accordingly, one error and it will read false
            rename($doc_web, $doc_web_new); $renamed = ( (file_exists($doc_web_new)) && ($renamed != false) ) ? true : false;
            rename($doc_ori, $doc_ori_new); $renamed = ( (file_exists($doc_ori_new)) && ($renamed != false) ) ? true : false;
          }
        break;

      } // Mimetype switch

      if ($renamed == false) {
        $ajax_response['message'] = '<span class="error notehide">Could not rename file on server</span>';
      }

    } else {
      $ajax_response['message'] = '<span class="error notehide">Could not file name in database</span>';
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
    $title_text_trim = DB::trimspace($title_text);
    $alt_text_trim = DB::trimspace($alt_text);
    $query = $database->prepare("UPDATE media_library SET title_text=:title_text, alt_text=:alt_text, date_updated=NOW() WHERE id=:id");
    $query->bindParam(':title_text', $title_text_trim);
    $query->bindParam(':alt_text', $alt_text_trim);
    $query->bindParam(':id', $m_id);
    $pdo->exec_($query);
    if ($pdo->change) {
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
    $rows = $pdo->select('media_library', 'id', $m_id, 'size, mime_type, basic_type, file_base, file_extension, title_text, alt_text');
    // Shoule be 1 row
    if ($pdo->numrows == 1) {
      foreach ($rows as $row){
        // Assign the values
        $m_size = "$row->size";
        $m_mime_type = "$row->mime_type";
        $m_basic_type = "$row->basic_type";
        $m_file_base = "$row->file_base";
        $m_file_extension = "$row->file_extension";
        $m_title_text = "$row->title_text";
        $m_alt_text = "$row->alt_text";
      }
    } else {
      echo '<h1 id="media-editor-content" class="error">Error!</h1>
      <div id="media-editor-closer" onclick="mediaEditorClose();" title="close">&#xd7;</div>
      <p class="error">Database error: Media item not found.</p>';
      exit ();
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
        exit ();
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
      case 'audio/x-flac':
        $media_type_pretty .= 'FLAC';
        break;
      case 'audio/flac':
        $media_type_pretty .= 'FLAC';
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
        exit ();
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
  exit ();
}

?>
