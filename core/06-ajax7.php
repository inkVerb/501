<!DOCTYPE html>
<html>
<head>
  <!-- CSS file included as <link> -->
  <link href="style.css" rel="stylesheet" type="text/css" />
</head>
<body>

  <script>
    function ajaxFormData(formID, postTo, ajaxUpdate) { // These arguments can be anything, same as used in this function
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

    } // ajaxFormData() function
  </script>

<?php
// Start a SESSION to survive page reloads
session_start();

// Start a counter first time
if (!isset($_SESSION['count'])) {
  $_SESSION['count'] = 1;

// Increse the counter on each reload
} else {
  $_SESSION['count'] = $_SESSION['count'] + 1;
}

$count = $_SESSION['count'];

echo "SESSION count: $count<br>";

?>
  <div id="some_thing">Here always</div>
  <div id="ajax_changes">Replace me with AJAX<br>

    <form id="ajaxForm">
      <input type="text" value="AJAX" name="foo">
      <input type="text" value="5" name="bar">
      <button type="button" onclick="ajaxFormData('ajaxForm', 'ajax_responder.php', 'ajax_changes');">Button Form AJAX!</button>
      <input type="submit" value="Submit Form non-AJAX!">
    </form>

  </div>

</body>
</html>
