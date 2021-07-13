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

echo "<b>Auto-indexed array:</b><br>";
echo $autoArray[0];
echo "<br>"; // Add a break so it is easy to read
echo $autoArray[1];
echo "<br>";
echo $autoArray[2];
echo "<br>";
echo $autoArray[3];
echo "<br><br>";

// Assign array values to variables via one list
list($a_one, $a_two, $a_three, $a_four) = $autoArray;
echo "<b>Same, using <code>list()</code>:</b><br>";
echo $a_one;
echo "<br>";
echo $a_two;
echo "<br>";
echo $a_three;
echo "<br>";
echo $a_four;
echo "<br><br>";

// Associative array
// Define an empty array so it can take associative keys
$assocArray = array();

// Associative values
$assocArray['key_one'] = "Donuts";
$assocArray['twokeys'] = "Coffee";
$assocArray['badBoys'] = "Whatcha gonna do";

echo "<b>Associative array:</b><br>";

echo $assocArray['key_one'];

echo "<br>"; // Add break so it is easy to read

// Not allowed in "quotes", do like this
$someVariable = $assocArray['key_one'];
echo $someVariable;

echo "<br><br>"; // Add breaks so it is easy to read

echo "<b>Show array, using <code>print_r()</code>:</b><br>";

// Get all values of any array
echo "<code>print_r \$assocArray</code>:<br>";
print_r($assocArray);

echo "<br><br>"; // Add breaks so it is easy to read

echo "<code>print_r \$autoArray</code>:<br>";
print_r($autoArray);

echo "<br><br>"; // Add breaks so it is easy to read

echo "<b>Search array, using <code>in_array()</code>:</b><br>";

// Match something in an array
if (in_array('Donuts', $assocArray)) {
  echo 'Donuts found!';
} else {
  echo 'Donuts not found!';
}

echo "<br>"; // Add break so it is easy to read

if (in_array('Latte', $assocArray)) {
  echo 'Latte found!';
} else {
  echo 'Latte not found!';
}

?>

</body>
</html>
