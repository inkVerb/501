<?php

// Include our config (with SQL) up near the top of our PHP file
include ('./in.config.php');
include ('./in.logincheck.php');

// A few checks to make sure a user actually requested this
if ( ($_SERVER['REQUEST_METHOD'] === 'POST')
&& (!empty($_POST['u_id']))
&& (filter_var($_POST['u_id'], FILTER_VALIDATE_INT))
&& (isset($_SESSION['user_id']))
&& ($_SESSION['user_id'] == $_POST['u_id']) ) {

?>

<!-- AJAX mediaEdit container: media editor will CSS-float inside this -->
<div id="media-insert-editor-container" style="display:none;">
  <!-- AJAX mediaEdit HTML entity -->
  <div id="media-insert-editor"></div>
</div>

<!-- Iterate through each item in the media libary -->
  <?php

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

  // Get and display each item
  $query = "SELECT id, file_base, file_extension, basic_type, location, size, alt_text FROM media_library ORDER BY id DESC";
  $call = mysqli_query($database, $query);

  // Is anything there?
  if (mysqli_num_rows($call) == 0) {

    echo '<div style="display:block;clear:both;"><p style="display:inline;">Nothing yet. Upload a file to add to your Media Library.</p></div>';

  } else {

    // Simple line
    echo '&nbsp;<div id="media-insert-editor-saved-message" style="display:inline;"></div><br>';

    // Start our HTML table
    echo '
    <table class="contentlib" id="media-insert-table">
      <tbody>
        <tr>
        <td width="20%"></td>
        <td width="20%"></td>
        <td width="60%"></td>
        </tr>
    ';

    // Start our row colors
    $table_row_color = 'blues';
    // We have many entries, this will iterate one post per each
    while ($row = mysqli_fetch_array($call, MYSQLI_NUM)) {
      // Assign the values
      $m_id = "$row[0]";
      $m_file_base = "$row[1]";
      $m_file_extension = "$row[2]";
      $m_basic_type = "$row[3]";
      $m_location = "$row[4]";
      $m_size = "$row[5]";
      $m_alt = "$row[6]";

      // Proper filename
      $m_filename = $m_file_base.'.'.$m_file_extension;

      // Use our handy function
      $m_size_pretty = human_file_size($m_size);

      // Start our row
      echo '<tr class="'.$table_row_color.'" onmouseover="showActions('.$m_id.')" onmouseout="showActions('.$m_id.')">';

      // AJAX mediaEdit button
      $ajax_edit = '<form id="mediaEdit_'.$m_id.'">
          <input type="hidden" value="'.$m_id.'" name="m_id">
          <div id="showaction'.$m_id.'" style="display: none;">
            <button type="button" class="postform link-button inline orange" onclick="mediaEdit(\'mediaEdit_'.$m_id.'\', \'ajax.mediainfo.php\', \'media-insert-editor\');"><small>edit</small></button>
          </div>
        </form>';

      // Fill-in the row per media type
      $basepath = 'media/';
      $origpath = 'media/original/';
      switch ($m_basic_type) {
        case 'IMAGE':
          if ($m_file_extension == 'svg') {

            // Use the .png thumbnail because the .svg file is likely larger than this 50px .png
            $thumb = '<img max-width="50px" max-height="50px" alt="'.$m_alt.'" src="'.$basepath.$m_location.'/'.$m_file_base.'_thumb_svg.png">';

            // Thumbnail
            echo '<td class="media-lib-thumb"><div class="media-lib-thumb" id="mediatype_'.$m_id.'">'.$thumb.'</div></td>';

            // Filename
            echo '<td><pre><small id="filename_'.$m_id.'">'.$m_filename.'</small></pre>'.$ajax_edit.'</td>';

            // Info
            echo '<td class="media-lib-info">';

            $img_svg = $basepath.$m_location.'/'.$m_file_base.'.'.$m_file_extension;

            // Set links
            $img_svg_link = (file_exists($img_svg)) ? '<a href="http://localhost/web/'.$img_svg.'">blog '.$m_file_extension.'</a>&nbsp;('.human_file_size(filesize($img_svg)).')&nbsp;' : '';

            // File links
            echo '<pre id="filelink_'.$m_id.'"><small>SVG: '.$img_svg_link.'</small></pre>';

          } else {

            $thumb = '<img max-width="50px" max-height="50px" alt="'.$m_alt.'" src="'.$basepath.$m_location.'/'.$m_file_base.'_thumb.'.$m_file_extension.'">';

            // Thumbnail
            echo '<td class="media-lib-thumb"><div class="media-lib-thumb" id="mediatype_'.$m_id.'">'.$thumb.'</div></td>';

            // Filename
            echo '<td><pre><small id="filename_'.$m_id.'">'.$m_filename.'</small></pre>'.$ajax_edit.'</td>';

            // Info
            echo '<td class="media-lib-info">';

            $img_xs = $basepath.$m_location.'/'.$m_file_base.'_154.'.$m_file_extension;
            $img_sm = $basepath.$m_location.'/'.$m_file_base.'_484.'.$m_file_extension;
            $img_md = $basepath.$m_location.'/'.$m_file_base.'_800.'.$m_file_extension;
            $img_lg = $basepath.$m_location.'/'.$m_file_base.'_1280.'.$m_file_extension;
            $img_xl = $basepath.$m_location.'/'.$m_file_base.'_1920.'.$m_file_extension;
            $img_fl = $basepath.$m_location.'/'.$m_file_base.'.'.$m_file_extension;
            $img_or = $origpath.$m_location.'/'.$m_file_base.'.'.$m_file_extension;

            // Original and Blog image sizes
            list($img_fl_w, $img_fl_h) = (file_exists($img_fl)) ? getimagesize($img_fl) : '';
            list($img_or_w, $img_or_h) = (file_exists($img_or)) ? getimagesize($img_or) : '';

            // Orientation
            if ($img_fl_w == $img_fl_h) {
              $img_orientation = 'squr';
            } elseif ($img_fl_w > $img_fl_h) {
              $img_orientation = 'wide';
            } elseif ($img_fl_w < $img_fl_h) {
              $img_orientation = 'tall';
            }

            // Set links
            $img_xs_link = (file_exists($img_xs)) ? '<a href="http://localhost/web/'.$img_xs.'">154</a>&nbsp;('.human_file_size(filesize($img_xs)).')&nbsp;' : '';
            $img_sm_link = (file_exists($img_sm)) ? '<a href="http://localhost/web/'.$img_sm.'">484</a>&nbsp;('.human_file_size(filesize($img_sm)).')&nbsp;' : '';
            $img_md_link = (file_exists($img_md)) ? '<a href="http://localhost/web/'.$img_md.'">800</a>&nbsp;('.human_file_size(filesize($img_md)).')&nbsp;' : '';
            $img_lg_link = (file_exists($img_lg)) ? '<a href="http://localhost/web/'.$img_lg.'">1280</a>&nbsp;('.human_file_size(filesize($img_lg)).')&nbsp;' : '';
            $img_xl_link = (file_exists($img_xl)) ? '<a href="http://localhost/web/'.$img_xl.'">1920</a>&nbsp;('.human_file_size(filesize($img_xl)).')&nbsp;' : '';
            $img_fl_link = (file_exists($img_fl)) ? '<a href="http://localhost/web/'.$img_fl.'">blog '.$m_file_extension.'</a>'.'&nbsp;'.$img_fl_w.'x'.$img_fl_h.'&nbsp;('.human_file_size(filesize($img_fl)).')&nbsp;' : '';
            $img_or_link = (file_exists($img_or)) ? '<a href="http://localhost/web/'.$img_or.'">orig '.$m_file_extension.'</a>'.'&nbsp;'.$img_or_w.'x'.$img_or_h.'&nbsp;('.human_file_size(filesize($img_or)).')&nbsp;' : '';

            // File links
            echo '<pre id="filelink_'.$m_id.'"><small>IMG: '.$img_fl_link.$img_or_link.'<br><br>'.$img_orientation.'&nbsp;'.$img_xs_link.$img_sm_link.$img_md_link.$img_lg_link.$img_xl_link.'</small></pre>';

          }
        break;
        case 'VIDEO':

          $thumb = '<img max-width="50px" max-height="50px" alt="'.$m_alt.'" src="thumb-vid.png">';

          // Thumbnail
          echo '<td class="media-lib-thumb"><div class="media-lib-thumb" id="mediatype_'.$m_id.'">'.$thumb.'</div></td>';

          // Filename
          echo '<td><pre><small id="filename_'.$m_id.'">'.$m_filename.'</small></pre>'.$ajax_edit.'</td>';

          // Info
          echo '<td class="media-lib-info">';

          $vid_web = $basepath.$m_location.'/'.$m_file_base.'.'.$m_file_extension;
          $vid_ori = $origpath.$m_location.'/'.$m_file_base.'.'.$m_file_extension;

          // Set links
          $vid_web_link = (file_exists($vid_web)) ? '<a href="http://localhost/web/'.$vid_web.'">blog '.$m_file_extension.'</a>&nbsp;('.human_file_size(filesize($vid_web)).')&nbsp;' : '';
          $vid_ori_link = (file_exists($vid_ori)) ? '<a href="http://localhost/web/'.$vid_ori.'">orig '.$m_file_extension.'</a>&nbsp;('.human_file_size(filesize($vid_ori)).')&nbsp;' : '';

          // File links
          echo '<pre id="filelink_'.$m_id.'"><small>VID: '.$vid_web_link.$vid_ori_link.'</small></pre>';

        break;
        case 'AUDIO':

          $thumb = '<img max-width="50px" max-height="50px" alt="'.$m_alt.'" src="thumb-aud.png">';

          // Thumbnail
          echo '<td class="media-lib-thumb"><div class="media-lib-thumb" id="mediatype_'.$m_id.'">'.$thumb.'</div></td>';

          // Filename
          echo '<td><pre><small id="filename_'.$m_id.'">'.$m_filename.'</small></pre>'.$ajax_edit.'</td>';

          // Info
          echo '<td class="media-lib-info">';

          $aud_web = $basepath.$m_location.'/'.$m_file_base.'.mp3';
          $aud_ori = $origpath.$m_location.'/'.$m_file_base.'.'.$m_file_extension;

          // Set links
          $aud_web_link = (file_exists($aud_web)) ? '<a href="http://localhost/web/'.$aud_web.'">blog mp3</a>&nbsp;('.human_file_size(filesize($aud_web)).')&nbsp;' : '';
          $aud_ori_link = (file_exists($aud_ori)) ? '<a href="http://localhost/web/'.$aud_ori.'">orig '.$m_file_extension.'</a>&nbsp;('.human_file_size(filesize($aud_ori)).')&nbsp;' : '';

          // File links
          echo '<pre id="filelink_'.$m_id.'"><small>AUD: '.$aud_web_link.$aud_ori_link.'</small></pre>';
        break;
        case 'DOCUMENT':

          $thumb = '<img max-width="50px" max-height="50px" alt="'.$m_alt.'" src="thumb-doc.png">';

          if ( ($m_file_extension == 'txt') || ($m_file_extension == 'doc') ) {

            // Thumbnail
            echo '<td class="media-lib-thumb"><div class="media-lib-thumb" id="mediatype_'.$m_id.'">'.$thumb.'</div></td>';

            // Filename
            echo '<td><pre><small id="filename_'.$m_id.'">'.$m_filename.'</small></pre>'.$ajax_edit.'</td>';

            // Info
            echo '<td class="media-lib-info">';

            $doc_web = $basepath.$m_location.'/'.$m_file_base.'.'.$m_file_extension;

            // Set links
            $doc_web_link = (file_exists($doc_web)) ? '<a href="http://localhost/web/'.$doc_web.'">blog '.$m_file_extension.'</a>&nbsp;('.human_file_size(filesize($doc_web)).')&nbsp;' : '';

            // File links
            echo '<pre id="filelink_'.$m_id.'"><small>DOC: '.$doc_web_link.'</small></pre>';

          } else {

            // Thumbnail
            echo '<td class="media-lib-thumb"><div class="media-lib-thumb" id="mediatype_'.$m_id.'">'.$thumb.'</div></td>';

            // Filename
            echo '<td><pre><small id="filename_'.$m_id.'">'.$m_filename.'</small></pre>'.$ajax_edit.'</td>';

            // Info
            echo '<td class="media-lib-info">';

            $doc_web = $basepath.$m_location.'/'.$m_file_base.'.pdf';
            $doc_ori = $origpath.$m_location.'/'.$m_file_base.'.'.$m_file_extension;

            // Set links
            $doc_web_link = (file_exists($doc_web)) ? '<a href="http://localhost/web/'.$doc_web.'">blog pdf</a>&nbsp;('.human_file_size(filesize($doc_web)).')&nbsp;' : '';
            $doc_ori_link = (file_exists($doc_ori)) ? '<a href="http://localhost/web/'.$doc_ori.'">orig '.$m_file_extension.'</a>&nbsp;('.human_file_size(filesize($doc_ori)).')&nbsp;' : '';

            // File links
            echo '<pre id="filelink_'.$m_id.'"><small>DOC: '.$doc_web_link.$doc_ori_link.'</small></pre>';
          }
        break;

      } // Mimetype switch


      echo'</td>';

      // End the row
      echo '</tr>';

      // Toggle our row colors
      $table_row_color = ($table_row_color == 'blues') ? 'shady' : 'blues';

    }

    echo "
      </tbody>
    </table>
    ";

  } // End check for if there is anything in the media_library database


} else { // End POST check
 exit();
}
