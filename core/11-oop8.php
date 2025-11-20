<?php

// Class
class Topic {

  function course() {
    return "Codia (course)";
  }

  function slogan() {
    return "Anyone can learn! (slogan)";
  }

  function __construct() {
    echo "<br>..<br>I construct at the start!<br>..<br>";
    echo $this->course();
  }

  function __destruct() {
    echo "<br>..<br>I destruct at the end!<br>..<br>";
    echo $this->slogan();
  }

}

// Object
$topicObject = new Topic;

// Use the object
echo "<h1>After instantiation</h1>";
echo $topicObject->slogan();
echo "<br>..<br>";
echo $topicObject->course();
echo "<br>..<br>";
unset($topicObject);
echo "<h1>After <code>unset()</code></h1>";
echo $topicObject->slogan();
echo "<br>..<br>";
echo $topicObject->course();
echo "<br>..<br>";

?>
