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

echo "foreach autoArray as \$item:<br>";

foreach ($autoArray as $item) {

  echo $item.'<br>';

}

// Both key and value for each item
echo "<br>foreach autoArray as \$key => \$value:<br>";

foreach ($autoArray as $key => $value) {

  echo 'autoArray['.$key.']: '.$value.'<br>';

}


// Associative array
$assocArray = array();
$assocArray['key_one'] = "Donuts";
$assocArray['twokeys'] = "Coffee";
$assocArray['badBoys'] = "Whatcha gonna do";

echo "<br>foreach assocArray as \$key => \$value:<br>";

foreach ($assocArray as $key => $value) {

  echo 'assocArray['.$key.']: '.$value.'<br>';

}

?>

</body>
</html>
