<?php

// Define the "Topic" class
class classA {

  // Declare class property
  var $propertyA;

  // Method that sets class properties
  function MethodSetA($argument) {
    $this->propertyA = $argument;
  }

  // Method that returns class properties
  function MethodReturnA() {
      return $this->propertyA;
  }

}

class classB extends classA {

  // Declare class property
  var $propertyB;

  // Method that sets class properties
  function MethodSetB($argument) {
    $this->propertyB = $argument;
  }

  // Method that returns class properties
  function MethodReturnB() {
      return $this->propertyB;
  }

}

// Instantiate two objects build on those classes
$ObjectA = new classA;
$ObjectB = new classB;

// Set values with the set method
$ObjectA->MethodSetA("A set A");
//$ObjectA->MethodSetB("A set B");
$ObjectB->MethodSetA("B set A");
$ObjectB->MethodSetB("B set B");

// Display
echo "<h1>classA</h1>";
echo '<code>$ObjectA->MethodReturnA();</code> ' . $ObjectA->MethodReturnA();
echo "<br>..<br>";
//echo '<code>$ObjectA->MethodReturnB(2);</code> ' . $ObjectA->MethodReturnB();
echo "<br>..<br>";

echo "<h1>classB</h1>";
echo '<code>$ObjectB->MethodReturnA();</code> ' . $ObjectB->MethodReturnA();
echo "<br>..<br>";
echo '<code>$ObjectB->MethodReturnB();</code> ' . $ObjectB->MethodReturnB();
echo "<br>..<br>";

?>
