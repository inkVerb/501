<?php

// Include our config (with SQL) up near the top of our PHP file
include ('./in.db.php');
include ('./in.logincheck.php');

// A few checks to make sure a user actually requested this
if ( ($_SERVER['REQUEST_METHOD'] === 'POST')
&& (!empty($_POST['u_id']))
&& (filter_var($_POST['u_id'], FILTER_VALIDATE_INT))
&& (!empty($_POST['feature_type']))
&& (($_POST['feature_type'] == 'IMAGE') || ($_POST['feature_type'] == 'AUDIO') || ($_POST['feature_type'] == 'VIDEO') || ($_POST['feature_type'] == 'DOCUMENT'))
&& (isset($_SESSION['user_id']))
&& ($_SESSION['user_id'] == $_POST['u_id']) ) {

$m_basic_type = $_POST['feature_type'];

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
  $query = $database->prepare("SELECT id, file_base, file_extension, location, size, alt_text FROM media_library WHERE basic_type=:basic_type ORDER BY id DESC");
  $query->bindParam(':basic_type', $m_basic_type);
  $rows = $pdo->exec_($query);

  // Is anything there?
  if ($pdo->numrows == 0) {

    echo '<div style="display:block;clear:both;"><p style="display:inline;">Nothing yet. Upload a file to add to your Media Library.</p></div>';

  } else {

    // Simple line
    echo '&nbsp;<div id="media-insert-editor-saved-message" style="display:inline;"></div><br>';

    // Start our HTML table
    echo '
    <div id="featured-insert-table-container">
    <table class="contentlib" id="media-insert-table">
      <!-- Set our column widths without creating a row -->
      <colgroup>
       <col span="1" style="width: 20%;">
       <col span="1" style="width: 80%;">
      </colgroup>
      <tbody>

    ';

    // Start our row colors
    $table_row_color = 'blues';
    // We have many entries, this will iterate one post per each
    foreach ($rows as $row) {
      // Assign the values
      $m_id = "$row->id";
      $m_file_base = "$row->file_base";
      $m_file_extension = "$row->file_extension";
      $m_location = "$row->location";
      $m_size = "$row->size";
      $m_alt = "$row->alt_text";

      // Proper filename
      $m_filename = $m_file_base.'.'.$m_file_extension;

      // Use our handy function
      $m_size_pretty = human_file_size($m_size);

      // Start our row
      echo '<tr class="'.$table_row_color.'" onmouseover="showActions('.$m_id.')" onmouseout="showActions('.$m_id.')">';

      // setToFeature button
      $basepath = 'media/';
      $m_filename_full_path = $basepath.$m_location.'/'.$m_filename;

      // $file_thumb variable

      if ($m_basic_type == 'IMAGE') {
        $file_thumb = ($m_file_extension == 'svg') ? $basepath.$m_location.'/'.$m_file_base.'_thumb_svg.png' : $basepath.$m_location.'/'.$m_file_base.'_thumb'.'.'."$m_file_extension";
      } else {
        $file_thumb = 0;
      }
      $set_button = '<button class="postform orange" onclick="setToFeature(\''.$m_id.'\', \''.$m_filename_full_path.'\', \''.$m_filename.'\', \''.$m_basic_type.'\', \''.$file_thumb.'\');">&larr; '.$m_filename.'</button>&nbsp;('.human_file_size(filesize($m_filename_full_path)).')<br>';

      // Fill-in the row per media type
      switch ($m_basic_type) {
        case 'IMAGE':
          if ($m_file_extension == 'svg') {

            // Use the .png thumbnail because the .svg file is likely larger than this 50px .png
            $thumb = '<img max-width="50px" max-height="50px" alt="'.$m_alt.'" src="'.$basepath.$m_location.'/'.$m_file_base.'_thumb_svg.png">';

            // Thumbnail
            echo '<td class="media-lib-thumb"><div class="media-lib-thumb" id="mediatype_'.$m_id.'">'.$thumb.'</div></td>';

            // Filename
            echo '<td><pre><small id="filename_'.$m_id.'">'.$m_filename.'</small></pre>'.$set_button;

          } else {

            $thumb = '<img max-width="50px" max-height="50px" alt="'.$m_alt.'" src="'.$basepath.$m_location.'/'.$m_file_base.'_thumb.'.$m_file_extension.'">';

            // Thumbnail
            echo '<td class="media-lib-thumb"><div class="media-lib-thumb" id="mediatype_'.$m_id.'">'.$thumb.'</div></td>';

            // Filename
            echo '<td><pre><small id="filename_'.$m_id.'">'.$m_filename.'</small></pre>'.$set_button;

          }
        break;
        case 'VIDEO':

          $thumb = '<img max-width="50px" max-height="50px" alt="'.$m_alt.'" src="thumb-vid.png">';

          // Thumbnail
          echo '<td class="media-lib-thumb"><div class="media-lib-thumb" id="mediatype_'.$m_id.'">'.$thumb.'</div></td>';

          // Filename
          echo '<td><pre><small id="filename_'.$m_id.'">'.$m_filename.'</small></pre>'.$set_button;

        break;
        case 'AUDIO':

          $thumb = '<img max-width="50px" max-height="50px" alt="'.$m_alt.'" src="thumb-aud.png">';

          // Thumbnail
          echo '<td class="media-lib-thumb"><div class="media-lib-thumb" id="mediatype_'.$m_id.'">'.$thumb.'</div></td>';

          // Filename
          echo '<td><pre><small id="filename_'.$m_id.'">'.$m_filename.'</small></pre>'.$set_button;

        break;
        case 'DOCUMENT':

          $thumb = '<img max-width="50px" max-height="50px" alt="'.$m_alt.'" src="thumb-doc.png">';

          if ( ($m_file_extension == 'txt') || ($m_file_extension == 'doc') ) {

            // Thumbnail
            echo '<td class="media-lib-thumb"><div class="media-lib-thumb" id="mediatype_'.$m_id.'">'.$thumb.'</div></td>';

            // Filename
            echo '<td><pre><small id="filename_'.$m_id.'">'.$m_filename.'</small></pre>'.$set_button;

          } else {

            // Thumbnail
            echo '<td class="media-lib-thumb"><div class="media-lib-thumb" id="mediatype_'.$m_id.'">'.$thumb.'</div></td>';

            // Filename
            echo '<td><pre><small id="filename_'.$m_id.'">'.$m_filename.'</small></pre>'.$set_button;

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
    </div>
    ";

  } // End check for if there is anything in the media_library database


} else { // End POST check
 exit ();
}