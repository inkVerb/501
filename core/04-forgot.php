<!DOCTYPE html>
<html>
<head>
  <!-- CSS file included as <link> -->
  <link href="style.css" rel="stylesheet" type="text/css" />
</head>
<body>

<?php

// Include our config (with SQL) up near the top of our PHP file
include ('./in.config.php');

// Include our functions
include ('./in.functions.php');

// We must be logged in!
// See if we have a cookie
if (isset($_COOKIE['user_id'])) {
  $user_id = $_COOKIE['user_id'];
  $user_id_sqlesc = escape_sql($user_id);
  $query = "SELECT fullname FROM users WHERE id='$user_id_sqlesc'";
  $call = mysqli_query($database, $query);
  // Check to see that our SQL query returned exactly 1 row
  if (mysqli_num_rows($call) == 1) {
    // Assign the values
    $row = mysqli_fetch_array($call, MYSQLI_NUM);
      $fullname = "$row[0]";

      // Set the $_SESSION array
      $_SESSION['user_id'] = $user_id;
      $_SESSION['full_name'] = $fullname;

      // Show a message
      echo "<h1>Cookie Login, Seriously!?</h1>
      <p>$fullname, you are already logged in, silly! How could you forget your password?</p>";

      // We must finish the HTML page before we exit
      echo '
      </body>
      </html>';

      // We could just redirect to the main page instead
      //header("Location: webapp.php");
      exit ();

    } else { // Back-up plan just in case the impossible happens
      echo '<p class="error">Serious error.</p>';

      // We must finish the HTML page before we exit
      echo '
      </body>
      </html>';

      // We could just redirect to the main page instead
      //header("Location: webapp.php");
      exit ();
    }

// See if we are already logged in
} elseif ((isset($_SESSION['user_id'])) && (isset($_SESSION['full_name']))) {
  $user_id = $_SESSION['user_id'];
  $fullname = $_SESSION['full_name'];

  // Show a message
  echo "<h1>Logged In, Seriously!?</h1>
  <p>$fullname, you are already logged in, silly! How could you forget your password?</p>";

  // We must finish the HTML page before we exit
  echo '
  </body>
  </html>';

  // We could just redirect to the main page instead
  //header("Location: webapp.php");
  exit ();

}


// POSTed form?
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

  // Include our POST checks
  include ('./in.checks.php');
  if (($no_form_errors == true) && (!empty($email)) && (!empty($favnumber))) {
    $checks_out = true;

    // if SELECT: Query user info from the database if everything checks out
    if ($checks_out == true) {
      // SELECT ... WHERE [col1='$some' AND col2='$also'] // This is a way to double check
      $email_sqlesc = escape_sql($email);
      $favnumber_sqlesc = escape_sql($favnumber);
      $query = "SELECT id, fullname FROM users WHERE favnumber='$favnumber_sqlesc' AND email='$email_sqlesc'";
      $call = mysqli_query($database, $query);
      // Check to see that our SQL query returned exactly 1 row
      if (mysqli_num_rows($call) == 1) {
        // Assign the values
        $row = mysqli_fetch_array($call, MYSQLI_NUM);
          $user_id = "$row[0]";
          $fullname = "$row[1]";

        // Set the $_SESSION array
        $_SESSION['user_id'] = $user_id;
        $_SESSION['full_name'] = $fullname;

        // Show a message
        echo "<h1>Recovery success!</h1>";

        // Put a clickable link to Account Settings
        echo "<p>$fullname, you are logged in. Go to <a href=\"account.php\">Account Settings</a> to update your password!</p>";

        echo '<p class="blue">Teaching tip: Run this in the SQL terminal, it was the same query that just ran:<br><br><code>'.$query.';</code></p>';

        // We must finish the HTML page before we exit
        echo '
        </body>
        </html>';

        // We could just redirect to the main page instead
        exit ();


      } else { // Username fail
        echo '<p class="error">No match!</p>';
      } // End database check

    }

  // If errors in form
  } else {
      echo '<p class="error">Errors! Try again.</p>';
  }


// Not logged in, no login POST attempt
}

// Don't use empty variables, set dummy values
/// This also is more secure so the user must enter both correctly in the same form

$email = null;
$favnumber = null;

// Our actual recovery page

  echo '<h1>Forgot Your Password?</h1>';
  echo '
  <form action="forgot.php" method="post">';

  // Use null where errors would normally go
  echo 'Email: '.formInput('email', $email, $check_err).'<br><br>';
  echo 'Favorite number: '.formInput('favnumber', $favnumber, $check_err).'<br><br>';

  echo '
    <input type="submit" value="Recover account">
  </form>
  ';

?>

</body>
</html>
