<?php

/// Create the parent
class ParentClass {

  var $parentProperty;

}

// Create the child
class ChildClass extends ParentClass {

  var $childProperty;

}

// Instantiate
$ObjectParent = new ParentClass;
$ObjectChild = new ChildClass;

// Return and echo the values from the objects
echo "<h1>Parent Class</h1>";
echo '<code>$ObjectParent->parentProperty;</code> ' . $ObjectParent->parentProperty;
echo "<br>..<br>";
//echo '<code>$ObjectParent->childProperty;</code> ' . $ObjectParent->childProperty;
echo "<br>..<br>";

echo "<h1>Child Class</h1>";
echo '<code>$ObjectChild->parentProperty;</code> ' . $ObjectChild->parentProperty;
echo "<br>..<br>";
echo '<code>$ObjectChild->childProperty;</code> ' . $ObjectChild->childProperty;
echo "<br>..<br>";

?>
