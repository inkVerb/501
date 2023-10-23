<!DOCTYPE html>
<html>
<body>

<?php

// Auto-indexed array
$autoArray = array(
  'value_1',
  "second value",
  "triplets",
  'fourthly'
);

echo "foreach autoArray:<br>";

foreach ($autoArray as $item) {

  echo $item.'<br>';

}


// Associative array
$assocArray = array();
$assocArray['key_one'] = "Donuts";
$assocArray['twokeys'] = "Coffee";
$assocArray['badBoys'] = "Whatcha gonna do";

echo "<br>foreach assocArray:<br>";

foreach ($assocArray as $item) {

  echo $item.'<br>';

}

echo "<br>foreach assocArray with keys:<br>";

foreach ($assocArray as $key => $item) {

  echo 'loopArray['.$key.']: '.$item.'<br>';

}

?>

</body>
</html>
