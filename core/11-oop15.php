<?php

class classA {

  public const PUBLIC_A = "Public A";
  protected const PROTECTED_A = "Protected A";
  private const PRIVATE_A = "Private A";

  function AReturn() {
    return self::PUBLIC_A . ' & ' . self::PROTECTED_A . ' & ' . self::PRIVATE_A;
  }

  function AReturnB() {
    return self::PUBLIC_B;
  }

  function AFail() {
    return self::PROTECTED_B . ' & ' . self::PRIVATE_B;
  }

}

class classB extends classA {

  public const PUBLIC_B = "Public B";
  protected const PROTECTED_B = "Protected B";
  private const PRIVATE_B = "Private B";

  function BReturn() {
    return self::PUBLIC_B . ' & ' . self::PROTECTED_B . ' & ' . self::PRIVATE_B;
  }

  function BReturnA() {
    return self::PUBLIC_A . ' & ' . self::PROTECTED_A;
  }

  function BFail() {
    return self::PRIVATE_A;
  }

}

// Instantiate
$ObjectA = new classA;
$ObjectB = new classB;

// Display
echo "<h1>Class A</h1>";
echo "<h2>Methods call constants</h2>";
echo '<code>$ObjectA->AReturn();</code> ' . $ObjectA->AReturn();
echo "<br>..<br>";
//echo '<code>$ObjectA->AReturnB();</code> ' . $ObjectA->AReturnB();
echo "<br>..<br>";
//echo '<code>$ObjectA->AFail();</code> ' . $ObjectA->AFail();
echo "<br>..<br>";
echo "<h2>Constants called directly</h2>";
echo "<h2>Constants called by class</h2>";
echo '<code>$ObjectA->PUBLIC_A;</code> (empty property) ' . $ObjectA->PUBLIC_A;
echo "<br>..<br>";
echo '<code>classA::PUBLIC_A;</code> ' . classA::PUBLIC_A;
echo "<br>..<br>";
//echo '<code>classA::PROTECTED_A;</code> ' . classA::PROTECTED_A;
echo "<br>..<br>";
//echo '<code>classA::PRIVATE_A;</code> ' . classA::PRIVATE_A;
echo "<br>..<br>";

echo "<h1>Class B</h1>";
echo "<h2>Methods call constants</h2>";
echo '<code>$ObjectB->BReturn();</code> ' . $ObjectB->BReturn();
echo "<br>..<br>";
echo '<code>$ObjectB->BReturnA();</code> ' . $ObjectB->BReturnA();
echo "<br>..<br>";
//echo '<code>$ObjectA->BFail();</code> ' . $ObjectA->BFail();
echo "<br>..<br>";
echo "<h2>Constants called by class</h2>";
echo '<code>$ObjectB->PUBLIC_B;</code> (empty property) ' . $ObjectB->PUBLIC_B;
echo "<br>..<br>";
echo '<code>classB::PUBLIC_B;</code> ' . classB::PUBLIC_B;
echo "<br>..<br>";
//echo '<code>classB::PROTECTED_B;</code> ' . classB::PROTECTED_B;
echo "<br>..<br>";
//echo '<code>classB::PRIVATE_B;</code> ' . classB::PRIVATE_B;
echo "<br>..<br>";

?>
