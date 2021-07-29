<?php

// Define the "Topic" class
class Loopie {

  // Declare properties
  var $property1 = "Prop one";
  var $property2 = "Prop two";
  var $property3 = "Prop three";

  // Properties with visibility
  public     $publicProp = "Public Prop"; // Default
  protected  $protectedProp = "Protected Prop";
  private    $privateProp = "Private Prop";

  // Declare a method we won't see in the loop
  function cantSeeMe() {
    echo "nothere";
  }

}

// Instantiate the "Topic" objects
$loop_object = new Loopie;

// Get values from "Topic" object via methods
echo "<h1>Loop</h1>";

// Loop
foreach ( $loop_object as $key=>$value ) {
    echo "$key = $value<br>";
}

?>
