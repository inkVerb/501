<?php

// Define the "Topic" class
class Loopie {

  // Declare properties
  public     $publicProp = "Public Prop";
  protected  $protectedProp = "Protected Prop";
  private    $privateProp = "Private Prop";

  // Declare a method we won't see in the loop
  function cantSeeMe() {
    echo "nothere";
  }

}

// Instantiate the "Topic" objects
$loop_object = new ReflectionClass('Loopie');
$loop_obj_props = $loop_object->getDefaultProperties();

// Get values from "Topic" object via methods
echo "<h1>Loop</h1>";

// Loop
foreach ( $loop_obj_props as $key=>$value ) {
    echo "$key = $value<br>";
}

?>
