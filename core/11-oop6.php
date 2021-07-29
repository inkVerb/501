<?php

// Class
class Topic {

  function course() {
    return "VIP Linux (course)";
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

?>
