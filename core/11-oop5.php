<?php

// Class
class Topic {

  // Methods
  function course() {
    return "VIP Linux";
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
    echo "<br> That's VIP Linux!";
  }

}

// Object
$topicObject = new Topic;

?>
