<!DOCTYPE html>
<html>
<body>

<?php

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

  $website = ( (isset($_POST['website'])) &&
  (filter_var($_POST['website'],FILTER_VALIDATE_URL)) )
  ? preg_replace("/[^a-zA-Z0-9-_:\/.]/","", $_POST['website']) : 'Not a website!';

  echo "Website: <b>$website</b><br><br>";

} // Finish POST if

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
