<!DOCTYPE html>
<html>
<body>

<?php

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

  // $alpha
  $alpha = ( (isset($_POST['alpha'])) && ($_POST['alpha'] != '') ) ? $_POST['alpha'] : '';
  $alpha = preg_replace("/[a-z]/",'z',$alpha);
  $alpha = preg_replace("/[A-Z]/",'Z',$alpha);

  // $number
  $number = ( (isset($_POST['number'])) && ($_POST['number'] != '')) ? $_POST['number'] : '';
  $number = preg_replace('/[0-9]/','#',$number);

  // $dogfish
  $dogfish = ( (isset($_POST['dogfish'])) && ($_POST['dogfish'] != '')) ? $_POST['dogfish'] : '';
  $dogfish = preg_replace('/([a-zA-Z0-9])+_dogfish_+([a-zA-Z0-9])/','$1_GoldFish_$2',$dogfish);

  // $dash
  $dash = ( (isset($_POST['dash'])) && ($_POST['dash'] != '') ) ? $_POST['dash'] : '';
  $dash = preg_replace('/([0-9]$)+-+([0-9])/','$1–$2',$dash);
  $dash = str_replace(' -- ',' – ',$dash);
  $dash = str_replace('---','—',$dash);
  $dash = str_replace('--','—',$dash);

}

echo '
<form action="phpreplace.php" method="post">
  Letters: <input type="text" name="alpha" value="'.$alpha.'"><br><br>
  Numbers: <input type="text" name="number" value="'.$number.'"><br><br>
  Dogfish: <input type="text" name="dogfish" placeholder="1b3D_dogfish_A2c4" value="'.$dogfish.'"><br><br>
  Dashes: <input type="text" name="dash" value="'.$dash.'"><br><br>
  <input type="submit" value="Replace stuff">
</form>
';

?>

</body>
</html>
