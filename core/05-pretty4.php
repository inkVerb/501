<?php

// Process GET arguments more thoroughly...

// n=
if (isset($_GET['n'])) {
  $n = $_GET['n'];
  echo $n;
}

// o=
if (isset($_GET['o'])) {
  $o = $_GET['o'];
  echo '<br>'; // We need a new line
  echo $o;
}

?>
