<?php

var_dump($_FILES);

$temp_file = $_FILES['upload_file']['tmp_name'][0];

$file_path_dest =  'dropzone_uploads/'.$_FILES['upload_file']['name'][0];

move_uploaded_file($temp_file, $file_path_dest);

?>
