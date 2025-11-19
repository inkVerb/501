<?php

class staticDemo { // Instantiation doesn't matter

    static function say_something($string) { // Returns a string
    return $string;
  }

  static $answer;
  static function set_answer($string) { // Sets the $answer value
    self::$answer = $string;
  }

}

class nonStaticDemo { // Instantiation required

  public function say_something($string) { // Returns a string
    return $string;
  }

  public $answer;
  public function set_answer($string) { // Sets the $answer value
    $this->$answer = $string;
  }

}

// Instantiate
$NonStaticDemo = new nonStaticDemo;

// Display
echo "<h1>Static Demo: Not Instantiated</h1>";
echo '<code>staticDemo::say_something("Hello there, some world!");</code> ' . staticDemo::say_something("Hello there, some world!");
echo "<br>..<br>";
echo '<code>staticDemo::set_answer("Hello there, valued world!");</code>';
staticDemo::set_answer("Hello there, valued world!");
echo "<br><br>";
echo '<code>staticDemo::$answer;</code> ' . staticDemo::$answer;

echo "<h1>Non-Static Demo: Instantiated</h1>";
echo '<code>$NonStaticDemo = new nonStaticDemo;</code>';
echo "<br>";
echo '<code>$NonStaticDemo->say_something("Hello there, some world!");</code> ' . $NonStaticDemo->say_something("Hello there, some world!");
echo "<br>..<br>";
echo '<code>$NonStaticDemo->set_answer("Hello there, valued world!");</code>';
$NonStaticDemo->set_answer("Hello there, valued world!");
echo "<br><br>";
echo '<code>$NonStaticDemo->$answer;</code> ' . $NonStaticDemo->$answer;

?>
