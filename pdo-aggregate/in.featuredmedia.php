<?php

// This sets variables for Featured Media
// This must go in edit.php, immediately after in.editprocess.php
// Dependent int variables: $p_feat_img, $p_feat_aud, $p_feat_vid, $p_feat_doc

// Featured media
$media_library_folder = 'media';
$img_thum_max = '50px';
$img_blog_max = '100px';
$vid_blog_max = '250px';
$feat_file_basepath = $blog_web_base.'/'.$media_library_folder.'/';

// Featured image
if (filter_var($p_feat_img, FILTER_VALIDATE_INT, array('min_range' => 1))) {
  $query = $database->prepare("SELECT file_base, file_extension, mime_type, title_text, alt_text, location FROM media_library WHERE id=:id AND basic_type='IMAGE'");
  $query->bindParam(':id', $p_feat_img);
  $rows = $pdo->exec_($query);
  // Shoule be 1 row
  if ($pdo->numrows == 1) {
    foreach ($rows as $row) {
      // Assign the values
      $feat_img_id = $p_feat_img;
      $feat_img_ext = "$row->file_extension";
      $feat_img_mime = "$row->mime_type";
      $feat_img_file = "$row->file_base".".".$feat_img_ext;
      $feat_img_thumb = ($row->file_extension == 'svg') ? "$row->file_base".'_thumb_svg.png' : "$row->file_base".'_thumb.'."$row->file_extension";
      $feat_img_file_title = "$row->title_text";
      $feat_img_file_alt = "$row->alt_text";
      $feat_img_file_location = "$row->location";
      $feat_img_rel_url = $media_library_folder.'/'.$feat_img_file_location.'/'.$feat_img_file;
      $feat_img_url = $feat_file_basepath.$feat_img_file_location.'/'.$feat_img_file;
      $feat_img_url_blog = ($feat_img_ext == 'svg') ? $feat_file_basepath.$feat_img_file_location.'/'.$feat_img_thumb : $feat_img_url; // SVG files don't scale with <img> size attributes
      $feat_img_file_link = '<a href="'.$feat_img_url.'" target="_blank" style="text-decoration:none;">'."<b>$feat_img_file</b>".'</a>';
      $feat_img_showhide = 'inline';
      $feat_img_thumb_showhide = 'block';
      $feat_img_file_size = filesize($feat_img_rel_url);
    }
  } else {
    $feat_img_id = 0;
    $feat_img_file = 'none';
    $feat_img_file_link = '<i class="gray">'.$feat_img_file.'</i>';
    $feat_img_showhide = 'none';
    $feat_img_thumb_showhide = 'none';
  }
} elseif (filter_var($p_feat_img,FILTER_VALIDATE_URL)) {
  $feat_img_id = 'aggregated';
  $feat_img_file = 'aggregated';
  $feat_img_file_link = '<i class="gray">'.$feat_img_file.'</i>';
  $feat_img_showhide = 'aggregated';
  $feat_img_thumb_showhide = 'inline';
  $feat_img_url = $p_feat_img;
} else {
  $feat_img_id = 0;
  $feat_img_file = 'none';
  $feat_img_file_link = '<i class="gray">'.$feat_img_file.'</i>';
  $feat_img_showhide = 'none';
  $feat_img_thumb_showhide = 'none';
}

// Featured audio
if (filter_var($p_feat_aud, FILTER_VALIDATE_INT, array('min_range' => 1))) {
  $query = $database->prepare("SELECT file_base, file_extension, mime_type, location, duration FROM media_library WHERE id=:id AND basic_type='AUDIO'");
  $query->bindParam(':id', $p_feat_aud);
  $rows = $pdo->exec_($query);
  // Shoule be 1 row
  if ($pdo->numrows == 1) {
    foreach ($rows as $row) {
      // Assign the values
      $feat_aud_id = $p_feat_aud;
      $feat_aud_ext = "$row->file_extension";
      $feat_aud_mime = "$row->mime_type";
      $feat_aud_file = "$row->file_base".".".$feat_aud_ext;
      $feat_aud_file_location = "$row->location";
      $feat_aud_rel_url = $media_library_folder.'/'.$feat_aud_file_location.'/'.$feat_aud_file;
      $feat_aud_url = $feat_file_basepath.$feat_aud_file_location.'/'.$feat_aud_file;
      $feat_aud_file_link = '<a href="'.$feat_aud_url.'" target="_blank" style="text-decoration:none;">'."<b>$feat_aud_file</b>".'</a>';
      $feat_aud_showhide = 'inline';
      $feat_aud_file_size = filesize("$feat_aud_rel_url");
      $feat_aud_duration = "$row->duration";
    }
  } else {
    $feat_aud_id = 0;
    $feat_aud_file = 'none';
    $feat_aud_file_link = '<i class="gray">'.$feat_aud_file.'</i>';
    $feat_aud_showhide = 'none';
  }
} elseif (filter_var($p_feat_aud,FILTER_VALIDATE_URL)) {
  $feat_aud_id = 'aggregated';
  $feat_aud_file = 'aggregated';
  $feat_aud_file_link = '<i class="gray">'.$feat_aud_file.'</i>';
  $feat_aud_showhide = 'inline';
  $feat_aud_url = $p_feat_aud;
  $feat_aud_ext = pathinfo($feat_aud_url, PATHINFO_EXTENSION);
  if ($feat_aud_ext == 'mp3') {$feat_aud_mime = 'audio/mpeg';} elseif ($feat_aud_ext == 'ogg') {$feat_aud_mime = 'audio/ogg';} else {$feat_aud_mime = 'aggregated';}
} else {
  $feat_aud_id = 0;
  $feat_aud_file = 'none';
  $feat_aud_file_link = '<i class="gray">'.$feat_aud_file.'</i>';
  $feat_aud_showhide = 'none';
}

// Featured video
if (filter_var($p_feat_vid, FILTER_VALIDATE_INT, array('min_range' => 1))) {
  $query = $database->prepare("SELECT file_base, file_extension, mime_type, location, duration FROM media_library WHERE id=:id AND basic_type='VIDEO'");
  $query->bindParam(':id', $p_feat_vid);
  $rows = $pdo->exec_($query);
  // Shoule be 1 row
  if ($pdo->numrows == 1) {
    foreach ($rows as $row) {
      // Assign the values
      $feat_vid_id = $p_feat_vid;
      $feat_vid_ext = "$row->file_extension";
      $feat_vid_mime = "$row->mime_type";
      $feat_vid_file = "$row->file_base".".".$feat_vid_ext;
      $feat_vid_file_location = "$row->location";
      $feat_vid_rel_url = $media_library_folder.'/'.$feat_vid_file_location.'/'.$feat_vid_file;
      $feat_vid_url = $feat_file_basepath.$feat_vid_file_location.'/'.$feat_vid_file;
      $feat_vid_file_link = '<a href="'.$feat_vid_url.'" target="_blank" style="text-decoration:none;">'."<b>$feat_vid_file</b>".'</a>';
      $feat_vid_showhide = 'inline';
      $feat_vid_file_size = filesize($feat_vid_rel_url);
      $feat_vid_duration = "$row->duration";
    }
  } else {
    $feat_vid_id = 0;
    $feat_vid_file = 'none';
    $feat_vid_file_link = '<i class="gray">'.$feat_vid_file.'</i>';
    $feat_vid_showhide = 'none';
  }
} elseif (filter_var($p_feat_vid,FILTER_VALIDATE_URL)) {
  $feat_vid_id = 'aggregated';
  $feat_vid_file = 'aggregated';
  $feat_vid_file_link = '<i class="gray">'.$feat_vid_file.'</i>';
  $feat_vid_showhide = 'inline';
  $feat_vid_url = $p_feat_vid;
  $feat_vid_ext = pathinfo($p_feat_vid, PATHINFO_EXTENSION);
  if ($feat_vid_ext == 'mp4') {$feat_vid_mime = 'video/mp4';} elseif ($feat_vid_ext == 'ogg') {$feat_vid_mime = 'video/ogg';} else {$feat_vid_mime = 'aggregated';}
} else {
  $feat_vid_id = 0;
  $feat_vid_file = 'none';
  $feat_vid_file_link = '<i class="gray">'.$feat_vid_file.'</i>';
  $feat_vid_showhide = 'none';
}

// Featured document
if (filter_var($p_feat_doc, FILTER_VALIDATE_INT, array('min_range' => 1))) {
  $query = $database->prepare("SELECT file_base, file_extension, mime_type, location FROM media_library WHERE id=:id AND basic_type='DOCUMENT'");
  $query->bindParam(':id', $p_feat_doc);
  $rows = $pdo->exec_($query);
  // Shoule be 1 row
  if ($pdo->numrows == 1) {
    foreach ($rows as $row) {
      // Assign the values
      $feat_doc_id = $p_feat_doc;
      $feat_doc_ext = "$row->file_extension";
      $feat_doc_mime = "$row->mime_type";
      $feat_doc_file = "$row->file_base".".".$feat_doc_ext;
      $feat_doc_file_location = "$row->location";
      $feat_doc_rel_url = $media_library_folder.'/'.$feat_doc_file_location.'/'.$feat_doc_file;
      $feat_doc_url = $feat_file_basepath.$feat_doc_file_location.'/'.$feat_doc_file;
      $feat_doc_file_link = '<a href="'.$feat_doc_url.'" target="_blank" style="text-decoration:none;">'."<b>$feat_doc_file</b>".'</a>';
      $feat_doc_showhide = 'inline';
      $feat_doc_file_size = filesize($feat_doc_rel_url);
    }
  } else {
    $feat_doc_id = 0;
    $feat_doc_file = 'none';
    $feat_doc_file_link = '<i class="gray">'.$feat_doc_file.'</i>';
    $feat_doc_showhide = 'none';
  }
} elseif (filter_var($p_feat_doc,FILTER_VALIDATE_URL)) {
  $feat_doc_id = 'aggregated';
  $feat_doc_file = 'aggregated';
  $feat_doc_url = $p_feat_doc;
  $feat_doc_file_link = '<a href="'.$feat_doc_url.'" target="_blank" style="text-decoration:none;">'."<b>Click to open remote file</b>".'</a>';
  $feat_doc_showhide = 'inline';
} else {
  $feat_doc_id = 0;
  $feat_doc_file = 'none';
  $feat_doc_file_link = '<i class="gray">'.$feat_doc_file.'</i>';
  $feat_doc_showhide = 'none';
}
