<?php

// Include our config (with SQL) up near the top of our PHP file
include ('./in.db.php');
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
  $query = $database->prepare("SELECT id FROM media_library ORDER BY id DESC");
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
              echo "\" title=\"Page 1\" href=\"#\" onclick=\"mediaInsert($user_id, 1);\">&laquo;</a>
            </td>
            <td>
              <a class=\"paginate";
              if ($paged == 1) {echo " disabled";}
             echo "\" title=\"Previous\" href=\"#\" onclick=\"mediaInsert($user_id, $prevpaged);\">&lsaquo;&nbsp;</a>
            </td>
            <td>
              <a class=\"paginate current\" title=\"Next\" href=\"#\" onclick=\"mediaInsert($user_id, $paged);\">Page $paged ($totalpages)</a>
            </td>
            <td>
              <a class=\"paginate";
              if ($paged == $totalpages) {echo " disabled";}
             echo "\" title=\"Next\" href=\"#\" onclick=\"mediaInsert($user_id, $nextpaged);\">&nbsp;&rsaquo;</a>
            </td>
             <td>
               <a class=\"paginate";
               if ($paged == $totalpages) {echo " disabled";}
              echo "\" title=\"Last Page\" href=\"#\" onclick=\"mediaInsert($user_id, $totalpages);\">&raquo;</a>
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
  $query = $database->prepare("SELECT id, file_base, file_extension, basic_type, location, size, alt_text FROM media_library ORDER BY id DESC LIMIT $itemskip,$pageitems");
  $rows = $pdo->exec_($query);

  // Is anything there?
  if ($pdo->numrows == 0) {

    echo '<div style="display:block;clear:both;"><p style="display:inline;">Nothing yet. Upload a file to add to your Media Library.</p></div>';

  } else {

    // Simple line
    echo '&nbsp;<div id="media-insert-editor-saved-message" style="display:inline;"></div><br>';

    // Start our HTML table
    echo '
    <div id="media-insert-table-container">
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
      $m_basic_type = "$row->basic_type";
      $m_location = "$row->location";
      $m_size = "$row->size";
      $m_alt = "$row->alt_text";

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
            <button type="button" class="postform link-button inline orange" onclick="mediaEdit(\'mediaEdit_'.$m_id.'\', \'ajax.mediainfoinsert.php\', \'media-insert-editor\');"><small>edit / insert</small></button>
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
            echo '<td><pre><small id="filename_'.$m_id.'">'.$m_filename.'</small></pre>'.$ajax_edit;

          } else {

            $thumb = '<img max-width="50px" max-height="50px" alt="'.$m_alt.'" src="'.$basepath.$m_location.'/'.$m_file_base.'_thumb.'.$m_file_extension.'">';

            // Thumbnail
            echo '<td class="media-lib-thumb"><div class="media-lib-thumb" id="mediatype_'.$m_id.'">'.$thumb.'</div></td>';

            // Filename
            echo '<td><pre><small id="filename_'.$m_id.'">'.$m_filename.'</small></pre>'.$ajax_edit;

          }
        break;
        case 'VIDEO':

          $thumb = '<img max-width="50px" max-height="50px" alt="'.$m_alt.'" src="thumb-vid.png">';

          // Thumbnail
          echo '<td class="media-lib-thumb"><div class="media-lib-thumb" id="mediatype_'.$m_id.'">'.$thumb.'</div></td>';

          // Filename
          echo '<td><pre><small id="filename_'.$m_id.'">'.$m_filename.'</small></pre>'.$ajax_edit;

        break;
        case 'AUDIO':

          $thumb = '<img max-width="50px" max-height="50px" alt="'.$m_alt.'" src="thumb-aud.png">';

          // Thumbnail
          echo '<td class="media-lib-thumb"><div class="media-lib-thumb" id="mediatype_'.$m_id.'">'.$thumb.'</div></td>';

          // Filename
          echo '<td><pre><small id="filename_'.$m_id.'">'.$m_filename.'</small></pre>'.$ajax_edit;

        break;
        case 'DOCUMENT':

          $thumb = '<img max-width="50px" max-height="50px" alt="'.$m_alt.'" src="thumb-doc.png">';

          if ( ($m_file_extension == 'txt') || ($m_file_extension == 'doc') ) {

            // Thumbnail
            echo '<td class="media-lib-thumb"><div class="media-lib-thumb" id="mediatype_'.$m_id.'">'.$thumb.'</div></td>';

            // Filename
            echo '<td><pre><small id="filename_'.$m_id.'">'.$m_filename.'</small></pre>'.$ajax_edit;

          } else {

            // Thumbnail
            echo '<td class="media-lib-thumb"><div class="media-lib-thumb" id="mediatype_'.$m_id.'">'.$thumb.'</div></td>';

            // Filename
            echo '<td><pre><small id="filename_'.$m_id.'">'.$m_filename.'</small></pre>'.$ajax_edit;

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
                echo "\" title=\"Page 1\" href=\"#\" onclick=\"mediaInsert($user_id, 1);\">&laquo;</a>
              </td>
              <td>
                <a class=\"paginate";
                if ($paged == 1) {echo " disabled";}
               echo "\" title=\"Previous\" href=\"#\" onclick=\"mediaInsert($user_id, $prevpaged);\">&lsaquo;&nbsp;</a>
              </td>
              <td>
                <a class=\"paginate current\" title=\"Next\" href=\"#\" onclick=\"mediaInsert($user_id, $paged);\">Page $paged ($totalpages)</a>
              </td>
              <td>
                <a class=\"paginate";
                if ($paged == $totalpages) {echo " disabled";}
               echo "\" title=\"Next\" href=\"#\" onclick=\"mediaInsert($user_id, $nextpaged);\">&nbsp;&rsaquo;</a>
              </td>
               <td>
                 <a class=\"paginate";
                 if ($paged == $totalpages) {echo " disabled";}
                echo "\" title=\"Last Page\" href=\"#\" onclick=\"mediaInsert($user_id, $totalpages);\">&raquo;</a>
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
