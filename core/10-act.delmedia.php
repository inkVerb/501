<?php

// Include our config (with SQL) up near the top of our PHP file
include ('./in.config.php');

// Must be logged in
if (!isset($_SESSION['user_id'])) {
  exit(header("Location: blog.php"));
}

// Delete
if ($_POST['deleteaction'] == 'confirm delete forever') {
  unset($_POST['deleteaction']);
  foreach ($_POST as $m_id) {

    // Validate
    if (!filter_var($m_id, FILTER_VALIDATE_INT)) {
      continue;
    }

    // Get the file name from the database
    $query = "SELECT file_base, file_extension, location FROM media_library WHERE id='$m_id'";
    $call = mysqli_query($database, $query);

    while ($row = mysqli_fetch_array($call, MYSQLI_NUM)) {
      $m_file_base = "$row[0]";
      $m_file_extension = "$row[1]";
      $m_basic_location = "$row[2]";

      // Delete the file
      if (unlink('media/'.$m_basic_location.'/'.$m_file_base.'.'.$m_file_extension)) {

        // Delete the database entry
        $query = "DELETE FROM media_library WHERE id='$m_id'";
        $call = mysqli_query($database, $query);

        // Check SQL
        if (!$call) {
          exit('<pre class="error">Could not delete file from database: '.$m_basic_location.'/'.$m_file_base.'.'.$m_file_extension.' id: '.$m_id.'</pre>');
        }

      // Check file system
      } else {
        exit('<pre class="error">Could not delete file from server: '.$m_basic_location.'/'.$m_file_base.'.'.$m_file_extension.' id: '.$m_id.'</pre>');
      }

    } // End file

  } // for loop

  // Done, go home
  exit(header("Location: medialibrary.php"));

// Fail, get out of here
} else {
  exit(header("Location: blog.php"));
}

?>
