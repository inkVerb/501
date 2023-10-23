<?php

if (!empty($_FILES)) {

    $temp_file = $_FILES['file']['tmp_name'];

    $file_path_dest =  'dropzone_uploads/'.$_FILES['file']['name'];

    move_uploaded_file($temp_file, $file_path_dest);

}
?>
