<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $foo = $_POST['foo'];
  $bar = $_POST['bar'];

echo $foo;

echo '<br>';

echo $bar;

}
?>
