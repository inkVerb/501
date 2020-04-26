<!DOCTYPE html>
<html>
<body>

<?php

// Define the check function
function checkPostWebsite($formWebsite) {

  // Run our Validation and Sanitizing checks
  $result = (filter_var($formWebsite,FILTER_VALIDATE_URL))
  ? preg_replace("/[^a-zA-Z0-9-_:\/.]/","", $formWebsite) : 'Not a website!';

  return $result;
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {

  // Just use the function to get the same value
  $website = checkPostWebsite($_POST['website']);

  echo "Website: <b>$website</b><br><br>";
}


// Define the input function
function formInputWebsite($websiteURL) {
  echo 'Website: <input type="text" name="website" placeholder="http://..." value="'.$websiteURL.'"><br><br>';
}

echo '
<form action="phppost.php" method="post">';

// Use the function echo "Website: <input..."
formInputWebsite($website);

echo '
  <input type="submit" value="Submit Button">
</form>
';

?>

</body>
</html>
