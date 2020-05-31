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

echo $autoArray[0];
echo "<br>"; // Add a break so it is easy to read
echo $autoArray[1];
echo "<br>";
echo $autoArray[2];
echo "<br>";
echo $autoArray[3];


// Associative array
// Define an empty array so it can take associative keys
$assocArray = array();

// Associative values
$assocArray['key_one'] = "Donuts";
$assocArray['twokeys'] = "Coffee";
$assocArray['badBoys'] = "Whatcha gonna do";

echo "<br>"; // Add a break so it is easy to read

echo $assocArray['key_one'];

echo "<br>"; // Add a break so it is easy to read

// Not allowed in "quotes", do like this
$someVariable = $assocArray['key_one'];
echo "$someVariable";


// Get all values of any array
echo "<br>print_r \$assocArray:<br>";
print_r($assocArray);
echo "<br>print_r \$autoArray:<br>";
print_r($autoArray);

?>

</body>
</html>
