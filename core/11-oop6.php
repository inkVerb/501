<?php

// Class
class Topic {

  function course() {
    return "Codia (course)<br>";
  }

  function slogan() {
    return "Anyone can learn! (slogan)<br>";
  }

  function __construct() {
    echo "<br>..<br>I construct at the start!<br>..<br>";
    echo $this->property;
    echo $this->course();
  }

  function __destruct() {
    echo "<br>..<br>I destruct at the end!<br>..<br>";
    echo $this->property;
    echo $this->slogan();
  }

  // Property (class/object variable) declared after magic methods __construct & __destruct, which uses this property
  var $property = "I am a property.<br>";

}

// Normal variable
$normvar = "I am a variable.<br>";

// Object
$topicObject = new Topic;

// Note
echo "<br>...<br>Object instantiated.<br>...<br><br>";

// echo variables
echo $normvar;  // "I am a variable."
echo $topicObject->property; // "I am a property."

// __destruct happens here

?>
