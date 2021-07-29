<?php

class classA {

  public function APublic() {
    return "Public A";
  }

  protected function AProtected() {
    return "Protected A";
  }

  private function APrivate() {
    return "Private A ";
  }

  function CallA() {
    return $this->APublic() . ' & ' . $this->AProtected() . ' & ' . $this->APrivate();
  }

}

class classB extends classA {

  public function BPublic() {
    return "Public B";
  }

  protected function BProtected() {
    return "Protected B";
  }

  private function BPrivate() {
    return "Private B";
  }

  function BCall() {
    return $this->BPublic() . ' & ' . $this->BProtected() . ' & ' . $this->BPrivate();
  }

  function BCallA() {
    return $this->APublic() . ' & ' . $this->AProtected();
  }

  function BFailCall() {
    return $this->APrivate();
  }

}

// Instantiate
$ObjectA = new classA;
$ObjectB = new classB;

// Display
echo "<h1>Class A</h1>";
echo '<code>$ObjectA->APublic();</code> ' . $ObjectA->APublic();
echo "<br>..<br>";
//echo '<code>$ObjectA->AProtected();</code> ' . $ObjectA->AProtected();
echo "<br>..<br>";
//echo '<code>$ObjectA->APrivate();</code> ' . $ObjectA->APrivate();
echo "<br>..<br>";
echo '<code>$ObjectA->CallA();</code> ' . $ObjectA->CallA();
echo "<br>..<br>";

echo "<h1>Class B</h1>";
echo '<code>$ObjectB->BPublic();</code> ' . $ObjectB->BPublic();
echo "<br>..<br>";
//echo '<code>$ObjectB->BProtected();</code> ' . $ObjectB->BProtected();
echo "<br>..<br>";
//echo '<code>$ObjectB->BPrivate();</code> ' . $ObjectB->BPrivate();
echo "<br>..<br>";
echo '<code>$ObjectB->BCall();</code> ' . $ObjectB->BCall();
echo "<br>..<br>";
echo '<code>$ObjectB->BCallA();</code> ' . $ObjectB->BCallA();
echo "<br>..<br>";
//echo '<code>$ObjectB->BFailCall();</code> ' . $ObjectB->BFailCall();
echo "<br>..<br>";

?>
