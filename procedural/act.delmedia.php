<?php

// Include our config (with SQL) up near the top of our PHP file
include ('./in.db.php');

// Must be logged in
if (!isset($_SESSION['user_id'])) {
  exit (header("Location: blog.php"));
}

// Delete
if ($_POST['deleteaction'] == 'confirm delete forever') {
  unset($_POST['deleteaction']);
  foreach ($_POST as $m_id) {

    // Validate
    if (!filter_var($m_id, FILTER_VALIDATE_INT)) {
      continue;
    }

    // Get the file name from the database
    $query = "SELECT file_base, basic_type, file_extension, location FROM media_library WHERE id='$m_id'";
    $call = mysqli_query($database, $query);

    while ($row = mysqli_fetch_array($call, MYSQLI_NUM)) {
      $m_file_base = "$row[0]";
      $m_basic_type = "$row[1]";
      $m_file_extension = "$row[2]";
      $m_location = "$row[3]";

      // File & conversion links
      $basepath = 'media/';
      $origpath = 'media/original/';
      $deleted = true; // Start our tests out right
      switch ($m_basic_type) {
        case 'IMAGE':
          if ($m_file_extension == 'svg') {
            $thumb = $basepath.$m_location.'/'.$m_file_base.'_thumb_svg.'.$m_file_extension;
            $img_svg = $basepath.$m_location.'/'.$m_file_base.'.'.$m_file_extension;

            // Delete & set variable accordingly, one error and it will read false
            unlink($thumb); $deleted = ( (!file_exists($thumb)) && ($deleted != false) ) ? true : false;
            unlink($img_svg); $deleted = ( (!file_exists($img_svg)) && ($deleted != false) ) ? true : false;
          } else {
            $img_th = $basepath.$m_location.'/'.$m_file_base.'_thumb.'.$m_file_extension;
            $img_xs = $basepath.$m_location.'/'.$m_file_base.'_154.'.$m_file_extension;
            $img_sm = $basepath.$m_location.'/'.$m_file_base.'_484.'.$m_file_extension;
            $img_md = $basepath.$m_location.'/'.$m_file_base.'_800.'.$m_file_extension;
            $img_lg = $basepath.$m_location.'/'.$m_file_base.'_1280.'.$m_file_extension;
            $img_xl = $basepath.$m_location.'/'.$m_file_base.'_1920.'.$m_file_extension;
            $img_fl = $basepath.$m_location.'/'.$m_file_base.'.'.$m_file_extension;
            $img_or = $origpath.$m_location.'/'.$m_file_base.'.'.$m_file_extension;

            // Some of these might not necessarily exist, depending on image size, smaller first
            unlink($img_th); $deleted = ( (!file_exists($img_th)) && ($deleted != false) ) ? true : false;
            unlink($img_xs); $deleted = ( (!file_exists($img_xs)) && ($deleted != false) ) ? true : false;
            unlink($img_sm); $deleted = ( (!file_exists($img_sm)) && ($deleted != false) ) ? true : false;
            unlink($img_md); $deleted = ( (!file_exists($img_md)) && ($deleted != false) ) ? true : false;
            unlink($img_lg); $deleted = ( (!file_exists($img_lg)) && ($deleted != false) ) ? true : false;
            unlink($img_xl); $deleted = ( (!file_exists($img_xl)) && ($deleted != false) ) ? true : false;

            // Delete & set variable accordingly, one error and it will read false
            unlink($img_fl); $deleted = ( (!file_exists($img_fl)) && ($deleted != false) ) ? true : false;
            unlink($img_or); $deleted = ( (!file_exists($img_or)) && ($deleted != false) ) ? true : false;
          }
        break;
        case 'VIDEO':
          $vid_web = $basepath.$m_location.'/'.$m_file_base.'.'.$m_file_extension;
          $vid_ori = $origpath.$m_location.'/'.$m_file_base.'.'.$m_file_extension;

          // Delete & set variable accordingly, one error and it will read false
          unlink($vid_web); $deleted = ( (!file_exists($vid_web)) && ($deleted != false) ) ? true : false;
          unlink($vid_ori); $deleted = ( (!file_exists($vid_ori)) && ($deleted != false) ) ? true : false;
        break;
        case 'AUDIO':
          $aud_web = $basepath.$m_location.'/'.$m_file_base.'.mp3';
          $aud_ori = $origpath.$m_location.'/'.$m_file_base.'.'.$m_file_extension;

          // Delete & set variable accordingly, one error and it will read false
          unlink($aud_web); $deleted = ( (!file_exists($aud_web)) && ($deleted != false) ) ? true : false;
          unlink($aud_ori); $deleted = ( (!file_exists($aud_ori)) && ($deleted != false) ) ? true : false;
        break;
        case 'DOCUMENT':
          if ( ($m_file_extension == 'txt') || ($m_file_extension == 'doc') ) {
            $doc_web = $basepath.$m_location.'/'.$m_file_base.'.'.$m_file_extension;

            // Delete & set variable accordingly, one error and it will read false
            unlink($doc_web); $deleted = ( (!file_exists($doc_web)) && ($deleted != false) ) ? true : false;
          } else {
            $doc_web = $basepath.$m_location.'/'.$m_file_base.'.pdf';
            $doc_ori = $origpath.$m_location.'/'.$m_file_base.'.'.$m_file_extension;

            // Delete & set variable accordingly, one error and it will read false
            unlink($doc_web); $deleted = ( (!file_exists($doc_web)) && ($deleted != false) ) ? true : false;
            unlink($doc_ori); $deleted = ( (!file_exists($doc_ori)) && ($deleted != false) ) ? true : false;
          }
        break;

      } // Mimetype switch

      // Delete the file
      if ( $deleted == true ) {

        // Delete the database entry
        $query = "DELETE FROM media_library WHERE id='$m_id'";
        $call = mysqli_query($database, $query);

        if ( ($call) && ($m_basic_type == 'IMAGE') ) {
          $query = "DELETE FROM media_images WHERE m_id='$m_id'";
          $call = mysqli_query($database, $query);
        }

        // Check SQL
        if (!$call) { // Both $call statements will stack and be tested here either way
          exit ('<pre class="error">Could not delete file from database: '.$m_location.'/'.$m_file_base.'.'.$m_file_extension.' id: '.$m_id.'</pre>');
        }

      // Check file system
      } else {
        exit ('<pre class="error">Could not delete file from server: '.$m_location.'/'.$m_file_base.'.'.$m_file_extension.' id: '.$m_id.'</pre>');
      }

    } // End file

  } // for loop

  // Done, go home
  exit (header("Location: medialibrary.php"));

// Fail, get out of here
} else {
  exit (header("Location: blog.php"));
}

?>
