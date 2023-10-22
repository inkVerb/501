<?php $ajax_token = $_SESSION['ajax_token']; ?>

<script>
  function newSeries() {
    // Bind a new event listener every time the <form> is changed:
    const FORM = document.getElementById("add_new_series");
    const AJAX = new XMLHttpRequest(); // AJAX handler
    const formData = new FormData(FORM); // Bind to-send data to form element

    AJAX.addEventListener( "load", function(event) {
      document.getElementById("p_series").innerHTML = event.target.responseText;
    } ); // Change HTML on successful response

    AJAX.addEventListener( "error", function( event ) {
      document.getElementById("p_series").innerHTML =  'Oops! Something went wrong.';
    } );

    AJAX.open( "POST", "ajax.series.php" ); // Send data, ajax.series.php can be any file or URL

    formData.append('ajax_token', '<?php echo $ajax_token; ?>');
    AJAX.send(formData); // Data sent is from the form
  } // newSeries() function
</script>

<?php

echo '<div id="p_series">';

if (($series_form == 'edit_piece') || ($series_form == 'blog_settings') || ($series_form == 'new_feed')) {
  // Query the Serieses
  $rows = $pdo->exec_($database->prepare("SELECT id, name FROM series ORDER BY name"));

  // Start the select input
  // We need the div with our AJAX form inside so the input value is reset on success
  echo '<select form="'.$series_form.'" name="p_series"';
  if ($series_form == 'edit_piece') {
    echo ' onchange="onNavWarn();" onkeyup="onNavWarn();" onclick="onNavWarn();"';
  }
  echo '>';

  // Iterate each Series
  foreach ($rows as $row) {
    $s_id = "$row->id";
    $s_name = "$row->name";
    $selected_yn = ((($p_series == $s_id) && ($series_form == 'edit_piece'))
    || (($blog_default_series == $s_id) && ($series_form == 'blog_settings'))
    || (($p_series == $s_id) && ($series_form == 'new_feed'))) ? ' selected="selected"' : ''; // So 'selected="selected"' appears in the Series
    echo '<option value="'.$s_id.'"'.$selected_yn.'>'.$s_name.'</option>';
  }

  // Finish the select input
  echo '</select>';
}

// New Series form
echo '<br><br>
<form id="add_new_series">
  <input form="add_new_series" type="hidden" name="series_form" value="'.$series_form.'">
  <input form="add_new_series" type="text" name="new_series" id="new_series_input_text">
  <button type="button" title="Create the new series" onclick="newSeries();';
  if ($series_form == 'edit_piece') {
    echo ' offNavWarn();';
  }
  echo '">+ Series</button>
</form>
</div>
';
