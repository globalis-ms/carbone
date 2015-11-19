<?php
// Chargement du framework

require 'start_php.php';

// Construction du titre de la page <title>

define('RUBRIQUE_TITRE', STR_ERREUR_TITRE);

// Début de l'affichage

require 'start_html.php';

//
// Début du traitement
//

echo STR_ERREUR_MESSAGE;
if(isset($_SERVER['HTTP_REFERER']) && $_SERVER['HTTP_REFERER']!='')
	printf(STR_ERREUR_MESSAGE_BACK, $_SERVER['HTTP_REFERER']);

//
// Fin du traitement
//

require 'stop_php.php';
?>