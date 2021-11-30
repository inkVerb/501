<?php

// Featured media
$feat_file_basepath = 'media/';
// Featured image filename
$query = $database->prepare("SELECT file_base, file_extension, alt_text, location FROM media_library WHERE id=:id AND basic_type='IMAGE'");
$query->bindParam(':id', $p_feat_img);
$rows = $pdo->exec_($query);
// Shoule be 1 row
if ($pdo->numrows == 1) {
  foreach ($rows as $row) {
    // Assign the values
    $feat_img_id = $p_feat_img;
    $feat_img_file = "$row->file_base"."."."$row->file_extension";
    $feat_img_thumb = ($row->file_extension == 'svg') ? "$row->file_base".'_thumb_svg.png' : "$row->file_base".'_thumb.'."$row->file_extension";
    $feat_img_file_alt = "$row->alt_text";
    $feat_img_file_location = "$row->location";
    $feat_img_url = $feat_file_basepath.$feat_img_file_location.'/'.$feat_img_file;
    $feat_img_file_link = '<a href="'.$feat_img_url.'" target="_blank" style="text-decoration:none;">'."<b>$feat_img_file</b>".'</a>';
    $feat_img_showhide = 'inline';
    $feat_img_thumb_showhide = 'block';
  }
} else {
  $feat_img_id = 0;
  $feat_img_file = 'none';
  $feat_img_file_link = '<i class="gray">'.$feat_img_file.'</i>';
  $feat_img_showhide = 'none';
  $feat_img_thumb_showhide = 'none';
}

// Featured audio filename
$query = $database->prepare("SELECT file_base, file_extension, location FROM media_library WHERE id=:id AND basic_type='AUDIO'");
$query->bindParam(':id', $p_feat_aud);
$rows = $pdo->exec_($query);
// Shoule be 1 row
if ($pdo->numrows == 1) {
  foreach ($rows as $row) {
    // Assign the values
    $feat_aud_id = $p_feat_aud;
    $feat_aud_file = "$row->file_base"."."."$row->file_extension";
    $feat_aud_file_location = "$row->location";
    $feat_aud_url = $feat_file_basepath.$feat_aud_file_location.'/'.$feat_aud_file;
    $feat_aud_file_link = '<a href="'.$feat_aud_url.'" target="_blank" style="text-decoration:none;">'."<b>$feat_aud_file</b>".'</a>';
    $feat_aud_showhide = 'inline';
  }
} else {
  $feat_aud_id = 0;
  $feat_aud_file = 'none';
  $feat_aud_file_link = '<i class="gray">'.$feat_aud_file.'</i>';
  $feat_aud_showhide = 'none';
}

// Featured video filename
$query = $database->prepare("SELECT file_base, file_extension, location FROM media_library WHERE id=:id AND basic_type='VIDEO'");
$query->bindParam(':id', $p_feat_vid);
$rows = $pdo->exec_($query);
// Shoule be 1 row
if ($pdo->numrows == 1) {
  foreach ($rows as $row) {
    // Assign the values
    $feat_vid_id = $p_feat_vid;
    $feat_vid_file = "$row->file_base"."."."$row->file_extension";
    $feat_vid_file_location = "$row->location";
    $feat_vid_url = $feat_file_basepath.$feat_vid_file_location.'/'.$feat_vid_file;
    $feat_vid_file_link = '<a href="'.$feat_vid_url.'" target="_blank" style="text-decoration:none;">'."<b>$feat_vid_file</b>".'</a>';
    $feat_vid_showhide = 'inline';
  }
} else {
  $feat_vid_id = 0;
  $feat_vid_file = 'none';
  $feat_vid_file_link = '<i class="gray">'.$feat_vid_file.'</i>';
  $feat_vid_showhide = 'none';
}

// Featured document filename
$query = $database->prepare("SELECT file_base, file_extension, location FROM media_library WHERE id=:id AND basic_type='DOCUMENT'");
$query->bindParam(':id', $p_feat_doc);
$rows = $pdo->exec_($query);
// Shoule be 1 row
if ($pdo->numrows == 1) {
  foreach ($rows as $row) {
    // Assign the values
    $feat_doc_id = $p_feat_doc;
    $feat_doc_file = "$row->file_base"."."."$row->file_extension";
    $feat_doc_file_location = "$row->location";
    $feat_doc_url = $feat_file_basepath.$feat_doc_file_location.'/'.$feat_doc_file;
    $feat_doc_file_link = '<a href="'.$feat_doc_url.'" target="_blank" style="text-decoration:none;">'."<b>$feat_doc_file</b>".'</a>';
    $feat_doc_showhide = 'inline';
  }
} else {
  $feat_doc_id = 0;
  $feat_doc_file = 'none';
  $feat_doc_file_link = '<i class="gray">'.$feat_doc_file.'</i>';
  $feat_doc_showhide = 'none';
}
