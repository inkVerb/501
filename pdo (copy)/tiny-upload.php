<?php

if (!empty($_FILES)) {

    $temp_file = $_FILES['file']['tmp_name'];

    $file_path_dest =  'tinymce_uploads/'.$_FILES['file']['name'];

    move_uploaded_file($temp_file, $file_path_dest);

    $json_file_is_here = json_encode(array('filepath' => $file_path_dest));

    echo $json_file_is_here;

}

?>
