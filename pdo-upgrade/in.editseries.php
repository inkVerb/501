<?php
// JavaScript for series editor
?>
<script>
// Show/hide the edit-series div
function seriesEditorShowHide() {
  var x = document.getElementById("edit-series-container");
  if (x.style.display === "block") {
    x.style.display = "none";
  } else {
    x.style.display = "block";
  }
}

// Hide edit-series
function seriesEditorHide() {
  document.getElementById("edit-series-container").style.display = "none";
  document.getElementById("uploadresponse").innerHTML = '';
}

// The editor content
function seriesEditor() { // These arguments can be anything, same as used in this function

    // Bind a new event listener every time the <form> is changed:
    const FORM = document.getElementById("edit-series-form");
    const AJAX = new XMLHttpRequest(); // AJAX handler
    const FD = new FormData(FORM); // Bind to-send data to form element

    AJAX.addEventListener( "load", function(event) { // This runs when AJAX responds
      document.getElementById("edit-series").innerHTML = event.target.responseText;
    } );

    AJAX.addEventListener( "error", function(event) { // This runs if AJAX fails
      document.getElementById("edit-series").innerHTML =  'Oops! Something went wrong.';
    } );

    AJAX.open("POST", "ajax.editseries.php");

    AJAX.send(FD); // Data sent is from the form

  } // seriesEditor() function

  // show/hide action link
  function showChangeButton(s_id) {
    var x = document.getElementById("make-edits-"+s_id);
    if (x.style.display === "inline") {
      x.style.display = "none";
    } else {
      x.style.display = "inline";
    }
  }

  // show/hide action link
  function showHideEdit(s_id) {
    var y = document.getElementById("e_buttons_"+s_id);
    if (y.style.display === "inline") {
      document.getElementById("change-cancel-"+s_id).innerHTML = 'Change';
      y.style.display = "none";
    } else {
      document.getElementById("change-cancel-"+s_id).innerHTML = 'Cancel';
      y.style.display = "inline";
    }
  }

  // The editor content
  function seriesSave(sID) { // These arguments can be anything, same as used in this function

      // Bind a new event listener every time the <form> is changed:
      const FORM = document.getElementById("series-edit-"+sID);
      const AJAX = new XMLHttpRequest(); // AJAX handler
      const FD = new FormData(FORM); // Bind to-send data to form element

      AJAX.addEventListener( "load", function(event) { // This runs when AJAX responds
        var jsonSeriesEditResponse = JSON.parse(event.target.responseText); // For contents from the form
        document.getElementById("edit-series-"+sID).innerHTML = jsonSeriesEditResponse["message"]; // Message
        if (jsonSeriesEditResponse["change"] == 'change') {
          showHideEdit(sID);// Hide the Save/Cancel buttons
          document.getElementById("input-name-"+sID).value = jsonSeriesEditResponse["name"]; // Update Name
          document.getElementById("input-slug-"+sID).value = jsonSeriesEditResponse["slug"]; // Update Slug

        }
        if (jsonSeriesEditResponse["new_rss"] == 'newrss') { // Hide the Save/Cancel buttons
           document.getElementById("rss-none-"+sID).innerHTML = "<i>refresh to see image</i>";
        }
        if (jsonSeriesEditResponse["new_podcast"] == 'newpodcast') { // Hide the Save/Cancel buttons
          document.getElementById("podcast-none-"+sID).innerHTML = "<i>refresh to see image</i>";
        }
      } );

      AJAX.addEventListener( "error", function(event) { // This runs if AJAX fails
        document.getElementById("edit-series-"+sID).innerHTML =  'Oops! Something went wrong.';
      } );

      AJAX.open("POST", "ajax.editseries.php");

      AJAX.send(FD); // Data sent is from the form

    } // seriesEditor() function

 </script>
<?php