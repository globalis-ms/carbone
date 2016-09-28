<?php
// Chargement du framework

require 'start_php.php';

// Gestion des accès si la base de données est disponible

if ($indicateur_db === TRUE){
    redirect(CFG_PATH_HTTP.'/erreur.php');
}

// Construction du titre de la page <title>

define('RUBRIQUE_TITRE', STR_NOBDD_TITRE);

// Embarquement des scripts coté client <script javascript>

define('LOAD_JAVASCRIPT','');

// Début de l'affichage

require 'start_html.php';

echo '<p>'.sprintf(STR_NOBDD_MESSAGE,CFG_TITRE).'</p>';
echo '<p>'.sprintf(STR_NOBDD_MESSAGE_BACK, CFG_PATH_HTTP).'</p>';

// Fin de l'affichage / passage par stop_html.php plutôt que par stop_php.php pour éviter la clôture de la session et de la connexion SGBD

require 'stop_html.php';
?>
