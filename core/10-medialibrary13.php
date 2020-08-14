<?php

// Include our config (with SQL) up near the top of our PHP file
include ('./in.config.php');

// Include our login cluster
$head_title = 'Media Library'; // Set a <title> name used next
$edit_page_yn = false; // Include JavaScript for TinyMCE?
include ('./in.login_check.php');

?>
<script src="dropzone.min.js"></script>

<!-- Dropzone settings -->
<script>
  Dropzone.options.dropzoneUploaderMediaLibrary = { // JS: .dropzoneUploader = HTML: id="dropzone-uploader-media-library"
    dictDefaultMessage: 'Drop to upload!',
    paramName: "upload_file", // We are still using upload_file; default: file
    maxFilesize: 5, // MB
    uploadMultiple: true, // Default: false
      maxFiles: 50,
      parallelUploads: 1, // Default: 2
    addRemoveLinks: true, // Default: false
      dictCancelUpload: "cancel", // Cancel before upload starts text
      dictRemoveFile: "hide", // We don't have this set to delete the file since we will manage that ourselves, but it can hide the message in the Dropzone area

    // File types ported over from upload.php, redundant but consistent:
    acceptedFiles: "image/jpeg, image/png, image/gif, image/svg+xml, video/webm, video/ogg, video/mp4, audio/mpeg, audio/ogg, audio/x-wav, audio/wav, text/plain, text/html, .md, application/msword, application/vnd.openxmlformats-officedocument.wordprocessingml.document, application/vnd.oasis.opendocument.text, application/x-pdf, application/pdf",

    // Process AJAX response from upload.php

    init: function() {
      var upResponse = ''; // Variable to concatenate multiple AJAX responses
      this.on('success', function(file, responseText) {

        // Update our upResponse variable
        upResponse += '<b>'+file.name+' info:</b><br>'+responseText;

        // Show the filename and HTML response in an alert box for learning purposes
        //alert(file.name+' :: UPLOAD MESSAGE :: '+responseText);

        // Update our webpage with the current contatenated AJAX responses
        if (upResponse != '') {
          // Write the response to HTML element id="uploadresponse"
          document.getElementById("uploadresponse").innerHTML = upResponse;
        } else {
          // Write the response to HTML element id="uploadresponse"
          document.getElementById("uploadresponse").innerHTML = '<span class="error">Nothing uploaded.</span>';
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
      function mediaEdit(formID, postTo, ajaxUpdate, save_message='') { // These arguments can be anything, same as used in this function

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
    </script>
    <?php

    // Get and display each item
    $query = "SELECT id, file_base, file_extension, basic_type, size FROM media_library";
    $call = mysqli_query($database, $query);

    // Is anything there?
    if (mysqli_num_rows($call) == 0) {

      echo 'Nothing yet. Upload a file to add to your Media Library.';

    } else {

      // Message at top
      $num_items = (mysqli_num_rows($call) == 1) ? mysqli_num_rows($call).' media item' : mysqli_num_rows($call).' media items';
      echo '<p style="display: inline;"><b>'.$num_items.'</b>&nbsp;&nbsp;<div id="media-editor-saved-message" style="display: inline;"></div></p>';

      // Start our HTML table
      echo '
      <table class="contentlib" id="media-table">
        <tbody>
          <tr>
          <th width="15%">Filename</th>
          <th width="15%">Type</th>
          <th width="70%" colspan="2">Info</th>
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
        $m_size = "$row[4]";

        // Proper filename
        $m_filename = $m_file_base.'.'.$m_file_extension;

        // Use our handy function
        $m_size_pretty = human_file_size($m_size);

        // JavaScript with unique function name per row, show/hide action links
        ?>
        <script>
        function showActions<?php echo $m_id; ?>() {
          var x = document.getElementById("showaction<?php echo $m_id; ?>");
          if (x.style.display === "inline") {
            x.style.display = "none";
          } else {
            x.style.display = "inline";
          }
        }
        </script>
        <?php

        // Fill-in the row
        echo '<tr class="'.$table_row_color.'" onmouseover="showActions'.$m_id.'()" onmouseout="showActions'.$m_id.'()">';

        // File
        echo '<td><small><pre id="filename_'.$m_id.'">'.$m_filename.'</pre></small></td>';

        // Type
        echo '<td><small><pre id="mediatype_'.$m_id.'">'.$m_basic_type.'</pre></small></td>';

        // Info
        echo '<td><small><pre id="filesize_'.$m_id.'">'.$m_size_pretty.'</pre></small>&nbsp;
        </td>';

        // AJAX mediaEdit button
        echo '<td>
          <form id="mediaEdit_'.$m_id.'">
            <input type="hidden" value="'.$m_id.'" name="m_id">
            <div id="showaction'.$m_id.'" style="display: none;">
              <button type="button" class="postform link-button inline orange" onclick="mediaEdit(\'mediaEdit_'.$m_id.'\', \'ajax.mediainfo.php\', \'media-editor\');" style="float: right;">edit</button>
            </div>
          </form>
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
