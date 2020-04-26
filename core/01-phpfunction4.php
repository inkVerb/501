<!DOCTYPE html>
<html>
<head>
  <!-- CSS to make our class="error" stuff red -->
  <style>
    form input.error {
    	border: 2px solid #cc3333;
    }
    .error {
    	color: #cc3333;
    	font-weight: thick;
    }
  </style>
</head>
<body>

<?php

// Define our error array
$check_err = array();

// Define the check function
function checkPostWebsite($formWebsite) {

  // We need our error array inside and outside this function
  global $check_err;

  // Run our Validation and Sanitizing checks
  $result = (filter_var($formWebsite,FILTER_VALIDATE_URL))
  ? preg_replace("/[^a-zA-Z0-9-_:\/.]/","", $formWebsite) : '';

  // Add an entry to $check_err array if there is an error
  if ($result == '') {
    $check_err['website'] = 'Not a website!';
  }

  return $result;
} // Finish function

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

  // Just use the function to get the same value
  $website = checkPostWebsite($_POST['website']);

  // Show our results if $check_err is empty
  if (empty($check_err)) {
    echo "Website: <b>$website</b><br><br>";
  }
} // Finish POST if

// Define the input function
function formInput($name, $value, $errors) {

  echo 'Website: <input type="text" name="website" placeholder="http://..." value="'.$value.'" ';

  // Add class="error" to the <input>, then an error message if $check_err['website'] has something
  if (array_key_exists($name, $errors)) {
    echo ' class="error"> <span class="error">'.$errors[$name].'</span>';
  } else {
    echo '>';
  }

  echo '<br><br>';
} // Finish function

echo '
<form action="phppost.php" method="post">';

// Use the function echo "Website: <input..."
formInput('website', $website, $check_err);

echo '
  <input type="submit" value="Submit Button">
</form>
';

?>

</body>
</html>
