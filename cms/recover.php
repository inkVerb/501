<!DOCTYPE html>
<html>
<head>
  <!-- CSS file included as <link> -->
  <link href="style.css" rel="stylesheet" type="text/css" />
</head>
<body>

<?php

// Include our config (with SQL) up near the top of our PHP file
include ('./in.db.php');
include ('./in.head.php');

// Include our functions
include ('./in.functions.php');

// Include our recover cluster
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

  // Include our POST checks
  include ('./in.checks.php');
  if (($no_form_errors == true) && (!empty($favnumber)) && (!empty($username))) {
    $checks_out = true;

    // if SELECT: Query user info from the database if everything checks out
    $username_trim = DB::trimspace($username);
    $favnumber_trim = DB::trimspace($favnumber);
    $query = $database->prepare("SELECT id FROM users WHERE username=:username AND favnumber=:favnumber");
    $query->bindParam(':username', $username_trim);
    $query->bindParam(':favnumber', $favnumber_trim);
    $rows = $pdo->exec_($query);
    // Check to see that our SQL query returned exactly 1 row
    if ($pdo->numrows == 1) {
      foreach ($rows as $row){
        // Assign the values
        $user_id = "$row->id";
      }
    } else { // Favorite number fail
      echo '<p class="error">Recovery error!</p>';
    }

    // Our page content for recovery-verified users

    // Start a counter first time
    if (!isset($_SESSION['count'])) {
      $_SESSION['count'] = 1;
    // Increse the counter on each reload
    } else {
      $_SESSION['count'] = $_SESSION['count'] + 1;
    }
    $count = $_SESSION['count'];


    // Title etc
    echo "<h1>Random string to recover login:</h1>";
    echo "SESSION count: $count (times reloaded, not AJAXed)<br>";

    // Our recovery message
    echo "<p>Get a link to recover your account...</p>";
    echo "<hr><br>";

    // echo our AJAX JavaScript
    echo '  <!-- AJAX JavaScript code included as <script> -->
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

          ajaxHandler.open("POST", "ajax_string.php", true); // GET changed to POST
          ajaxHandler.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
          ajaxHandler.send("rsuid='.$user_id.'&ajax_token='.$ajax_token.'");
        }
      </script>';

    // echo our AJAX string button
    echo '
    <div id="ajax_changes">[String link will appear here] <i>(replaced by the AJAX)</i></div>
    <br>
    <button onclick="doAjax();">Get your recovery string link... (AJAX)</button>
    ';

    // end our AJAX section with another line
    echo "<br><br><hr>";

  } else { // Favorite number fail
    echo '<p class="error">Form error! Go back and try again.</p>';
  }


// Recovery form
} else {

  echo '<h1>Recover login</h1>
  <form action="recover.php" method="post">';

  echo 'Username: '.formInput('username', $username, $check_err).'<br><br>';
  echo 'Favorite Number: '.formInput('favnumber', $favnumber, $check_err).'<br><br>';

  echo '
    <input type="submit" value="Submit Button">
  </form>';

}


?>

</body>
</html>
