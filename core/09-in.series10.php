<?php

// Must be set:
// $form_id = 'x';
// $edit_piece_id = 'x';

?>

<!-- AJAX a form via JavaScript & PHP from 501-06 -->
<script>
window.addEventListener( "load", function () {
  function sendData() {
    const AJAX = new XMLHttpRequest(); // AJAX handler
    const FD = new FormData( form ); // Bind to-send data to form element

    AJAX.addEventListener( "load", function(event) {
      document.getElementById("p_series").innerHTML = event.target.responseText;
    } ); // Change HTML on successful response

    AJAX.addEventListener( "error", function( event ) {
      document.getElementById("p_series").innerHTML =  'Oops! Something went wrong.';
    } );

    AJAX.open( "POST", "ajax.series.php" ); // Send data, ajax.series.php can be any file or URL

    AJAX.send( FD ); // Data sent is from the form
  } // sendData() function

  const form = document.getElementById( "add_new_series_<?php echo $edit_piece_id; ?>" ); // Access <form id="___">, id= can be anything
  form.addEventListener( "submit", function ( event ) { // Takeover <input type="submit">
    event.preventDefault();
    sendData();
  } );

} );
</script>

<?php

// Query the Serieses
$query = "SELECT id, name FROM series";
$call = mysqli_query($database, $query);

// Start the select input
// We need the div with our AJAX form inside so the input value is reset on success
echo '
<div id="p_series'.$edit_piece_id.'">
<select form="'.$form_id.$edit_piece_id.'" name="p_series">';

// Iterate each Series
while ($row = mysqli_fetch_array($call, MYSQLI_NUM)) {
  $s_id = "$row[0]";
  $s_name = "$row[1]";
  $selected_yn = ($p_series == $s_id) ? ' selected' : ''; // So 'selected' appears in the current Series
  echo '<option value="'.$s_id.'"'.$selected_yn.'>'.$s_name.'</option>';
}

// Finish the select input
echo '</select>';

// New Series form
echo '<br><br>
<form id="add_new_series_'.$edit_piece_id.'">
  <input form="add_new_series_'.$edit_piece_id.'" type="text" name="new_series" id="new_series_input_text">
  <input form="add_new_series_'.$edit_piece_id.'" type="submit" value="+ Series">
</form>
</div>
';

// Space
echo '<br><br>';
