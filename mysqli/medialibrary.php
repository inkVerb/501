<?php

// Include our config (with SQL) up near the top of our PHP file
include ('./in.db.php');

// Include our login cluster
$head_title = 'Media Library'; // Set a <title> name used next
$edit_page_yn = false; // Include JavaScript for TinyMCE?
include ('./in.logincheck.php');
include ('./in.head.php');

?>
<script src="dropzone.min.js"></script>

<!-- Dropzone settings -->
<script>
  Dropzone.options.dropzoneUploaderMediaLibrary = { // JS: .dropzoneUploader = HTML: id="dropzone-uploader"
    dictDefaultMessage: 'Drop to upload!',
    paramName: "upload_file", // We are still using upload_file; default: file
    maxFilesize: 100, // MB
    uploadMultiple: true, // Default: false
    maxFiles: 50,
    parallelUploads: 1, // Default: 2
    addRemoveLinks: true, // Default: false
    dictCancelUpload: "cancel", // Cancel before upload starts text
    dictRemoveFile: "hide", // We don't have this set to delete the file since we will manage that ourselves, but it can hide the message in the Dropzone area

    // File types ported over from upload.php, redundant but consistent:
    acceptedFiles: "image/jpeg, image/png, image/gif, image/svg+xml, image/bmp, image/x-windows-bmp, image/x-ms-bmp, video/webm, video/x-theora+ogg, video/ogg, video/mp4, video/x-flv, video/x-msvideo, video/x-matroska, video/quicktime, audio/mpeg, audio/ogg, audio/x-wav, audio/wav, audio/x-flac, audio/flac, text/plain, text/html, .md, application/msword, application/vnd.openxmlformats-officedocument.wordprocessingml.document, application/vnd.oasis.opendocument.text, application/x-pdf, application/pdf",

    // Process AJAX response from upload.php
    init: function() {
      var upResponse = ''; // Variable to concatenate multiple AJAX responses
      this.on('success', function(file, responseText) {

        // Update our upResponse variable
        upResponse += '<div class="media-upload-info"><b>'+file.name+' info:</b><br>'+responseText+'</div>';

        // Update our webpage with the current contatenated AJAX responses
        if (upResponse != '') {
          // Write the response to HTML element id="uploadresponse"
          document.getElementById("uploadresponse").innerHTML = upResponse;
        } else {
          // Write the response to HTML element id="uploadresponse"
          document.getElementById("uploadresponse").innerHTML = '<div style="float:left;"><span class="error">Nothing uploaded.</span></div>';
        }

      });

    } // Process AJAX response

  };
</script>
<!-- End Dropzone settings -->

<!-- AJAX mediaEdit container: media editor will CSS-float inside this -->
<div id="media-editor-container" style="display:none;">
  <!-- AJAX mediaEdit HTML entity -->
  <div id="media-editor"></div>
</div>

<!-- Media Library -->
<div id="media-library">

  <!-- Dropzone -->
  <div id="media-upload">

    <form id="dropzone-uploader-media-library" class="dropzone ml" action='upload.php' method='post' enctype='multipart/form-data'></form>

    <!-- AJAX response from upload.php will go here-->
    <div id="uploadresponse"></div>
  </div>
  <br>
  <!-- Iterate through each item in the media libary -->
  <div id="media-list">
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

    // JavaScript for AJAX mediaEdit
    ?>
    <script>
      // Open the media editor, populate via AJAX
      function mediaEdit(formID, postTo, ajaxUpdate) { // These arguments can be anything, same as used in this function

        // Show the media-edit div
        document.getElementById("media-editor-container").style.display = "block";

        // Bind a new event listener every time the <form> is changed:
        const FORM = document.getElementById(formID); // <form> by ID to access, formID is the JS argument in the function
        const AJAX = new XMLHttpRequest(); // AJAX handler
        const FD = new FormData(FORM); // Bind to-send data to form element

        AJAX.addEventListener( "load", function(event) { // This runs when AJAX responds
          document.getElementById(ajaxUpdate).innerHTML = event.target.responseText; // HTML element by ID to update, ajaxUpdate is the JS argument in the function
        } );

        AJAX.addEventListener( "error", function(event) { // This runs if AJAX fails
          document.getElementById(ajaxUpdate).innerHTML =  'Oops! Something went wrong.';
        } );

        AJAX.open("POST", postTo); // Send data, postTo is the .php destination file, from the JS argument in the function

        AJAX.send(FD); // Data sent is from the form

      } // mediaEdit() function

      // Save media info via AJAX, only indicate "Saved" or other message AJAXed backed from the server
      function mediaSave(m_id) { // These arguments can be anything, same as used in this function

        // Bind a new event listener every time the <form> is changed:
        const FORM = document.getElementById("media-edit-form"); // <form> by ID to access, formID is the JS argument in the function
        const AJAX = new XMLHttpRequest(); // AJAX handler
        const FD = new FormData(FORM); // Bind to-send data to form element

        AJAX.addEventListener( "load", function(event) { // This runs when AJAX responds
          // Parse our response
          var jsonMetaEditResponse = JSON.parse(event.target.responseText); // For "title" and "changed"

          // Reload the media edit form
          mediaEdit('mediaEdit_'+m_id, 'ajax.mediainfo.php', 'media-editor');

          // Show the message
          document.getElementById("media-editor-saved-message").innerHTML = jsonMetaEditResponse["message"];

          // Style the media type in the Media Library table
          document.getElementById("mediatype_"+m_id).classList.add('orange');
          document.getElementById("upload_"+m_id).classList.remove('blue');
        } );

        AJAX.addEventListener( "error", function(event) { // This runs if AJAX fails
          document.getElementById("media-editor-saved-message").innerHTML =  'Oops! Something went wrong.';
        } );

        AJAX.open("POST", "ajax.mediainfo.php"); // Send data, postTo is the .php destination file, from the JS argument in the function

        AJAX.send(FD); // Data sent is from the form

      } // mediaSave() function

      // Save new file name via AJAX, only indicate "Saved" or other message AJAXed backed from the server
      function nameChange(m_id) { // These arguments can be anything, same as used in this function

        // Bind a new event listener every time the <form> is changed:
        const FORM = document.getElementById("name-change-form"); // <form> by ID to access, formID is the JS argument in the function
        const AJAX = new XMLHttpRequest(); // AJAX handler
        const FD = new FormData(FORM); // Bind to-send data to form element

        AJAX.addEventListener( "load", function(event) { // This runs when AJAX responds
          // Parse our response
          var jsonMetaEditResponse = JSON.parse(event.target.responseText); // For "title" and "changed"

          // Reload the media edit form
          mediaEdit('mediaEdit_'+m_id, 'ajax.mediainfo.php', 'media-editor');

          // Show the message
          document.getElementById("media-editor-saved-message").innerHTML = jsonMetaEditResponse["message"];

          // Style & update the name in the Media Library table
          document.getElementById("filename_"+m_id).innerHTML = jsonMetaEditResponse["file_name"];
          document.getElementById("filename_"+m_id).classList.add('orange');
          document.getElementById("upload_"+m_id).classList.remove('blue');
        } );

        AJAX.addEventListener( "error", function(event) { // This runs if AJAX fails
          document.getElementById("media-editor-saved-message").innerHTML =  'Oops! Something went wrong.';
        } );

        AJAX.open("POST", "ajax.mediainfo.php"); // Send data, postTo is the .php destination file, from the JS argument in the function

        AJAX.send(FD); // Data sent is from the form

      } // nameChange() function

      // Close the media editor
      function mediaEditorClose() {
        document.getElementById("media-editor-container").style.display = "none";
      }

      // File name change form
      function changeFileName(m_id, m_file_base, m_file_extension) {
        const FILE_NAME_FORM = '<form id="name-change-form">\
          <input type="hidden" value="'+m_id+'" name="m_id">\
          <input type="hidden" value="'+m_id+'" name="name_change">\
          <input type="text" name="save_file_name" value="'+m_file_base+'">&nbsp;<b><code>.'+m_file_extension+'</code></b>\
          <button type="button" onclick="nameChange('+m_id+');">Change file name</button>\
          &nbsp;&nbsp;<span onclick="changeFileNameClose(\''+m_id+'\', \''+m_file_base+'\', \''+m_file_extension+'\');" class="postform" title="cancel">&#xd7;</span>\
        </form>';
        document.getElementById("change-file-name").innerHTML = FILE_NAME_FORM;
      }

      // Close the file name change form
      function changeFileNameClose(m_id, m_file_base, m_file_extension) {
        const PRE_CONTENT = '<pre onclick="changeFileName(\''+m_id+'\', \''+m_file_base+'\', \''+m_file_extension+'\');" class="postform blue" title="change file name">'+m_file_base+'.'+m_file_extension+'</pre>';
        document.getElementById("change-file-name").innerHTML = PRE_CONTENT;
      }

      // JavaScript to show/hide edit link
      function showActions(m_id) {
        var x = document.getElementById("showaction"+m_id);
        if (x.style.display === "inline") {
          x.style.display = "none";
        } else {
          x.style.display = "inline";
        }
      }

      // JavaScript to show/hide delete action
      function showDelete() {
        // Hide/show the bulk delete div
        var x = document.getElementById("bulk_delete_div");
        if (x.style.display === "block") {
          x.style.display = "none";
        } else {
          x.style.display = "block";
        }

        // Hide/show the delete button
        var d = document.getElementById("bulk_delete_button");
        if (d.style.display === "block") {
          d.style.display = "none";
        } else {
          d.style.display = "block";
        }

        // Hide/show all checkboxes by class
        [].forEach.call(document.querySelectorAll(".del_checkbox"), function (c) {
          if (c.style.display === "block") {
            c.style.display = "none";
          } else {
            c.style.display = "block";
          }
        });

        // Make sure the delete confirm is hidden (so it doesn't stay shown if delete checkboxes are re-hidden)
        document.getElementById("bulk_delete_confirm").style.display = "none";

      }

      // JavaScript to "Select all"
      function toggle(source) {
        var checkboxes = document.querySelectorAll('input[type="checkbox"]');
        for (var i = 0; i < checkboxes.length; i++) {
          if (checkboxes[i] != source)
            checkboxes[i].checked = source.checked;
        }
      }

      // JavaScript to show "confirm delete forever"
      function confirmDelete() {
        var x = document.getElementById("bulk_delete_confirm");
        if (x.style.display === "block") {
          x.style.display = "none";
        } else {
          x.style.display = "block";
        }
      }
    </script>
    <?php

    // Get and display each item
    $query = "SELECT id, file_base, file_extension, basic_type, location, size, alt_text FROM media_library ORDER BY id DESC";
    $call = mysqli_query($database, $query);

    // Is anything there?
    if (mysqli_num_rows($call) == 0) {

      echo '<div style="display:block;clear:both;"><p style="display:inline;">Nothing yet. Upload a file to add to your Media Library.</p></div>';

    } else {

      // Message at top
      $num_items = (mysqli_num_rows($call) == 1) ? mysqli_num_rows($call).' media item' : mysqli_num_rows($call).' media items';
      echo '<div style="display:block;clear:both;"><p style="display:inline;"><b>'.$num_items.'</b>&nbsp;&nbsp;<div id="media-editor-saved-message" style="display:inline;"></div></p></div>';


      // Simple line
      echo '<br><hr><br>';

      // Start our HTML table
      echo '
      <table class="contentlib" id="media-table">
        <tbody>
          <tr>
          <th width="15%">File
            <div id="bulk_delete_button" style="display: none;">
              <br>
              <button type="button" class="postform link-button inline red" onclick="confirmDelete();">delete &rarr;</button>
            </div>
          </th>
          <th width="15%"></th>
          <th width="55%">Info
            <div id="bulk_delete_confirm" style="display: none;">
              <form id="delete_action" method="post" action="act.delmedia.php">
                <br>
                <b><input type="submit" class="red" name="deleteaction" value="confirm delete forever"></b>
              </form>
            </div>
          </th>
          <th width="15%">
            <div onclick="showDelete()" style="cursor: pointer; display: inline; float: right;"><b>Delete&#9660;</b></div><br>
            <div id="bulk_delete_div" style="display: none;">
              <br>
              <label style="float: right;"><b>Select all</b> <input type="checkbox" onclick="toggle(this);"></label>
            </div>
          </th>
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
              <button type="button" class="postform link-button inline orange" onclick="mediaEdit(\'mediaEdit_'.$m_id.'\', \'ajax.mediainfo.php\', \'media-editor\');"><small>edit</small></button>
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
              $img_svg_link = (file_exists($img_svg)) ? '<a target="_blank" href="http://localhost/web/'.$img_svg.'">blog '.$m_file_extension.'</a>&nbsp;('.human_file_size(filesize($img_svg)).')&nbsp;' : '';

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
              $img_xs_link = (file_exists($img_xs)) ? '<a target="_blank" href="http://localhost/web/'.$img_xs.'">154</a>&nbsp;('.human_file_size(filesize($img_xs)).')&nbsp;' : '';
              $img_sm_link = (file_exists($img_sm)) ? '<a target="_blank" href="http://localhost/web/'.$img_sm.'">484</a>&nbsp;('.human_file_size(filesize($img_sm)).')&nbsp;' : '';
              $img_md_link = (file_exists($img_md)) ? '<a target="_blank" href="http://localhost/web/'.$img_md.'">800</a>&nbsp;('.human_file_size(filesize($img_md)).')&nbsp;' : '';
              $img_lg_link = (file_exists($img_lg)) ? '<a target="_blank" href="http://localhost/web/'.$img_lg.'">1280</a>&nbsp;('.human_file_size(filesize($img_lg)).')&nbsp;' : '';
              $img_xl_link = (file_exists($img_xl)) ? '<a target="_blank" href="http://localhost/web/'.$img_xl.'">1920</a>&nbsp;('.human_file_size(filesize($img_xl)).')&nbsp;' : '';
              $img_fl_link = (file_exists($img_fl)) ? '<a target="_blank" href="http://localhost/web/'.$img_fl.'">blog '.$m_file_extension.'</a>'.'&nbsp;'.$img_fl_w.'x'.$img_fl_h.'&nbsp;('.human_file_size(filesize($img_fl)).')&nbsp;' : '';
              $img_or_link = (file_exists($img_or)) ? '<a target="_blank" href="http://localhost/web/'.$img_or.'">orig '.$m_file_extension.'</a>'.'&nbsp;'.$img_or_w.'x'.$img_or_h.'&nbsp;('.human_file_size(filesize($img_or)).')&nbsp;' : '';

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

            $vid_web = $basepath.$m_location.'/'.$m_file_base.'.mp4';
            $vid_ori = $origpath.$m_location.'/'.$m_file_base.'.'.$m_file_extension;

            // Set links
            $vid_web_link = (file_exists($vid_web)) ? '<a target="_blank" href="http://localhost/web/'.$vid_web.'">blog mp4</a>&nbsp;('.human_file_size(filesize($vid_web)).')&nbsp;' : '';
            $vid_ori_link = (file_exists($vid_ori)) ? '<a target="_blank" href="http://localhost/web/'.$vid_ori.'">orig '.$m_file_extension.'</a>&nbsp;('.human_file_size(filesize($vid_ori)).')&nbsp;' : '';

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
            $aud_web_link = (file_exists($aud_web)) ? '<a target="_blank" href="http://localhost/web/'.$aud_web.'">blog mp3</a>&nbsp;('.human_file_size(filesize($aud_web)).')&nbsp;' : '';
            $aud_ori_link = (file_exists($aud_ori)) ? '<a target="_blank" href="http://localhost/web/'.$aud_ori.'">orig '.$m_file_extension.'</a>&nbsp;('.human_file_size(filesize($aud_ori)).')&nbsp;' : '';

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
              $doc_web_link = (file_exists($doc_web)) ? '<a target="_blank" href="http://localhost/web/'.$doc_web.'">blog '.$m_file_extension.'</a>&nbsp;('.human_file_size(filesize($doc_web)).')&nbsp;' : '';

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
              $doc_web_link = (file_exists($doc_web)) ? '<a target="_blank" href="http://localhost/web/'.$doc_web.'">blog pdf</a>&nbsp;('.human_file_size(filesize($doc_web)).')&nbsp;' : '';
              $doc_ori_link = (file_exists($doc_ori)) ? '<a target="_blank" href="http://localhost/web/'.$doc_ori.'">orig '.$m_file_extension.'</a>&nbsp;('.human_file_size(filesize($doc_ori)).')&nbsp;' : '';

              // File links
              echo '<pre id="filelink_'.$m_id.'"><small>DOC: '.$doc_web_link.$doc_ori_link.'</small></pre>';
            }
          break;

        } // Mimetype switch


        echo'</td>';

        // Delete actions
        echo '<td>
          <br>
          <div class="del_checkbox" style="display: none; float: right;"><label for="bulk_'.$m_id.'">delete <input form="delete_action" type="checkbox" id="bulk_'.$m_id.'" name="bulk_'.$m_id.'" value="'.$m_id.'"></label></div>
        </td>';

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
  ?>
  </div>

</div>

<?php

//

// Footer
include ('./in.footer.php');
