<?php

// Define the "Topic" class
class Topic{

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

// Create the "Topic" object
$topicObject = new Topic();

// Use the "Topic" object and its functions
echo $booksObject->course();
echo "<br>..<br>";
echo $booksObject->website();
echo "<br>..<br>";
echo $booksObject->slogan();

?>
