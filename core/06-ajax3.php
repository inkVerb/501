<!DOCTYPE html>
<html>
<head>
  <!-- CSS file included as <link> -->
  <link href="style.css" rel="stylesheet" type="text/css" />

  <!-- AJAX JavaScript code included as <script> -->
  <script>
    function vipAjax() { // vipAjax can be anything
      var ajax;
      ajax = new XMLHttpRequest();
      return ajax;
    }

    function doAjax() { // doAjax can be anything
      var ajaxHandler = vipAjax();
      ajaxHandler.onreadystatechange = function() {
        if (ajaxHandler.readyState == 4 && ajaxHandler.status == 200) {

          // ajax_thing can be anything, it also is the HTML id

          document.getElementById("ajax_thing").innerHTML = ajaxHandler.responseText;
        }
      }

      ajaxHandler.open("POST", "ajax_source.php", true); // GET changed to POST
      ajaxHandler.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
      ajaxHandler.send("go=AJAX&time=5");
    }
  </script>

</head>
<body>

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

echo '
<div id="some_thing">Here always</div>
<div id="ajax_thing">Replace me with AJAX</div>
<button onclick="doAjax();">Go AJAX!</button>
';

?>
</body>
</html>
