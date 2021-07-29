<?php

// Define the "Topic" class
class immaClass {

  // Declare class properties
  var $property1;
  var $property2;

  // Method that sets class properties
  function MethodSet($argument1, $argument2) {

    $this->property1 = $argument1;
    $this->property2 = $argument2;

  }


  // Method that returns class properties
  function MethodReturn($argument) {

    if ($argument == 1) {
      return $this->property1;
    } elseif ($argument == 2) {
      return $this->property2;
    } else {
      return "error";
    }

  }

}

// Instantiate two objects build on that class
$ObjectFirst = new immaClass;
$ObjectAlso = new immaClass;

// Set values with the set method
$ObjectFirst->MethodSet("One arg", "Two arg");
$ObjectAlso->MethodSet("One also", "Two also");

// Return and echo the values from the objects
echo "<h1>Shell</h1>";
echo '$ObjectFirst->MethodReturn(1); ' . $ObjectFirst->MethodReturn(1);
echo "<br>..<br>";
echo '$ObjectFirst->MethodReturn(2); ' . $ObjectFirst->MethodReturn(2);
echo "<br>..<br>";
echo '$ObjectFirst->MethodReturn(); ' . $ObjectFirst->MethodReturn();
echo "<h1>Shell</h1>";
echo '$ObjectAlso->MethodReturn(1); ' . $ObjectAlso->MethodReturn(1);
echo "<br>..<br>";
echo '$ObjectAlso->MethodReturn(2); ' . $ObjectAlso->MethodReturn(2);
echo "<br>..<br>";
echo '$ObjectAlso->MethodReturn(); ' . $ObjectAlso->MethodReturn();

?>
