<?php

// Include our config (with SQL) up near the top of our PHP file
include ('./in.db.php');

// Include our piece functions
include ('./in.piecefunctions.php');

// Include our login cluster
$heading = ""; // Setting no title, our users know where they are
$head_title = "Editor";
$edit_page_yn = true; // Include JavaScript for TinyMCE?
$nologin_allowed = false; // Login required?
include ('./in.logincheck.php');
include ('./in.head.php');

// Include our POST processor
include ('./in.editprocess.php');

?>

<!-- Page container -->
<div id="editor-body">

<!-- Div for media insert -->
<div id="media-insert-container" style="display:none;">
  <!-- Close button -->
  <div id="media-insert-closer" onclick="mediaInsertHide();" title="close"><b>&#xd7;</b></div>
  <!-- Dropzone -->
  <div id="media-upload">

    <form id="dropzone-uploader-media-insert" class="dropzone ml" action="upload.php" method="post" enctype="multipart/form-data"></form>

    <!-- AJAX response from upload.php will go here-->
    <div id="uploadresponse"></div>
  </div>
  <br>
  <!-- AJAX mediaInsert HTML entity -->
  <div id="media-insert"></div>
</div>

<!-- Sidebar for meta -->
<div id="editor-meta-bar">
  <?php


  // Save button: first save or AJAX?
  if (isset($piece_id)) {
    // Save AJAX for existing Piece
    echo '
    <input form="edit_piece" type="hidden" id="p_submit_edit" name="p_submit" value="">
    <button type="button" title="Save (Ctrl + S)" onclick="ajaxSaveDraft(); offNavWarn();" name="save_draft" id="save_draft" class="lt_button small" style="display: inline;">Save draft</button>
    &nbsp;'; // Space between the buttons

  } else {

    // First Save for new Piece
    echo '
    <input form="edit_piece" type="submit" onclick="offNavWarn(); var f=this; setTimeout(function(){f.disabled=true;}, 0); return true;" name="p_submit" id="save_draft" value="Save draft">
    &nbsp;'; // Space between the buttons

    // AJAX false triggers in new piece
    ?>
      <script>
        // Ctrl + S = submit Save on new Piece
        document.addEventListener("keydown", function(cs) {
          if ( (window.navigator.platform.match("Mac") ? cs.metaKey : cs.ctrlKey) && (cs.keyCode == 83) ) {
            cs.preventDefault(); // Stop it from doing what it normally does
            document.getElementById('save_draft').click();
          }
        }, false); // Ctrl + S capture

        // False AJAX save from TinyMCE Ctrl + S hotkey when AJAX has no piece ID to process
        function ajaxSaveDraft() {
          document.getElementById('save_draft').click();
        }
      </script>
    <?php
  } // Save button

  // Existing piece? (can't publish without saving once first)
  if ( (isset($editing_existing_piece)) && ($editing_existing_piece == true) ) {
    // Editing a published piece?
    if ( (isset($editing_published_piece)) && ($editing_published_piece == true) ) {
      echo '
      <button type="button" title="Save (Ctrl + S)" onclick="buttonFormSubmit(\'Update\');" name="edit_update" id="edit_update_button" class="lt_button small" style="display: inline;">Update publication</button>';

    } else {
      echo '
      <button type="button" title="Save (Ctrl + S)" onclick="buttonFormSubmit(\'Publish\');" name="edit_update" id="edit_publish_button" class="lt_button small" style="display: inline;">Publish</button>';

    }
  }

  // AJAX save changes message
  if (isset($piece_id)) {
    echo '<br><br><div id="ajax_save_draft_response" style="display: inline;"></div><br>';
  }

  // Recovered Autosave?
  if (isset($_SESSION['as_recovered'])) {
    echo '<pre class="green">Autosave recovered.</pre>';
    unset($_SESSION['as_recovered']);
  }

  // New or update?
  if (isset($piece_id)) { // Updating piece
    // Unpublished changes to draft?
    $query = $database->prepare("SELECT P.date_updated FROM pieces AS P LEFT JOIN publications AS U ON P.id=U.piece_id AND P.date_updated=U.date_updated WHERE U.piece_id=:piece_id ORDER BY U.id DESC LIMIT 1");
    $query->bindParam(':piece_id', $piece_id);
    $pdo->exec_($query);
    $draft_diff = ($pdo->numrows == 0) ? '<pre class="orange"><a href="hist.php?p='.$piece_id.'">view diff</a> for unpublished changes</pre>' : '';
    echo $draft_diff;

    // Other notices and relevant links
    if ( (isset($editing_published_piece)) && ($editing_published_piece == true) ) {
      echo '<pre><a href="piece.php?p='.$piece_id.'" target="_blank">view on blog</a></pre>';
    } else {
      echo '<pre>(unpublished draft)</pre>';
    }
    // Preview & change notices
    echo '<pre><a href="piece.php?p='.$piece_id.'&preview" target="_blank">preview</a></pre>';
    echo '<div id="edit_changes_notice"></div>';
    // Our edit form
    echo '<form action="edit.php?p='.$piece_id.'" method="post" name="edit_piece" id="edit_piece">';
    echo '<input form="edit_piece" type="hidden" name="piece_id" value="'.$piece_id.'"><br>';
  } else { // New piece
    echo '<form action="edit.php" method="post" name="edit_piece" id="edit_piece" id="edit_piece">';
  }
  // Finish the form
  echo '</form>';

  // Type
  $infomsg = '
  <b>Page</b>: hides meta (After, Tags, Links), works in menues, appears as prominent link in "Series lists"<br><br>
  <b>Post</b>: appears in blog lists';
  echo 'Type:'.infoPop('type_info', $infomsg).'<br>'.pieceInput('p_type', $p_type).'<br><br>';

  // Tags
  $infomsg = 'Tags: comma-separated list;<br>only first three tags show in excerpts & blog pages';
  echo 'Tags:'.infoPop('tags_info', $infomsg).'<br>'.pieceInput('p_tags', $p_tags).'<br><br>';

  // Series
  $infomsg = 'Exclusive "category" -like label, Pieces of a Series may appear together in some areas';
  echo 'Series:'.infoPop('series_info', $infomsg).'<br><br>';

    // Set necessary values
    // Set a default Series, probably from settings table
    $de_series = (isset($_SESSION['de_series'])) ? $_SESSION['de_series'] : 1;

    // Accept any set value
    $p_series = (isset($p_series)) ? $p_series : $de_series;
    include ('./in.series.php');

  // Schedule
  // Clickable <label for="CHECKBOX_ID"> doesn't work well with two "onClick" JavaScript functions, so we need extra JavaScript
  echo pieceInput('p_live_schedule', $p_live_schedule).'<label onclick="showGoLiveOptionsLabel()"> Scheduled...</label><br><br>';
  echo '<div id="goLiveOptions" '.($p_live_schedule == true ? 'style="display:block"' : 'style="display:none"').'>';
    echo 'Live: '.
    pieceInput('p_live_yr', $p_live_yr).', '.
    pieceInput('p_live_mo', $p_live_mo).' '.
    pieceInput('p_live_day', $p_live_day).' @ '.
    pieceInput('p_live_hr', $p_live_hr).':'.
    pieceInput('p_live_min', $p_live_min).':'.
    pieceInput('p_live_sec', $p_live_sec).'<br><br>';
  echo '
  </div>';

  ?>
<!-- End sidebar for meta -->
</div>

<!-- Sidebar for main content -->
<div id="editor-main-content">
  <?php

  // Tell in.editprocess.php that this is a "Piece" form
  echo '<input form="edit_piece" type="hidden" name="piece">';

  // Title & Slug
  echo pieceInput('p_title', $p_title).'<br><br>';
  if (isset($piece_id)) {
    echo 'localhost/web/<a href="piece.php?p='.$piece_id.'&preview" id="p_slug_a" target="_blank">'.$p_slug.'</a>&nbsp;<button type="button" onclick="showSlugEdit();">edit</button>';
    echo '<div id="slug_edit" style="display: none;">&nbsp;&nbsp;'.pieceInput('p_slug', $p_slug).'
          <button type="button" title="Auto-set from Title" onclick="ajaxResetSlug(); offNavWarn(); showSlugEdit();" class="orange link-button" name="slug_reset">reset</button>
          <button type="button" title="Save and update slug" onclick="ajaxSaveDraft(); offNavWarn(); showSlugEdit();" name="save_draft">save</button>
          </div>';
    echo '<br><br>';
  }

  // Content (no label)
  // Content
  echo pieceInput('p_content', $p_content);

  // AJAX mediaInsert button
  echo '<form id="media-insert-form">
      <input type="hidden" name="u_id" value="'.$user_id.'">
      <button type="button" class="postform link-button inline orange" onclick="mediaInsert(); mediaInsertShowHide();"><small>insert from media library</small></button>
    </form><br>';

  // After
  $infomsg = 'After: unstyled text, HTML not allowed';
  echo 'After:'.infoPop('after_info', $infomsg).'<br>'.pieceInput('p_after', $p_after).'<br><br>';

  // Links
  $string1 = htmlspecialchars('<a href="https://inkisaverb.com">Ink is a verb.</a>');
  $string2 = htmlspecialchars('<a href="https://verb.vip">Get inking. // VIP Linux</a>');
  $string3 = htmlspecialchars('<a href="http://poetryiscode.com">Poetry is code. | piC</a>');
  $a_tag = htmlspecialchars('<a>');
  $infomsg =
  "
  <big>Links</big><br>
  <code>
  <b>1. Separate [url] [title] [credit] via ;;</b><br>
  - In any order on a line ([title] before [credit])<br>
  - Only [url] is required<br>
  - If no [credit], Credit can be pulled after a | Pipe from [title]<br>
  - All else after | Pipe gets truncated<br><br>
  <b>2. Or use an HTML $a_tag tag</b><br>
  - title= attribute pulled after last | Pipe or // Doubleslash<br><br>
  <b>Examples:</b><br>
  https://verb.one<br>
  https://verb.red ;;Get inking.<br>
  https://verb.ink;; Ink is a verb.;;inkVerb<br>
  https://verb.blue;; Inky | Blue Ink<br>
  $string1<br>
  $string2<br>
  $string3<br>
  </code>
  ";
  echo 'Links:'.infoPop('links_info', $infomsg).'<br>'.pieceInput('p_links', $p_links).'<br><br>';


  ?>

<!-- End sidebar for main content -->
</div>

<!-- End page container -->
</div>

<!-- JavaScript for after document -->
<script src="dropzone.min.js"></script>

<script>
  // Navigate away warning (used by pieceInput PHP function)
	function onNavWarn() {
    // Normal inputs
		window.onbeforeunload = function() {
      // We're done
			return true;
		};
	}
	function offNavWarn() {
		window.onbeforeunload = null;
	}

  // Disable "Enter" key on forms
  window.addEventListener('keydown',function(e){if(e.keyIdentifier=='U+000A'||e.keyIdentifier=='Enter'||e.keyCode==13){if(e.target.nodeName=='INPUT'&&e.target.type=='text'){e.preventDefault();return false;}}},true);

  // Show/hide the media-edit div
  function mediaInsertShowHide() {
    var x = document.getElementById("media-insert-container");
    if (x.style.display === "block") {
      x.style.display = "none";
    } else {
      x.style.display = "block";
    }
  }

  // Open the media insert, populate via AJAX
  function mediaInsert() { // These arguments can be anything, same as used in this function

    // Bind a new event listener every time the <form> is changed:
    const FORM = document.getElementById("media-insert-form");
    const AJAX = new XMLHttpRequest(); // AJAX handler
    const FD = new FormData(FORM); // Bind to-send data to form element

    AJAX.addEventListener( "load", function(event) { // This runs when AJAX responds
      document.getElementById("media-insert").innerHTML = event.target.responseText;
    } );

    AJAX.addEventListener( "error", function(event) { // This runs if AJAX fails
      document.getElementById("media-insert").innerHTML =  'Oops! Something went wrong.';
    } );

    AJAX.open("POST", "ajax.mediainsert.php");

    AJAX.send(FD); // Data sent is from the form

  } // mediaInsert() function

  // Hide media-insert
  function mediaInsertHide() {
    document.getElementById("media-insert-container").style.display = "none";
    document.getElementById("uploadresponse").innerHTML = '';
  }
  // Dropzone settings
  Dropzone.options.dropzoneUploaderMediaInsert = { // JS: .dropzoneUploader = HTML: id="dropzone-uploader"
    dictDefaultMessage: 'Drop to upload!',
    paramName: "upload_file", // Becomes $_FILES['upload_file']; default: "file"
    maxFilesize: 100, // MB
    uploadMultiple: true, // Default: false
    maxFiles: 50,
    parallelUploads: 1, // Default: 2
    addRemoveLinks: true, // Default: false
    dictCancelUpload: "cancel", // Cancel before upload starts text
    dictRemoveFile: "hide", // We don't have this set to delete the file since we will manage that ourselves, but it can hide the message in the Dropzone area

    // File types ported over from upload.php, redundant but consistent:
    acceptedFiles: "image/jpeg, image/png, image/gif, image/svg+xml, image/bmp, image/x-windows-bmp, image/x-ms-bmp, video/webm, video/x-theora+ogg, video/ogg, video/mp4, video/x-flv, video/x-msvideo, video/x-matroska, video/quicktime, audio/mpeg, audio/ogg, audio/x-wav, audio/wav, audio/x-flac, audio/flac, text/plain, text/html, .md, application/msword, application/vnd.openxmlformats-officedocument.wordprocessingml.document, application/vnd.oasis.opendocument.text, application/x-pdf, application/pdf",

    // Initiation
    init: function() {
      // Refresh Dropzone box after upload
      this.on("complete", function(file) {
         this.removeAllFiles(true);
      });

      // Process AJAX success from upload.php
      this.on('success', function(file) {

        // Just AJAX-refresh the mini Media Library Insert list, no need to handle responses from upload.php
        mediaInsert();

      });

    } // End initialization

  };
  // End Dropzone settings

  // Show slug edit
  function showSlugEdit() {
    var x = document.getElementById("slug_edit");
    if (x.style.display === "inline") {
      x.style.display = "none";
    } else {
      x.style.display = "inline";
    }
  }
  // Check/uncheck the box = hide/show the Date Live schedule (p_live_schedule) <div>
  function showGoLiveOptionsBox() {
    var x = document.getElementById("goLiveOptions");
    if (x.style.display === "block") {
      x.style.display = "none";
    } else {
      x.style.display = "block";
    }
  }
  // JavaScript does not allow onClick action for both the label and the checkbox
  // So, we make the label open the Date Live schedule div AND check the box...
  function showGoLiveOptionsLabel() {
    // Show the Date Live schedule div
    var x = document.getElementById("goLiveOptions");
    if (x.style.display === "block") {
      x.style.display = "none";
    } else {
      x.style.display = "block";
    }
    // Use JavaScript to check the box
    var y = document.getElementById("p_live_schedule");
    if (y.checked === false) {
      y.checked = true;
    } else {
      y.checked = false;
    }
  }

  // JavaScript for AJAX mediaEdit
    // Open the media editor, populate via AJAX
    function mediaEdit(formID, postTo, ajaxLoad, save_message='') { // These arguments can be anything, same as used in this function

      // Show the media-edit div
      document.getElementById("media-insert-editor-container").style.display = "block";

      // Bind a new event listener every time the <form> is changed:
      const FORM = document.getElementById(formID); // <form> by ID to access, formID is the JS argument in the function
      const AJAX = new XMLHttpRequest(); // AJAX handler
      const FD = new FormData(FORM); // Bind to-send data to form element

      AJAX.addEventListener( "load", function(event) { // This runs when AJAX responds
        document.getElementById(ajaxLoad).innerHTML = event.target.responseText; // HTML element by ID to update, ajaxLoad is the JS argument in the function
      } );

      AJAX.addEventListener( "error", function(event) { // This runs if AJAX fails
        document.getElementById(ajaxLoad).innerHTML =  'Oops! Something went wrong.';
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
        mediaEdit('mediaEdit_'+m_id, 'ajax.mediainfoinsert.php', 'media-insert-editor');

        // Show the message
        document.getElementById("media-insert-editor-saved-message").innerHTML = jsonMetaEditResponse["message"];

        // Style the media type in the Media Library table
        document.getElementById("mediatype_"+m_id).classList.add('orange');
        document.getElementById("upload_"+m_id).classList.remove('blue');
      } );

      AJAX.addEventListener( "error", function(event) { // This runs if AJAX fails
        document.getElementById("media-insert-editor-saved-message").innerHTML =  'Oops! Something went wrong.';
      } );

      AJAX.open("POST", "ajax.mediainfoinsert.php"); // Send data, postTo is the .php destination file, from the JS argument in the function

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
        mediaEdit('mediaEdit_'+m_id, 'ajax.mediainfoinsert.php', 'media-insert-editor');

        // Show the message
        document.getElementById("media-insert-editor-saved-message").innerHTML = jsonMetaEditResponse["message"];

        // Style & update the name in the Media Library table
        document.getElementById("filename_"+m_id).innerHTML = jsonMetaEditResponse["file_name"];
        document.getElementById("filename_"+m_id).classList.add('orange');
        document.getElementById("upload_"+m_id).classList.remove('blue');
      } );

      AJAX.addEventListener( "error", function(event) { // This runs if AJAX fails
        document.getElementById("media-insert-editor-saved-message").innerHTML =  'Oops! Something went wrong.';
      } );

      AJAX.open("POST", "ajax.mediainfoinsert.php"); // Send data, postTo is the .php destination file, from the JS argument in the function

      AJAX.send(FD); // Data sent is from the form

    } // nameChange() function

    // Close the media editor
    function mediaEditorClose() {
      document.getElementById("media-insert-editor-container").style.display = "none";
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

  // Functions for inserting media items

  function addImageToTiny(thisFile, thisAlt='', thisTitle='', w='', h='') {
    tinymce.activeEditor.insertContent('<img alt="'+thisAlt+'" title="'+thisTitle+'" width="'+w+'" height="'+h+'" src="http://localhost/web/'+thisFile+'">');
  }

  function addVideoToTiny(thisFile, mimeType='', h='', w='', poster='') {
    tinymce.activeEditor.insertContent('<video height="'+h+'" width="'+w+'" controls><source src="http://localhost/web/'+thisFile+'" type="'+mimeType+'">Not supported.</video> ');
  }

  function addAudioToTiny(thisFile, mimeType='') {
    tinymce.activeEditor.insertContent('<audio controls><source src="http://localhost/web/'+thisFile+'" type="'+mimeType+'">Not supported.</audio> ');
  }

  function addDocToTiny(thisFile, thisName, thisTitle='') {
    tinymce.activeEditor.insertContent('<a alt="'+thisTitle+'" title="'+thisTitle+'" href="http://localhost/web/'+thisFile+'">'+thisName+'</a>');
  }

</script>

<?php
// JavaScript functions for editing existing pieces
if (isset($piece_id)) {
  ?>
    <script>
      // Autosave
      function pieceAutoSave() {
        var formData = new FormData(document.getElementById("edit_piece")); // Get data from our form
        var edit_piece_json = Object.fromEntries(formData); // Put our form data into a JSON object
        var as_json = {}; // Create our JSON save-as object
        // Add each item to the as_json object (we don't need everything in the form, but we also need the time)
          as_json["piece_id"] = edit_piece_json["piece_id"];
          as_json["p_title"] = edit_piece_json["p_title"];
          as_json["p_slug"] = edit_piece_json["p_slug"];
          as_json["p_content"] = edit_piece_json["p_content"];
          as_json["p_after"] = edit_piece_json["p_after"];
          as_json["p_tags"] = edit_piece_json["p_tags"];
          as_json["p_links"] = edit_piece_json["p_links"];
          as_json["as_time"] = new Date().getTime(); // "Epoch" time in miliseconds from Jan 1, 1970
        // Set our local storage ID
        as_id = 'as_'+as_json["piece_id"];
        // Check and trigger a nav warning if there are any changes in the Piece content
        if (localStorage.getItem(as_id) === null) { // If there is no autosave
          old_as_p_content = as_json["p_content"]; // This sets the new and old to be the same, a roundabout way of neutralizing the check if there is no autosave yet
        } else {
          var old_as = localStorage.getItem(as_id); // Use the Piece ID in localStorage name
          var old_as_json = JSON.parse(old_as); // Make it a JSON-readable Object
          var old_as_p_content = old_as_json["p_content"];
        }
        var new_as_p_content = as_json["p_content"];
        if (new_as_p_content != old_as_p_content) { // Is there a change?
          onNavWarn(); // Trigger the nav warning
        }
        // Save it to local storage
        localStorage.setItem(as_id, JSON.stringify(as_json)); // Save our JSON as a string, JS can't understand it but it will save and open easily
      }
      // Timer used by recurring Autosave
      function Timer(fn, t) {
        var timerObj = setInterval(fn, t);

        // Stop timer
        this.stop = function() {
            if (timerObj) {
                clearInterval(timerObj);
                timerObj = null;
            }
            return this;
        }

        // Start timer
        this.start = function() {
            if (!timerObj) {
                this.stop();
                timerObj = setInterval(fn, t);
            }
            return this;
        }

        // Stop current & reset
        this.reset = function(newT = t) {
            t = newT;
            return this.stop().start();
        }
      }
      // Set the Autosave to happen every 30 seconds via our Timer function (it doesn't start yet)
      var autoSaveTimer = new Timer(function() { // Defines and initiates in the same command
        pieceAutoSave()
      }, 30000);
      autoSaveTimer.stop(); // Stop it right away, just in case we don't want it running yet
      // Run this when the page loads: check for inconsistency with current edit_piece form and last autosave
      window.addEventListener( "load", function () {
        const formData = new FormData(document.getElementById("edit_piece")); // Get data from our form
        var edit_piece_json = Object.fromEntries(formData); // Put our form data into a JSON object just to get the Piece ID
        const as_id = 'as_'+edit_piece_json["piece_id"]; // Set our localStorage autosave name
        // Function to escape our diff
        function htmlchars(string) {
          return string
           .replace(/&/g, "&amp;")
           .replace(/</g, "&lt;")
           .replace(/>/g, "&gt;")
           .replace(/"/g, "&quot;")
           .replace(/'/g, "&#039;");
        }
        // Create our to-test JSON string from the edit_piece form
        var curr_json = {}; // Create our JSON save-as object
        // Add each item to the as_json object (we don't need everything in the form, but we also need the time)
          curr_json["piece_id"] = edit_piece_json["piece_id"];
          curr_json["p_title"] = edit_piece_json["p_title"];
          curr_json["p_slug"] = edit_piece_json["p_slug"];
          curr_json["p_content"] = edit_piece_json["p_content"];
          curr_json["p_after"] = edit_piece_json["p_after"];
          curr_json["p_tags"] = edit_piece_json["p_tags"];
          curr_json["p_links"] = edit_piece_json["p_links"];
          var curr = JSON.stringify(curr_json);
          var pID = edit_piece_json["piece_id"];
        if (localStorage.getItem(as_id) === null) { // If there is no autosave
          // Start the Autosave 30 second repeating Timer
          autoSaveTimer.start();
        } else {
          var old_as = localStorage.getItem(as_id); // Use the Piece ID in localStorage name
          var recover_as = htmlchars(old_as); // Escape it, but keep it unchanged in case we want to recover it, including the time, which we will soon delete
          var old_as_json = JSON.parse(old_as); // Make it a JSON-readable Object
          var old_as_time = old_as_json["as_time"];
          delete old_as_json["as_time"]; // Get rid of the time
          old_as = JSON.stringify(old_as_json);// Reset the string
          var edit_piece = JSON.stringify(old_as_json);
          // See if different
          if (old_as === curr) {
            // Start the Autosave 30 second repeating Timer
            autoSaveTimer.start();
          } else {
            var changesMessage = '<pre class="orange"><small>Connection issue or browser closed incorrectly:<br>Something has changed since last save!</small></pre><form id="save_diff_error" name="save_diff_error" method="post" action="hist.php?o='+pID+'&a=1"><input type="hidden" form="save_diff_error" name="old_as" value=\''+recover_as+'\'><input type="submit" class="orange" value="See diff...">&nbsp;<button type="button" class="red" onclick="dismissASdiff();">Dismiss forever</button></form>';
            document.getElementById("edit_changes_notice").innerHTML = changesMessage;
          }
        }

      });
      // Dismiss the Autosave diff warning by updateing the Autosave
      function dismissASdiff() {
        // Autosave update
        pieceAutoSave();

        // Turn off any nav away warning
        offNavWarn();

        // Reload the page
        location.reload();
        return false; // So we don't keep reloading forever
      }

      // Update/Publish buttons
      function buttonFormSubmit(p_sub) { // These arguments can be anything, same as used in this function
        // Set the value of p_submit
        document.getElementById("p_submit_edit").value = p_sub;

        // Stop our Autosave Timer so it can't produce conflicts, it's no longer needed because we are going to POST away
        autoSaveTimer.stop();

        // Update our Autosave so it is up to date
        pieceAutoSave();

        // Turn off any nav away warning
        offNavWarn();

        // Submit the form
        document.edit_piece.submit();
      }

      // AJAX
      function ajaxSaveDraft() { // These arguments can be anything, same as used in this function
        // Update the Autosave
        pieceAutoSave();

        // Set the value of p_submit
        document.getElementById("p_submit_edit").value = "Save draft";

        // Bind a new event listener every time the <form> is changed:
        const FORM = document.getElementById("edit_piece");
        const AJAX = new XMLHttpRequest(); // AJAX handler
        const FD = new FormData(FORM); // Bind to-send data to form element

        AJAX.addEventListener( "load", function(event) { // This runs when AJAX responds

          document.getElementById("ajax_save_draft_response").innerHTML = event.target.responseText;

          // Parse our response
          var jsonSaveDraftResponse = JSON.parse(event.target.responseText); // For "slug" and "Save draft" message

          document.getElementById("ajax_save_draft_response").innerHTML = jsonSaveDraftResponse["message"];
          document.getElementById("p_slug").value = jsonSaveDraftResponse["slug"];
          document.getElementById("p_slug_a").innerHTML = jsonSaveDraftResponse["slug"];

          // Turn off the nav away warning (after pieceAutoSave may have triggered a now-unnecessary nav warning)
          offNavWarn();

          // Reset our Autosave 30 second repeating Timer
          autoSaveTimer.reset(30000);
        } );

        AJAX.addEventListener( "error", function(event) { // This runs if AJAX fails
          document.getElementById("ajax_save_draft_response").innerHTML =  'Oops! Something went wrong.';
        } );

        AJAX.open("POST", "ajax.edit.php");

        AJAX.send(FD); // Data sent is from the form

      } // ajaxSaveDraft() function

      // Reset slug
      function ajaxResetSlug() {
        document.getElementById("p_slug").value = '';
        ajaxSaveDraft();
      }

      // Ctrl + S = ajaxSaveDraft();
      document.addEventListener("keydown", function(cs) {
        if ( (window.navigator.platform.match("Mac") ? cs.metaKey : cs.ctrlKey) && (cs.keyCode == 83) ) {
          cs.preventDefault(); // Stop it from doing what it normally does
          ajaxSaveDraft(); // Run our "Save" AJAX
        }
      }, false); // Ctrl + S capture
    </script>
  <?php

}

// Footer
include ('./in.footer.php');
