<?php

echo '<form action="postformarrays.php" method="post" id="apply2all">
  <input type="submit" name="all" value="Blue all">
  <input type="submit" name="all" value="Red all">
</form>


<table>
  <tr>
    <td>
      <input type="checkbox" name="1" value="1" form="apply2all">
    </td>
    <td>
      Item One
    </td>
  </tr>
  <tr>
    <td>
      <input type="checkbox" name="2" value="2" form="apply2all">
    </td>
    <td>
      Item Two
    </td>
  </tr>
  <tr>
    <td>
      <input type="checkbox" name="3" value="3" form="apply2all">
    </td>
    <td>
      Item Three
    </td>
  </tr>
  <tr>
    <td>
      <input type="checkbox" name="4" value="4" form="apply2all">
    </td>
    <td>
      Item Four
    </td>
  </tr>
</table>
';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

  echo '<pre>This is the entire raw _POST array:</pre>';
  echo '<pre>';
  print_r($_POST);
  echo '</pre>';

  echo '<p>This is how we can process each:</p>';

  if ($_POST['all'] == 'Blue all') {
    unset($_POST['all']);

    foreach($_POST as $item) {
        echo '<pre>';
        echo 'blue '.$item;
        echo '</pre>';
    }
  } elseif ($_POST['all'] == 'Red all') {
    unset($_POST['all']);

    foreach($_POST as $item) {
        echo '<pre>';
        echo 'red '.$item;
        echo '</pre>';
    }
  }

  exit();
}

?>
