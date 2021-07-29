<?php

class classA {

  public $publicA = "Public A";
  protected $protectedA = "Protected A";
  private $privateA = "Private A";

  function AReturn() {
    return $this->publicA . ' & ' . $this->protectedA . ' & ' . $this->privateA;
  }

  function AReturnB() {
    return $this->publicB;
  }

  function AFail() {
    return $this->protectedB . ' & ' . $this->privateB;
  }

}

class classB extends classA {

  public $publicB = "Public B";
  protected $protectedB = "Protected B";
  private $privateB = "Private B";

  function BReturn() {
    return $this->publicB . ' & ' . $this->protectedB . ' & ' . $this->privateB;
  }

  function BReturnA() {
    return $this->publicA . ' & ' . $this->protectedA;
  }

  function BFail() {
    return $this->privateA;
  }

}

// Instantiate
$ObjectA = new classA;
$ObjectB = new classB;

// Display
echo "<h1>Class A</h1>";
echo "<h2>Methods call properties</h2>";
echo '<code>$ObjectA->AReturn();</code> ' . $ObjectA->AReturn();
echo "<br>..<br>";
//echo '<code>$ObjectA->AReturnB();</code> ' . $ObjectA->AReturnB();
echo "<br>..<br>";
//echo '<code>$ObjectA->AFail();</code> ' . $ObjectA->AFail();
echo "<br>..<br>";
echo "<h2>Properties called directly</h2>";
echo '<code>$ObjectA->publicA;</code> ' . $ObjectA->publicA;
echo "<br>..<br>";
//echo '<code>$ObjectA->protectedA;</code> ' . $ObjectA->protectedA;
echo "<br>..<br>";
//echo '<code>$ObjectA->privateA;</code> ' . $ObjectA->privateA;
echo "<br>..<br>";

echo "<h1>Class B</h1>";
echo "<h2>Methods call properties</h2>";
echo '<code>$ObjectB->BReturn();</code> ' . $ObjectB->BReturn();
echo "<br>..<br>";
echo '<code>$ObjectB->BReturnA();</code> ' . $ObjectB->BReturnA();
echo "<br>..<br>";
//echo '<code>$ObjectA->BFail();</code> ' . $ObjectA->BFail();
echo "<br>..<br>";
echo "<h2>Properties called directly</h2>";
echo '<code>$ObjectB->publicB;</code> ' . $ObjectB->publicB;
echo "<br>..<br>";
//echo '<code>$ObjectB->protectedB;</code> ' . $ObjectB->protectedB;
echo "<br>..<br>";
//echo '<code>$ObjectA->privateB;</code> ' . $ObjectA->privateB;

?>
