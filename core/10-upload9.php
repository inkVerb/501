<?php

var_dump($_FILES);

$temp_file = $_FILES['upload_file']['tmp_name'];

$file_path_dest =  'dropzone_uploads/'.$_FILES['upload_file']['name'];

move_uploaded_file($temp_file, $file_path_dest);

?>
