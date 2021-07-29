<?php

// Class
class Topic {

  // Methods
  function course() {
    return "VIP Linux";
  }

  function website() {
    return "verb.vip";
  }

  function slogan() {
    return "Anyone can learn!";
  }

}

// Object
$topicObject = new Topic;
exit();
// Use
echo $topicObject->course();
echo "<br>..<br>";
echo $topicObject->website();
echo "<br>..<br>";
echo $topicObject->slogan();

?>
