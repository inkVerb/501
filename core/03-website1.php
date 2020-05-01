<!DOCTYPE html>
<html>
<head>
  <!-- CSS file included as <link> -->
  <link href="style.css" rel="stylesheet" type="text/css" />
</head>
<body>

<?php

// We will remove functions, POST, and the form in the next step... //

// Include our functions
include ('./in.functions.php');

// POSTed form?
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

  // Include our POST checks
  include ('./in.checks.php');

} // Finish POST if

// Our actual website

echo '
<form action="website.php" method="post">';

echo formInput('website', $website, $check_err);
echo formInput('email', $email, $check_err);
echo formInput('number', $number, $check_err);
echo formInput('fullname', $fullname, $check_err);
echo formInput('username', $username, $check_err);
echo formInput('password', $password, $check_err);
echo formInput('password2', $password2, $check_err);

echo '
  <input type="submit" value="Submit Button">
</form>
';

// ...will be removed //

?>

</body>
</html>
