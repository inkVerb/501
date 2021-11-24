<?php

class classA {

  public function publicA() {
    return "Public A";
  }

  static function staticA() {
    return "Static A";
  }

}

class classB extends classA {

  public function publicB() {
    return "Public B";
  }

  static function staticB() {
    return "Static B";
  }

}

// Before instantiation
echo "<h1>Static methods called by uninstantiated class</h1>";
echo '<code>classA::staticA();</code> ' . classA::staticA();
echo "<br>..<br>";
echo '<code>classB::staticB();</code> ' . classB::staticB();
echo "<br>..<br>";

// Instantiate
$ObjectA = new classA;
$ObjectB = new classB;

// Display
echo "<h1>Static methods called by instantiated class</h1>";
echo '<code>classA::staticA();</code> ' . classA::staticA();
echo "<br>..<br>";
echo '<code>classB::staticB();</code> ' . classB::staticB();
echo "<br>..<br>";
echo "<h2>Public methods called as if static would break script</h2>";
//echo '<code>classA::publicA();</code> ' . classA::publicA();
echo "<br>..<br>";
//echo '<code>classB::publicB();</code> ' . classB::publicB();
echo "<br>..<br>";

echo "<h1>Class A</h1>";
echo '<code>$ObjectA->publicA();</code> ' . $ObjectA->publicA();
echo "<br>..<br>";
echo '<code>$ObjectA->staticA();</code> ' . $ObjectA->staticA();
echo "<br>..<br>";
echo "<h1>Class B</h1>";
echo '<code>$ObjectB->publicB();</code> ' . $ObjectB->publicB();
echo "<br>..<br>";
echo '<code>$ObjectB->staticB();</code> ' . $ObjectB->staticB();
echo "<br>..<br>";

?>
