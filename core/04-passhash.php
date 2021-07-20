<!DOCTYPE html>
<html>
<body>

<?php

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

  if (isset($_POST['pass'])) {
    $pass = $_POST['pass'];
    echo "Password was: $pass<br>";
  }

  // Hash the same password many times
  $password_hashed_1 = password_hash($pass, PASSWORD_BCRYPT);
  $password_hashed_2 = password_hash($pass, PASSWORD_BCRYPT);
  $password_hashed_3 = password_hash($pass, PASSWORD_BCRYPT);
  $password_hashed_4 = password_hash($pass, PASSWORD_BCRYPT);
  $password_hashed_5 = password_hash($pass, PASSWORD_BCRYPT);

  // Verify each different hash of the same password
  $pass_verify_1 = password_verify($pass, $password_hashed_1);
  $pass_verify_2 = password_verify($pass, $password_hashed_2);
  $pass_verify_3 = password_verify($pass, $password_hashed_3);
  $pass_verify_4 = password_verify($pass, $password_hashed_4);
  $pass_verify_5 = password_verify($pass, $password_hashed_5);
  $pass_verify_6 = password_verify(wrong, $password_hashed_5);

  // Display the different password hashes
  echo "<h1>Password hash results</h1>";
  echo "<pre>Hash 1: $password_hashed_1</pre>";
  echo "<pre>Hash 2: $password_hashed_2</pre>";
  echo "<pre>Hash 3: $password_hashed_3</pre>";
  echo "<pre>Hash 4: $password_hashed_4</pre>";
  echo "<pre>Hash 5: $password_hashed_5</pre>";
  echo "<pre>Hash 6: wrong</pre>";


  // Display whether the different hashed check out
  echo "<pre>Checks-out 1: $pass_verify_1 PHP code: password_verify($pass, $password_hashed_1)</pre>";
  echo "<pre>Checks-out 2: $pass_verify_2 PHP code: password_verify($pass, $password_hashed_2)</pre>";
  echo "<pre>Checks-out 3: $pass_verify_3 PHP code: password_verify($pass, $password_hashed_3)</pre>";
  echo "<pre>Checks-out 4: $pass_verify_4 PHP code: password_verify($pass, $password_hashed_4)</pre>";
  echo "<pre>Checks-out 5: $pass_verify_5 PHP code: password_verify($pass, $password_hashed_5)</pre>";
  echo "<pre>Checks-out 6: $pass_verify_6 PHP code:  password_verify(wrong, $password_hashed_5)</pre>";

  echo "<hr><br><br>";

}

echo '
<form action="passhash.php" method="post">
  Password: <input type="text" name="pass" placeholder="Enter some password" value="'.$pass.'"><br><br>
  <input type="submit" value="Submit Button">
</form>
';

?>

</body>
</html>
