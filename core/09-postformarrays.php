<?php

echo '
<form action="postformarrays.php" method="post" id="apply2all">
  <input type="submit" name="all" value="Blue all">
  <input type="submit" name="all" value="Red all">
</form>

<table>
  <tr>
    <td>
      <label><input type="checkbox" name="n_1" value="item_1" form="apply2all"> Me too </label>
    </td>
    <td>
      | Item One
    </td>
  </tr>
  <tr>
    <td>
      <label><input type="checkbox" name="n_2" value="item_2" form="apply2all"> Me too </label>
    </td>
    <td>
      | Item Two
    </td>
  </tr>
  <tr>
    <td>
      <label><input type="checkbox" name="n_3" value="item_3" form="apply2all"> Me too </label>
    </td>
    <td>
      | Item Three
    </td>
  </tr>
  <tr>
    <td>
      <label><input type="checkbox" name="n_4" value="item_4" form="apply2all"> Me too </label>
    </td>
    <td>
      | Item Four
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

  // Assign from which button
  if ($_POST['all'] == 'Blue all') {
    $pressed_button = 'blue button';
  } elseif ($_POST['all'] == 'Red all') {
    $pressed_button = 'red button';
  }

  // We don't want [all] in our foreach($_POST) loop
  unset($_POST['all']);

  // Process each entry
  foreach($_POST as $item) {
    echo '<pre>';

    echo $pressed_button.' -- '.$item; // echo in this example
    // or do anything else here...
    //some_function($item, $pressed_button);
    //include('some_file.php?a=$item&b=$pressed_button');

    echo '</pre>';
  }


  exit ();
}

?>
