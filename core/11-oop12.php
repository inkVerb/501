<?php

class classA {

  public $publicA = "Public A";
  static $staticA = "Static A";

  function APublic() {
    return $this->publicA . ' & ' . $this->staticA; // Incorrect
    //return $this->publicA . ' & ' . classA::$staticA; // Correct
  }

  function AClassStaticA() {
    return classA::$staticA;
  }

  function AClassStaticB() {
    return classB::$staticB;
  }

  function AClassPublicA() {
    return classA::$publicA;
  }

  function AClassPublicB() {
    return classB::$publicB;
  }

}

class classB extends classA {

  public $publicB = "Public B";
  static $staticB = "Static B";

  function BPublic() {
    //return $this->publicB . ' & ' . $this->staticB; // Incorrect
    return $this->publicB . ' & ' . classB::$staticB; // Correct
  }

  function BClassStaticB() {
    return classB::$staticB;
  }

  function BClassStaticA() {
    return classA::$staticA;
  }

  function BClassPublicB() {
    return classB::$publicB;
  }

  function BClassPublicA() {
    return classA::$publicA;
  }

}

// Instantiate
$ObjectA = new classA;
$ObjectB = new classB;

// Display
echo "<h1>Properties called by class</h1>";
echo '<code>classA::$staticA</code> ' . classA::$staticA;
echo "<br>..<br>";
echo '<code>classB::$staticB</code> ' . classB::$staticB;
echo "<br>..<br>";
//echo '<code>classA::$publicA</code> ' . classA::$publicA;
echo "<br>..<br>";
//echo '<code>classB::$publicB</code> ' . classB::$publicB;
echo "<br>..<br>";

echo "<h1>Class A object methods</h1>";
echo '<code>$ObjectA->APublic();</code> ' . $ObjectA->APublic();
echo "<br>..<br>";
echo '<code>$ObjectA->AClassStaticA();</code> ' . $ObjectA->AClassStaticA();
echo "<br>..<br>";
echo '<code>$ObjectA->AClassStaticB();</code> ' . $ObjectA->AClassStaticB();
echo "<br>..<br>";
//echo '<code>$ObjectA->AClassPublicA();</code> ' . $ObjectA->AClassPublicA();
echo "<br>..<br>";
//echo '<code>$ObjectA->AClassPublicB();</code> ' . $ObjectA->AClassPublicB();
echo "<br>..<br>";
echo "<h1>Class B object methods</h1>";
echo '<code>$ObjectB->APublic();</code> ' . $ObjectB->BPublic();
echo "<br>..<br>";
echo '<code>$ObjectB->AClassStaticA();</code> ' . $ObjectB->BClassStaticB();
echo "<br>..<br>";
echo '<code>$ObjectB->AClassStaticB();</code> ' . $ObjectB->BClassStaticA();
echo "<br>..<br>";
//echo '<code>$ObjectB->AClassPublicA();</code> ' . $ObjectB->BClassPublicB();
echo "<br>..<br>";
//echo '<code>$ObjectB->AClassPublicB();</code> ' . $ObjectB->BClassPublicA();
echo "<br>..<br>";

?>
