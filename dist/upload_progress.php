<?php
include 'start_php.php';
session_start();

include 'close.php';

$return = null;
if (isset($_SESSION['upload_progress_' . CFG_UPLOAD_PROGRESS_NAME])) {
    $return = $_SESSION['upload_progress_' . CFG_UPLOAD_PROGRESS_NAME];
}

die(json_encode($return));
