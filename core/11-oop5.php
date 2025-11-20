<?php

// Class
class Topic {

  // Methods
  function course() {
    return "Codia";
  }

  function slogan() {
    return "Anyone can learn!";
  }

  // Broken lines:
  //echo "Anyone can learn!";
  //slogan();
  //$this->slogan();
  //echo $this->slogan();

  function __construct() {
    echo $this->slogan();
    echo "<br> That's Codia!";
  }

}

// Object
$topicObject = new Topic;

?>
