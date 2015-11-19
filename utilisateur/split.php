<?php
// Chargement du framework

require 'start_php.php';

// Construction du titre de la page <title>

define('RUBRIQUE_TITRE', STR_UTILISATEUR_TITRE);

// Embarquement des scripts coté client <script javascript>

if(!empty($_GET['action']) && ($_GET['action']=='add' || $_GET['action']=='edit'))
    define('LOAD_JAVASCRIPT','autocomplete/jquery.autocomplete.js|multiselect/jquery.multiselect.js');
else
    define('LOAD_JAVASCRIPT','autocomplete/jquery.autocomplete.js|datepicker/bootstrap.datepicker.js|backoffice/jquery.backoffice.js|notice/jquery.notice.js');

// Début de l'affichage

require 'start_html.php';

//
// Début du traitement
//

// Formulaire
// Si action en GET

if(!empty($_GET['action']))
	require 'split_form.php';

// Backoffice
// Si pas d'action en GET

if(empty($_GET['action']))
	require 'split_bo.php';

//
// Fin du traitement
//

require 'stop_php.php';
?>