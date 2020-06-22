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
  $number = ( (isset($_POST['number'])) && ($_POST['number'] != '') ) ? $_POST['number'] : '';
  $number = preg_replace('/[0-9]/','#',$number);

  // $dogfish
  $dogfish = ( (isset($_POST['dogfish'])) && ($_POST['dogfish'] != '') ) ? $_POST['dogfish'] : '';
  $dogfish = preg_replace('/([a-zA-Z0-9]+)_dogfish_([a-zA-Z0-9]+)/','$1_GoldFish_$2',$dogfish);

  // $dash
  $dash = ( (isset($_POST['dash'])) && ($_POST['dash'] != '') ) ? $_POST['dash'] : '';
  $dash = preg_replace('/([A-Z].[a-z]+)-([A-Z].[a-z]+)/','$1–$2',$dash);
  $dash = preg_replace('/([0-9]+)-([0-9]+)/','$1–$2',$dash);
  $dash = str_replace(' -- ',' – ',$dash);
  $dash = str_replace('---','—',$dash);
  $dash = str_replace('--','—',$dash);

}


echo '
<form action="phpreplace.php" method="post">
  Letters: <input type="text" size="32" name="alpha" value="'.$alpha.'"> (uppercase and lowercase)<br><br>
  Numbers: <input type="text" size="32" name="number" value="'.$number.'"> (see what happens to digits, but not to letters)<br><br>
  Dogfish: <input type="text" size="32" name="dogfish" placeholder="1b3D_dogfish_A2c4" value="'.$dogfish.'"> (put <code>_dogfish_</code> in the middle)<br><br>
  Dashes: <input type="text" size="32" name="dash" value="'.$dash.'"> (try: <code>--- -- abc--def ghi -- klm 89-72 Mon-Fri world-wide</code> )<br><br>
  <input type="submit" value="Replace stuff">
</form>
';

?>

</body>
</html>
