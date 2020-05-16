<?php
//if ((isset($_POST['go'])) && (isset($_POST['time']))) {
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $go = $_POST['go'];
  $time = $_POST['time'];

echo $go;

echo '<br>';

echo $time;
}
?>
