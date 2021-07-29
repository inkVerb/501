<?php

// Define the "Topic" class
class Topic {

  // Declare class properties
  var $course;
  var $website;
  var $slogan;

  // Define methods, setting the class properties we just declared
  function setCourse($arg) {
    $this->course = $arg;
  }

  function setWebsite($arg) {
    $this->website = $arg;
  }

  function setSlogan($arg) {
    $this->slogan = $arg;
  }

  // Define methods to return the class properties
  function retCourse() {
    return $this->course;
  }

  function retWebsite() {
    return $this->website;
  }

  function retSlogan() {
    return $this->slogan;
  }

}

// Instantiate the "Topic" objects
$Shell = new Topic;
$BASH = new Topic;
$PHP = new Topic;

// Set values for "Topic" object via methods
$Shell->setCourse("Shell 101");
$Shell->setWebsite("verb.vip/101");
$Shell->setSlogan("Learn the Linux Shell");

$BASH->setCourse("BASH 401");
$BASH->setWebsite("verb.vip/401");
$BASH->setSlogan("Learn the Linux BASH");

$PHP->setCourse("PHP 501");
$PHP->setWebsite("verb.vip/501");
$PHP->setSlogan("Learn the PHP Linux stack");

// Get values from "Topic" object via methods
echo "<h1>Shell</h1>";
echo $Shell->retCourse();
echo "<br>..<br>";
echo $Shell->retWebsite();
echo "<br>..<br>";
echo $Shell->retSlogan();

echo "<h1>BASH</h1>";
echo $BASH->retCourse();
echo "<br>..<br>";
echo $BASH->retWebsite();
echo "<br>..<br>";
echo $BASH->retSlogan();

echo "<h1>PHP</h1>";
echo $PHP->retCourse();
echo "<br>..<br>";
echo $PHP->retWebsite();
echo "<br>..<br>";
echo $PHP->retSlogan();

?>
