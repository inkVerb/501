<?php

// Include our config (with SQL) up near the top of our PHP file
include ('./in.config.php');

// Include our login cluster
$head_title = 'Media Library'; // Set a <title> name used next
$edit_page_yn = false; // Include JavaScript for TinyMCE?
include ('./in.logincheck.php');
include ('./in.head.php');

?>
<script src="dropzone.min.js"></script>

<!-- Dropzone settings -->
<script>
  Dropzone.options.dropzoneUploader = { // JS: .dropzoneUploader = HTML: id="dropzone-uploader"
    dictDefaultMessage: 'Drop to upload!',
    paramName: "upload_file", // We are still using upload_file; default: file
    maxFilesize: 5, // MB
    addRemoveLinks: true, // Default: false
      dictCancelUpload: "cancel", // Cancel before upload starts text
      dictRemoveFile: "hide", // We don't have this set to delete the file since we will manage that ourselves, but it can hide the message in the Dropzone area

    // Process AJAX response from upload.php
    init: function() {
      this.on('success', function(file, responseText) {

        // Write the response to HTML element id="uploadresponse"
        document.getElementById("uploadresponse").innerHTML = '<b>'+file.name+' info:</b><br>'+responseText;

        // Show the filename and HTML response in an alert box for learning purposes
        alert(file.name+' :: UPLOAD MESSAGE :: '+responseText);

      });
    } // Process AJAX response

  };
</script>
<!-- End Dropzone settings -->

<form id="dropzone-uploader" class="dropzone ml" action='upload.php' method='post' enctype='multipart/form-data'></form>

<p id="uploadresponse"></p>
<?php

// Footer
include ('./in.footer.php');
