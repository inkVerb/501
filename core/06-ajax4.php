<?php
// Start a SESSION to survive page reloads
session_start();

// Set our variables
$post_foo = 'Render this AJAX JavaScript code';
$post_bar = "The 'Time' is: whenever you're ready";

// Start a counter first time
if (!isset($_SESSION['count'])) {
  $_SESSION['count'] = 1;

// Increse the counter on each reload
} else {
  $_SESSION['count'] = $_SESSION['count'] + 1;
}

$count = $_SESSION['count'];

// Echo our entire page so we can put variables into the JavaScript function
?>
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

          // ajax_changes can be anything, it also is the HTML id

          document.getElementById("ajax_changes").innerHTML = ajaxHandler.responseText;
        }
      }

      ajaxHandler.open("POST", "ajax_responder.php", true); // POST could be GET
      ajaxHandler.setRequestHeader("Content-type", "application/x-www-form-urlencoded"); // Needed to contruct our own <form> on next line
      ajaxHandler.send(<?php echo '"foo='.$post_foo.'&bar='.$post_bar.'"'; ?>);
    }
  </script>

</head>
<body>

<?php echo '$_SESSION[\'count\']: '.$count.'<br>'; ?>

<div id="some_thing">Here always</div>
<div id="ajax_changes">Replace me with AJAX</div>
<button onclick="doAjax();">Go AJAX!</button>

</body>
</html>
