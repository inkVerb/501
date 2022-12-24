<?php

// Include our config (with SQL) up near the top of our PHP file
include ('./in.db.php');
include ('./in.logincheck.php');

// A few checks to make sure a user actually requested this
if ( ($_SERVER['REQUEST_METHOD'] === 'POST')
&& (!empty($_POST['u_id']))
&& (filter_var($_POST['u_id'], FILTER_VALIDATE_INT))
&& (!empty($_POST['feature_type']))
&& (($_POST['feature_type'] == 'IMAGE')
   || ($_POST['feature_type'] == 'AUDIO')
   || ($_POST['feature_type'] == 'VIDEO')
   || ($_POST['feature_type'] == 'DOCUMENT'))
&& (isset($_SESSION['user_id']))
&& ($_SESSION['user_id'] == $_POST['u_id']) ) {

// AJAX token check
if ( $_POST['ajax_token'] !== $_SESSION['ajax_token'] ) {
  exit();
}

$m_basic_type = $_POST['feature_type'];

?>

<!-- AJAX mediaEdit container: media editor will CSS-float inside this -->
<div id="media-insert-editor-container" style="display:none;">
  <!-- AJAX mediaEdit HTML entity -->
  <div id="media-insert-editor"></div>
</div>

<!-- Iterate through each item in the media libary -->
  <?php

  // Pagination
  // Valid the Pagination
  if ((isset($_POST['r'])) && (filter_var($_POST['r'], FILTER_VALIDATE_INT, array('min_range' => 1)))) {
   $paged = preg_replace("/[^0-9]/","", $_POST['r']);
  } else {
   $paged = 1;
  }
  // Set pagination variables:
  $pageitems = 100;
  $itemskip = $pageitems * ($paged - 1);
  // We add this to the end of the $query, after DESC
  // LIMIT $itemskip,$pageitems

  // Pagination navigation: How many items total?
  $query = $database->prepare("SELECT id FROM media_library WHERE basic_type=:basic_type ORDER BY id DESC");
  $query->bindParam(':basic_type', $m_basic_type);
  $rows = $pdo->exec_($query);
  $totalrows = $pdo->numrows;

  $totalpages = floor($totalrows / $pageitems);
  $remainder = $totalrows % $pageitems;
  if ($remainder > 0) {
  $totalpages = $totalpages + 1;
  }
  if ($paged > $totalpages) {
  $totalpages = 1;
  }
  $nextpaged = $paged + 1;
  $prevpaged = $paged - 1;

  // Pagination nav row
  if ($totalpages > 1) {
    echo "
    <div class=\"paginate_nav_container\">
      <div class=\"paginate_nav\">
        <table>
          <tr>
            <td>
              <a class=\"paginate";
              if ($paged == 1) {echo " disabled";}
              echo "\" title=\"Page 1\" href=\"#\" onclick=\"mediaFeatureInsert('$m_basic_type', $user_id, 1);\">&laquo;</a>
            </td>
            <td>
              <a class=\"paginate";
              if ($paged == 1) {echo " disabled";}
             echo "\" title=\"Previous\" href=\"#\" onclick=\"mediaFeatureInsert('$m_basic_type', $user_id, $prevpaged);\">&lsaquo;&nbsp;</a>
            </td>
            <td>
              <a class=\"paginate current\" title=\"Next\" href=\"#\" onclick=\"mediaFeatureInsert('$m_basic_type', $user_id, $paged);\">Page $paged ($totalpages)</a>
            </td>
            <td>
              <a class=\"paginate";
              if ($paged == $totalpages) {echo " disabled";}
             echo "\" title=\"Next\" href=\"#\" onclick=\"mediaFeatureInsert('$m_basic_type', $user_id, $nextpaged);\">&nbsp;&rsaquo;</a>
            </td>
             <td>
               <a class=\"paginate";
               if ($paged == $totalpages) {echo " disabled";}
              echo "\" title=\"Last Page\" href=\"#\" onclick=\"mediaFeatureInsert('$m_basic_type', $user_id, $totalpages);\">&raquo;</a>
             </td>
          </tr>
        </table>
      </div>
    </div>";
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

  // Get and display each item
  $query = $database->prepare("SELECT id, file_base, file_extension, location, size, alt_text, duration FROM media_library WHERE basic_type=:basic_type ORDER BY id DESC LIMIT $itemskip,$pageitems");
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
      $m_duration = "$row->duration";

      // Proper filename
      $m_filename = $m_file_base.'.'.$m_file_extension;

      // Use our handy function
      $m_size_pretty = human_file_size($m_size);

      // Start our row
      echo '<tr class="'.$table_row_color.'" onmouseover="showActions('.$m_id.')" onmouseout="showActions('.$m_id.')">';

      // Paths, also used in setToFeature()
      $media_library_folder = '/media/';
      $basepath = $blog_web_base.$media_library_folder;
      $m_filename_full_path = $basepath.$m_location.'/'.$m_filename;

      // $file_thumb variable
      if ($m_basic_type == 'IMAGE') {
        $file_thumb = ($m_file_extension == 'svg') ? $basepath.$m_location.'/'.$m_file_base.'_thumb_svg.png' : $basepath.$m_location.'/'.$m_file_base.'_thumb'.'.'."$m_file_extension";
      } else {
        $file_thumb = 0;
      }

      // Clickable text to set featured media
      $set_button = '<pre><button class="postform orange" onclick="setToFeature(\''.$m_id.'\', \''.$m_filename_full_path.'\', \''.$m_filename.'\', \''.$m_basic_type.'\', \''.$file_thumb.'\'); onNavWarn(); mediaInsertHide(); mediaFeatureHide();">&larr; '.$m_filename.'</button>&nbsp;('.$m_size_pretty.')</pre><br>';

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
          echo '<td class="media-lib-thumb"><div class="media-lib-thumb" id="mediatype_'.$m_id.'">'.$thumb.'</div><br><pre id="duration_'.$m_id.'"><small>'.$m_duration.'</small></pre></td>';

          // Filename
          echo '<td><pre><small id="filename_'.$m_id.'">'.$m_filename.'</small></pre>'.$set_button;

        break;
        case 'AUDIO':

          $thumb = '<img max-width="50px" max-height="50px" alt="'.$m_alt.'" src="thumb-aud.png">';

          // Thumbnail
          echo '<td class="media-lib-thumb"><div class="media-lib-thumb" id="mediatype_'.$m_id.'">'.$thumb.'</div><br><pre id="duration_'.$m_id.'"><small>'.$m_duration.'</small></pre></td>';

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
    ";

    echo "<br><br>";

    // Pagination nav row
    if ($totalpages > 1) {
      echo "
      <div class=\"paginate_nav_container\">
        <div class=\"paginate_nav\">
          <table>
            <tr>
              <td>
                <a class=\"paginate";
                if ($paged == 1) {echo " disabled";}
                echo "\" title=\"Page 1\" href=\"#\" onclick=\"mediaFeatureInsert('$m_basic_type', $user_id, 1);\">&laquo;</a>
              </td>
              <td>
                <a class=\"paginate";
                if ($paged == 1) {echo " disabled";}
               echo "\" title=\"Previous\" href=\"#\" onclick=\"mediaFeatureInsert('$m_basic_type', $user_id, $prevpaged);\">&lsaquo;&nbsp;</a>
              </td>
              <td>
                <a class=\"paginate current\" title=\"Next\" href=\"#\" onclick=\"mediaFeatureInsert('$m_basic_type', $user_id, $paged);\">Page $paged ($totalpages)</a>
              </td>
              <td>
                <a class=\"paginate";
                if ($paged == $totalpages) {echo " disabled";}
               echo "\" title=\"Next\" href=\"#\" onclick=\"mediaFeatureInsert('$m_basic_type', $user_id, $nextpaged);\">&nbsp;&rsaquo;</a>
              </td>
               <td>
                 <a class=\"paginate";
                 if ($paged == $totalpages) {echo " disabled";}
                echo "\" title=\"Last Page\" href=\"#\" onclick=\"mediaFeatureInsert('$m_basic_type', $user_id, $totalpages);\">&raquo;</a>
               </td>
            </tr>
          </table>
        </div>
      </div>";
    }

    echo "</div>"; // <div id="media-insert-editor-container"

  } // End check for if there is anything in the media_library database


} else { // End POST check
 exit ();
}
