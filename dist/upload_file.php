<?php
include 'start_php.php';

$return = [];
foreach ($_FILES as $key => $file) {
    if (is_array($file['tmp_name'])) {
        $_FILES[$key]['is_uploaded'] = [];
        foreach ($file['tmp_name'] as $i => $name) {
            if ($file['error'][$i] === UPLOAD_ERR_OK) {
                $new_path = $name;
                $_FILES[$key]['tmp_name'][$i] = $new_path;
                $_FILES[$key]['is_uploaded'][$i] = move_uploaded_file($name, $new_path);
            }
        }
    } else {
        if ($file['error'] === UPLOAD_ERR_OK) {
            $new_path = $file['tmp_name'];
            $_FILES[$key]['tmp_name'] = $new_path;
            $_FILES[$key]['is_uploaded'] = move_uploaded_file($file['tmp_name'], $new_path);
        }
    }

    $return[] = $key;
}


$session->register('ajax_upload', $_FILES);

include 'close.php';

die(json_encode($return));