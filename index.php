<?php
// Chargement du framework

require 'start_php.php';

// Construction du titre de la page <title>

define('RUBRIQUE_TITRE', STR_ACCUEIL_TITRE);

// Début de l'affichage

require 'start_html.php';

//
// Début du traitement
//

$changelog=explode('----------', file_get_contents('changelog.txt'));

print nl2br($changelog[0]);

//
// Fin du traitement
//

require 'stop_php.php';
?>