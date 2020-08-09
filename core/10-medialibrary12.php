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

<!-- Media Library -->
<div id="media-library">
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

    // Start our HTML table
    echo '
    <table class="contentlib" id="media-table">
      <tbody>
        <tr>
        <th width="15%">Filename</th>
        <th width="15%">Type</th>
        <th width="70%">Info</th>
        </tr>
    ';

    // Get and display each item
    $query = "SELECT id, file_base, file_extension, basic_type, size FROM media_library";
    $call = mysqli_query($database, $query);
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

      // Fill-in the row
      echo '<tr class="'.$table_row_color.'">';

      // File
      echo '<td><small><pre>'.$m_filename.'</pre></small></td>';

      // Type
      echo '<td><small>'.$m_basic_type.'</small></td>';

      // Info
      echo '<td><small>'.$m_size_pretty.'</small></td>';

      // End the row
      echo '</tr>';

      // Toggle our row colors
      $table_row_color = ($table_row_color == 'blues') ? 'shady' : 'blues';

    }

    echo "
      </tbody>
    </table>
    ";

    ?>
  </div>

</div>

<?php

//

// Footer
include ('./in.footer.php');
