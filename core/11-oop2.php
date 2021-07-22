<?php

// Define the "Topic" class
class Topic{

  // Declare class variables
  var $course;
  var $website;
  var $slogan;

  // Create functions, setting the class variables we just declared
  function setCourse($arg) {
    $this->course = $arg;
  }

  function setWebsite($arg) {
    $this->website = $arg;
  }

  function setSlogan($arg) {
    $this->slogan = $arg;
  }

  // Create functions to return the class variables
  function getCourse() {
    return $this->course;
  }

  function getWebsite() {
    return $this->website;
  }

  function getSlogan() {
    return $this->slogan;
  }

}

// Create the "Topic" objects
$Shell = new Topic();
$BASH = new Topic();
$PHP = new Topic();

// Set values from "Topic" object functions
$Shell->setCourse("Shell 101");
$Shell->setWebsite("verb.vip/101");
$Shell->setSlogan("Learn the Linux Shell");

$BASH->setCourse("BASH 401");
$BASH->setWebsite("verb.vip/401");
$BASH->setSlogan("Learn the Linux BASH");

$PHP->setCourse("PHP 501");
$PHP->setWebsite("verb.vip/501");
$PHP->setSlogan("Learn the PHP Linux stack");

// Get values from "Topic" object functions
echo "<h1>Shell</h1>";
echo $Shell->getCourse();
echo "<br>..<br>";
echo $Shell->getWebsite();
echo "<br>..<br>";
echo $Shell->getSlogan();

echo "<h1>BASH</h1>";
echo $BASH->getCourse();
echo "<br>..<br>";
echo $BASH->getWebsite();
echo "<br>..<br>";
echo $BASH->getSlogan();

echo "<h1>PHP</h1>";
echo $PHP->getCourse();
echo "<br>..<br>";
echo $PHP->getWebsite();
echo "<br>..<br>";
echo $PHP->getSlogan();

?>
