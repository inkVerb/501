<?php

// Class
class Topic {

  function __destruct() {
    echo "<br>..<br>I destruct at the end!<br>..<br>";
    echo $this->slogan();
  }

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

}

// Object
$topicObject = new Topic;

?>
