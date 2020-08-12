<!DOCTYPE html>
<html>
<head>
  <!-- CSS file included as <link> -->
  <link href="style.css" rel="stylesheet" type="text/css" />
</head>
<body>

  <!-- AJAX JavaScript from Mozilla Developer: https://developer.mozilla.org/en-US/docs/Learn/Forms/Sending_forms_through_JavaScript#Using_FormData_bound_to_a_form_element -->
  <script>
  window.addEventListener( "load", function () {
    function sendData() {
      const AJAX = new XMLHttpRequest(); // AJAX handler
      const FD = new FormData(form); // Bind to-send data to form element

      AJAX.addEventListener( "load", function(event) { // This runs when AJAX responds
        document.getElementById("ajax_changes").innerHTML = event.target.responseText;

        // Bind a new event listener every time the <form> is changed:
        form = document.getElementById( "ajaxForm" ); // Access <form id="ajaxForm">, id="ajaxForm" can be anything
        listenToForm(); // Listen to updated <form> after it is changed

      } ); // Change HTML on successful response

      AJAX.addEventListener( "error", function(event) { // This runs if AJAX fails
        document.getElementById("ajax_changes").innerHTML =  'Oops! Something went wrong.';
      } );

      AJAX.open("POST", "ajax_responder.php"); // Send data, ajax_responder.php can be any file or URL

      AJAX.send(FD); // Data sent is from the form

    } // sendData() function

    // Declare the variable the first time it is used, not a constant
    var form = document.getElementById("ajaxForm"); // Access <form id="ajaxForm">, id="ajaxForm" can be anything
    function listenToForm(){
      form.addEventListener( "submit", function(event) { // Takeover <input type="submit">
        event.preventDefault();
        sendData();
      } );
    }
    listenToForm();

  } );
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
      <input type="submit" value="Form AJAX!">
    </form>

  </div>

</body>
</html>
