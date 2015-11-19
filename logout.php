<?php
// Chargement du framework

require 'start_php.php';

// Destruction de la session et retour à la page login

$session->destroy();
$url='login.php';
header("Location: $url");
?>