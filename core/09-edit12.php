<?php

// Include our config (with SQL) up near the top of our PHP file
include ('./in.config.php');

// Include our piece functions
include ('./in.piecefunctions.php');

// Include our login cluster
$head_title = "Editor"; // Set a <title> name used next
$edit_page_yn = true; // Include JavaScript for TinyMCE?
$nologin_allowed = false; // Login required?
include ('./in.login_check.php');

// Include our POST processor
include ('./in.editprocess.php');

// JavaScript
?>
<div id="use_me"></div>
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
  </script>
<?php

// Our edit form
// New or update?
if (isset($piece_id)) { // Updating piece
  if ( (isset($editing_published_piece)) && ($editing_published_piece == true) ) {
    echo '<pre><a href="piece.php?p='.$piece_id.'" target="_blank">view on blog</a></pre>';
  } else {
    echo '<pre>(unpublished draft)</pre>';
  }
  echo '<pre><a href="piece.php?p='.$piece_id.'&preview" target="_blank">preview</a></pre>';
  echo '<form action="edit.php?p='.$piece_id.'" method="post" id="edit_piece">';
  echo '<input form="edit_piece" type="hidden" name="piece_id" value="'.$piece_id.'"><br>';
} else { // New piece
  echo '<form action="edit.php" method="post" id="edit_piece">';
}
// Finish the form
echo '</form>';

// Tell in.editprocess.php that this is a "Piece" form
echo '<input form="edit_piece" type="hidden" name="piece">';

// Title & Slug
echo 'Title: '.pieceInput('p_title', $p_title).'<br><br>';
if (isset($piece_id)) {
  echo 'URL: localhost/web/<a href="piece.php?p='.$piece_id.'&preview" id="p_slug_a" target="_blank">'.$p_slug.'</a>&nbsp;<button type="button" onclick="showSlugEdit();">edit</button>';
  echo '<div id="slug_edit" style="display: none;">&nbsp;&nbsp;'.pieceInput('p_slug', $p_slug).'
        <button type="button" title="Auto-set from Title" onclick="ajaxResetSlug(); offNavWarn(); showSlugEdit();" class="orange link-button" name="slug_reset">reset</button>
        <button type="button" title="Save and update slug" onclick="ajaxSaveDraft(); offNavWarn(); showSlugEdit();" name="save_draft">save</button>
        </div>';
}

echo '<br><br>';

// Content
echo 'Content:<br>'.pieceInput('p_content', $p_content).'<br><br>';

// Save button: first save or AJAX?
if (isset($piece_id)) {
  // Save AJAX for existing Piece
  echo '
  <input form="edit_piece" type="hidden" name="p_submit" value="Save draft">
  <button type="button" title="Save (Ctrl + S)" onclick="ajaxSaveDraft(); offNavWarn();" name="save_draft" id="save_draft" class="lt_button small" style="display: inline;">Save draft</button>
  &nbsp;'; // Space between the buttons

} else {

  // First Save for new Piece
  echo '
  <input form="edit_piece" type="submit" name="p_submit" id="save_draft" value="Save draft">
  &nbsp;'; // Space between the buttons

  // AJAX
  ?>
    <script>
      // Ctrl + S = submit Save on new Piece
      document.addEventListener("keydown", function(cs) {
        if ( (window.navigator.platform.match("Mac") ? cs.metaKey : cs.ctrlKey) && (cs.keyCode == 83) ) {
          cs.preventDefault(); // Stop it from doing what it normally does
          document.getElementById('save_draft').click();
        }
      }, false); // Ctrl + S capture
    </script>
  <?php
} // Save button

// Existing piece? (can't publish without saving once first)
if ( (isset($editing_existing_piece)) && ($editing_existing_piece == true) ) {
  // Editing a published piece?
  if ( (isset($editing_published_piece)) && ($editing_published_piece == true) ) {
    echo '<input form="edit_piece" type="submit" name="p_submit" value="Update">';
  } else {
    echo '<input form="edit_piece" type="submit" name="p_submit" value="Publish">';
  }
}

// AJAX save changes message
if (isset($piece_id)) {
  echo '&nbsp;<div id="ajax_save_draft_response" style="display: inline;"></div>';
}

// New line
echo '<br><br>';

// Type
$infomsg = '
<b>Page</b>: hides meta (After, Tags, Links), works in menues, appears as prominent link in "Series lists"<br><br>
<b>Post</b>: appears in blog lists';
echo 'Type:'.infoPop('type_info', $infomsg).'<br>'.pieceInput('p_type', $p_type).'<br><br>';

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
  echo 'Date live: '.
  pieceInput('p_live_yr', $p_live_yr).', '.
  pieceInput('p_live_mo', $p_live_mo).' '.
  pieceInput('p_live_day', $p_live_day).' @ '.
  pieceInput('p_live_hr', $p_live_hr).':'.
  pieceInput('p_live_min', $p_live_min).':'.
  pieceInput('p_live_sec', $p_live_sec).'<br><br>';
echo '
</div>';

?>
  <script>
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
  </script>
<?php

// Tags
$infomsg = 'Tags: comma-separated list;<br>only first three tags show in excerpts & blog pages';
echo 'Tags:'.infoPop('tags_info', $infomsg).'<br>'.pieceInput('p_tags', $p_tags).'<br><br>';

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
<b>2. Or se an HTML $a_tag tag</b><br>
- Title pulled after last | Pipe or // Doubleslash<br><br>
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

// JavaScript functions for editing existing pieces
if (isset($piece_id)) {
  ?>
  <div id="test_me"></div>
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
          as_json["p_links"] = edit_piece_json["p_links"];
          as_json["as_time"] = new Date().getTime(); // "Epoch" time in miliseconds from Jan 1, 1970
        // Save it to local storage
        localStorage.setItem('as_'+as_json["piece_id"], JSON.stringify(as_json)); // Save our JSON as a string, JS can't understand it but it will save and open easily
        document.getElementById("test_me").innerHTML = 'autosaving'+as_json["p_content"];//
      }
      // Run this when the page loads: check for inconsistency with current edit_piece form and last autosave
      window.addEventListener( "load", function () {
        const formData = new FormData(document.getElementById("edit_piece")); // Get data from our form
        var edit_piece_json = Object.fromEntries(formData); // Put our form data into a JSON object just to get the Piece ID
        const as_id = 'as_'+edit_piece_json["piece_id"]; // Set our localStorage autosave name
        // Create our to-test JSON string from the edit_piece form
        var curr_json = {}; // Create our JSON save-as object
        // Add each item to the as_json object (we don't need everything in the form, but we also need the time)
          curr_json["piece_id"] = edit_piece_json["piece_id"];
          curr_json["p_title"] = edit_piece_json["p_title"];
          curr_json["p_slug"] = edit_piece_json["p_slug"];
          curr_json["p_content"] = edit_piece_json["p_content"];
          curr_json["p_after"] = edit_piece_json["p_after"];
          curr_json["p_links"] = edit_piece_json["p_links"];
          var curr = JSON.stringify(curr_json);
        if (localStorage.getItem(as_id) === null) { // If there is no autosave
          // Autosave every 30 seconds
          setInterval( function () {
            pieceAutoSave()
          }, 10000);
        } else {
          var old_as = localStorage.getItem(as_id); // Use the Piece ID in localStorage name
          var old_as_json = JSON.parse(old_as); // Make it a JSON-readable Object
          var old_as_time = old_as_json["as_time"];
          delete old_as_json["as_time"]; // Get rid of the time
          old_as = JSON.stringify(old_as_json);// Reset the string
          var edit_piece = JSON.stringify(old_as_json);
          // See if different
          if (old_as === curr) {
            // Autosave every 30 seconds
            setInterval( function () {
              pieceAutoSave()
            }, 10000);
          } else {
            document.getElementById("test_me").innerHTML = 'something has changed<br>old:<br>'+old_as+'<br>new:<br>'+curr;///
          }
        }

      });
      // Send Autosave to history
      function histAutoSave() {
        const formData = new FormData(document.getElementById("edit_piece")); // Get data from our form
        var edit_piece_json = Object.fromEntries(formData); // Put our form data into a JSON object just to get the Piece ID
        var old_as = localStorage.getItem('as_'+edit_piece_json["piece_id"]); // Use the Piece ID in localStorage name
        var old_as_json = JSON.parse(old_as);
        /// Send via POST to hist.php
      }

      // AJAX
      function ajaxSaveDraft() { // These arguments can be anything, same as used in this function
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
          offNavWarn(); // Turn off the nav away warning

          // Update the Autosave
          pieceAutoSave();
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
          ajaxSaveDraft('ajax.edit.php'); // Run our "Save" AJAX
        }
      }, false); // Ctrl + S capture
    </script>
  <?php

}

// Footer
include ('./in.footer.php');
