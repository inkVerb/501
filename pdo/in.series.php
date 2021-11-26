<!-- AJAX a form via JavaScript & PHP from 501-06 -->
<script>
  function newSeries() {
    // Bind a new event listener every time the <form> is changed:
    const FORM = document.getElementById("add_new_series");
    const AJAX = new XMLHttpRequest(); // AJAX handler
    const FD = new FormData(FORM); // Bind to-send data to form element

    AJAX.addEventListener( "load", function(event) {
      document.getElementById("p_series").innerHTML = event.target.responseText;
    } ); // Change HTML on successful response

    AJAX.addEventListener( "error", function( event ) {
      document.getElementById("p_series").innerHTML =  'Oops! Something went wrong.';
    } );

    AJAX.open( "POST", "ajax.series.php" ); // Send data, ajax.series.php can be any file or URL

    AJAX.send(FD); // Data sent is from the form
  } // newSeries() function
</script>

<?php

// Query the Serieses
$rows = $pdo->exec_($database->prepare("SELECT id, name FROM series"));

// Start the select input
// We need the div with our AJAX form inside so the input value is reset on success
echo '
<div id="p_series">
<select form="edit_piece" name="p_series" onchange="onNavWarn();" onkeyup="onNavWarn();" onclick="onNavWarn();">';

// Iterate each Series
foreach ($rows as $row) {
  $s_id = "$row->id";
  $s_name = "$row->name";
  $selected_yn = ($p_series == $s_id) ? ' selected' : ''; // So 'selected' appears in the current Series
  echo '<option value="'.$s_id.'"'.$selected_yn.'>'.$s_name.'</option>';
}

// Finish the select input
echo '</select>';

// New Series form
echo '<br><br>
<form id="add_new_series">
  <input form="add_new_series" type="text" name="new_series" id="new_series_input_text">
  <button type="button" title="Create the new series" onclick="newSeries(); offNavWarn();">+ Series</button>
</form>
</div>
';

// Space
echo '<br><br>';
