<?php
// On précise le charset de retour

header('Content-Type: text/html; charset=utf-8');

// Construction du flux

$q = $_GET['q'];

if (!$q) return;

// Paire clé/valeur

$tableau = file('../data/prenom.dat');

foreach ($tableau as $key=>$value) {
    if (strpos(mb_strtolower($value,'UTF-8'), $q) !== FALSE) {
        echo "$value\n";
    }
}
?>