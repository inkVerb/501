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
function seriesEditor(uID, pageNum = 0, detailMessage = '') { // These arguments can be anything, same as used in this function

    // Bind a new event listener:
    const AJAX = new XMLHttpRequest(); // AJAX handler

    AJAX.addEventListener( "load", function(event) { // This runs when AJAX responds
      document.getElementById("edit-series").innerHTML = event.target.responseText;
    } );

    AJAX.addEventListener( "error", function(event) { // This runs if AJAX fails
      document.getElementById("edit-series").innerHTML =  'Oops! Something went wrong.';
    } );

    AJAX.open("POST", "ajax.editseries.php");
    AJAX.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    AJAX.send("u_id="+uID+"&r="+pageNum+"&m="+detailMessage); // Data as could be sent in a <form>

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
  // For the Default series, the "Permanently delete series" checkbox and <div> do not exist, so running a JavaScript action for it would break the above line also
  // So, we must check to see if the checkbox <div> even exists, only then we run the script action to change it
  function showHideEdit(s_id) {
    var y = document.getElementById("e_buttons_"+s_id);
    if (y.style.display === "inline") {
      document.getElementById("change-cancel-"+s_id).innerHTML = 'change';
      y.style.display = "none";
    } else {
      document.getElementById("change-cancel-"+s_id).innerHTML = 'cancel';
      y.style.display = "inline";
    }
    if (elementExists = document.getElementById("delete-checkbox-"+s_id)) {
      var x = document.getElementById("delete-checkbox-"+s_id);
      if (x.style.display === "inline") {
        x.style.display = "none";
      } else {
        x.style.display = "inline";
      }
    }
  }

  // The editor save
  function seriesSave(sID) { // These arguments can be anything, same as used in this function

      // Bind a new event listener every time the <form> is changed:
      const FORM = document.getElementById("series-edit-"+sID);
      const AJAX = new XMLHttpRequest(); // AJAX handler
      const FD = new FormData(FORM); // Bind to-send data to form element

      AJAX.addEventListener( "load", function(event) { // This runs when AJAX responds
        var jsonSeriesEditResponse = JSON.parse(event.target.responseText); // For contents from the form
        if (jsonSeriesEditResponse["change"] == 'change') { // Name and/or slug change
          showHideEdit(sID);// Hide the Save/Cancel buttons
          document.getElementById("input-name-"+sID).value = jsonSeriesEditResponse["name"]; // Update Name
          document.getElementById("input-slug-"+sID).value = jsonSeriesEditResponse["slug"]; // Update Slug
        } else if ((jsonSeriesEditResponse["change"] == 'nochange') && (jsonSeriesEditResponse["upload"] == 'delete')) { // Deleting images
          showHideEdit(sID);// Hide the Save/Cancel buttons
          document.getElementById("rss-none-"+sID).innerHTML = "<i>no image</i>";
          document.getElementById("rss-none-"+sID).style.display = "inline";
          document.getElementById("rss-image-"+sID).style.display = "none";
          document.getElementById("podcast-none-"+sID).innerHTML = "<i>no image</i>";
          document.getElementById("podcast-none-"+sID).style.display = "inline";
          document.getElementById("podcast-image-"+sID).style.display = "none";
          document.getElementById("pro-delete-"+sID).checked = false;
        } else if ((jsonSeriesEditResponse["change"] == 'nochange') && (jsonSeriesEditResponse["upload"] == 'failed')) { // Upload failed
          showHideEdit(sID); // Hide the Save/Cancel buttons
        } else if (jsonSeriesEditResponse["change"] == 'delete') { // Deleting series
          showHideEdit(sID); // Hide the Save/Cancel buttons
          document.getElementById("v_row_"+sID).innerHTML = '<td></td><td></td><td><div id="edit-message-'+sID+'"></div></td><td></td><td></td>';
          document.getElementById("v_row_"+sID).classList.remove("shady");
          document.getElementById("v_row_"+sID).classList.remove("blues");
          document.getElementById("v_row_"+sID).classList.add("deleting");
        }
        if (jsonSeriesEditResponse["new_rss"] == 'newrss') { // Hide the Save/Cancel buttons
           document.getElementById("rss-none-"+sID).innerHTML = "<i>refresh to see image</i>";
        }
        if (jsonSeriesEditResponse["new_podcast"] == 'newpodcast') { // Hide the Save/Cancel buttons
          document.getElementById("podcast-none-"+sID).innerHTML = "<i>refresh to see image</i>";
        }
        // Every scenario is considered, not update <div id="edit-message-ID"> with our AJAX response message
        document.getElementById("edit-message-"+sID).innerHTML = jsonSeriesEditResponse["message"]; // Message
      } );

      AJAX.addEventListener( "error", function(event) { // This runs if AJAX fails
        document.getElementById("edit-series-"+sID).innerHTML =  'Oops! Something went wrong.';
      } );

      AJAX.open("POST", "ajax.editseries.php");

      AJAX.send(FD); // Data sent is from the form

    } // seriesSave() function

  // Hide details-series
  function detailsEditorHide() {
    document.getElementById("edit-series-container").style.display = "none";
    document.getElementById("uploadresponse").innerHTML = '';
  }

  // The details content
  function detailsEditor(uID, sID) { // These arguments can be anything, same as used in this function

      // Bind a new event listener:
      const AJAX = new XMLHttpRequest(); // AJAX handler

      AJAX.addEventListener( "load", function(event) { // This runs when AJAX responds
        document.getElementById("edit-series").innerHTML = event.target.responseText;
      } );

      AJAX.addEventListener( "error", function(event) { // This runs if AJAX fails
        document.getElementById("edit-series").innerHTML =  'Oops! Something went wrong.';
      } );

      AJAX.open("POST", "ajax.editseriesdetails.php");
      AJAX.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
      AJAX.send("u_id="+uID+"&s_id="+sID); // Data as could be sent in a <form>

    } // detailsEditor() function

  // The details save
  function detailsSave(sID, uID) { // These arguments can be anything, same as used in this function

      // Bind a new event listener every time the <form> is changed:
      const FORM = document.getElementById("series-details");
      const AJAX = new XMLHttpRequest(); // AJAX handler
      const FD = new FormData(FORM); // Bind to-send data to form element

      AJAX.addEventListener( "load", function(event) { // This runs when AJAX responds
        document.getElementById("edit-series").innerHTML = event.target.responseText;
        // DEV All comments are to see the response for dev purposes
        var jsonSeriesEditResponse = JSON.parse(event.target.responseText); // For contents from the form
        var detailMessage = jsonSeriesEditResponse["message"];
        seriesEditor(uID, 0, detailMessage); // Reload the Series Editor

      } );

      AJAX.addEventListener( "error", function(event) { // This runs if AJAX fails
        document.getElementById("edit-series-"+sID).innerHTML =  'Oops! Something went wrong.';
      } );

      AJAX.open("POST", "ajax.editseriesdetails.php");

      AJAX.send(FD); // Data sent is from the form

    } // detailsSave() function

 </script>
<?php
