<?php
// Chargement du framework

require 'start_php.php';

// Construction du titre de la page <title>

define('RUBRIQUE_TITRE', STR_UTILISATEUR_TITRE);

// Embarquement des scripts et styles additionnels nécessaires

define('LOAD_JAVASCRIPT', 'backoffice/jquery.backoffice.js');
define('LOAD_CSS', '');

// Début de l'affichage

require 'start_html.php';

//
// Début du traitement
//

// Backoffice Alpha

$structure_alpha= array(
    'context' => array(
        'name' => 'utilisateur_double_alpha',
    ),
    'script' => array(
        'name'=> basename($_SERVER['PHP_SELF']),
        'action'=> array('label' => 'action'),
        'id' =>    array('label' => 'id', 'value' => 'user_id'),
    ),
    'config' => array(
        'css' => 'backoffice',
        'action'=> array('empty'=>TRUE, 'hide'=>TRUE, 'width'=>'40%', 'token'=>TRUE),
        'total' => TRUE,
        'total_string' => STR_UTILISATEUR_LIBELLE_NOMBRE,
        'view_empty' => FALSE,
        'type' => 'print',
        'ajax' => FALSE,
        'logical' => 'AND'
    ),
    'filtre' => array(
        array('field'=>'nom',       'label'=>STR_UTILISATEUR_LIBELLE_NOM,       'type'=>'alpha',    'like'=>'value%'),
        array('field'=>'prenom',    'label'=>STR_UTILISATEUR_LIBELLE_PRENOM,    'type'=>'alpha',    'like'=>'value%'),
        array('field'=>'poste',     'label'=>STR_UTILISATEUR_LIBELLE_POSTE,     'type'=>'liste',    'value'=> array('Chef de projet'=>'Chef de projet', 'Développeur'=>'Développeur', 'Démonstrateur'=>'Démonstrateur', 'Commercial' => 'Commercial', 'Stagiaire' => 'Stagiaire', 'Directeur' => 'Directeur'), 'like'=>TRUE, 'logical'=>'OR'),
    ),
    'requete' => array(
        'select'=>'SELECT user_id, actif, nom, prenom, poste FROM '.CFG_TABLE_USER.' ORDER BY nom, prenom ASC',
        //'select'=>'SELECT user_id, actif, nom, prenom, theme, last FROM '.CFG_TABLE_USER.' GROUP BY theme ORDER BY nom, prenom ASC',
        //'select'=>'SELECT DISTINCT(theme) FROM '.CFG_TABLE_USER.' ORDER BY nom, prenom ASC',
    ),
    'data' => array(
        array('field'=>'nom',       'label'=>STR_UTILISATEUR_LIBELLE_NOM,       'order'=>TRUE, 'export'=>TRUE,  'rowspan'=>TRUE),
        array('field'=>'prenom',    'label'=>STR_UTILISATEUR_LIBELLE_PRENOM,    'order'=>TRUE, 'export'=>TRUE,  'rowspan'=>TRUE),
        array('field'=>'poste',     'label'=>STR_UTILISATEUR_LIBELLE_POSTE,     'order'=>TRUE, 'export'=>TRUE,  'rowspan'=>TRUE),
    ),
    'navigation' => array(
        'page' => 10,
        'item' => 10,
        'choix_item' => array(5,10,15,20,0),
    ),
    'export' => array(
        'format'    => array('pdf', 'excel'),    // pdf ou excel
    ),
);

backoffice($structure_alpha);

echo "<hr/>";

// Backoffice Beta

$structure_beta= array(
    'context' => array(
        'name' => 'utilisateur_double_beta',
    ),
    'script' => array(
        'name'=> basename($_SERVER['PHP_SELF']),
        'action'=> array('label' => 'action'),
        'id' =>    array('label' => 'id', 'value' => 'user_id'),
    ),
    'config' => array(
        'css' => 'backoffice',
        'action'=> array('empty'=>TRUE, 'hide'=>TRUE, 'width'=>'30%', 'token'=>TRUE),
        'total' => TRUE,
        'total_string' => STR_UTILISATEUR_LIBELLE_NOMBRE,
        'view_empty' => FALSE,
        'type' => 'print',
        'ajax' => TRUE,
    ),
    'filtre' => array(
        array('field'=>'nom',       'label'=>STR_UTILISATEUR_LIBELLE_NOM,       'type'=>'input',    'like'=>TRUE),
        array('field'=>'prenom',    'label'=>STR_UTILISATEUR_LIBELLE_PRENOM,    'type'=>'input',    'like'=>TRUE),
        array('field'=>'poste',     'label'=>STR_UTILISATEUR_LIBELLE_POSTE,     'type'=>'select',   'value'=>array(''=>STR_UTILISATEUR_LIBELLE_TOUS, 'Chef de projet'=>'Chef de projet', 'Développeur'=>'Développeur', 'Démonstrateur'=>'Démonstrateur', 'Commercial' => 'Commercial', 'Stagiaire' => 'Stagiaire', 'Directeur' => 'Directeur'), 'like'=>TRUE),
    ),
    'requete' => array(
        'select'=>'SELECT user_id, nom, prenom, poste FROM '.CFG_TABLE_USER.' ORDER BY nom, prenom ASC',
        //'select'=>'SELECT user_id, actif, nom, prenom, theme, last FROM '.CFG_TABLE_USER.' GROUP BY theme ORDER BY nom, prenom ASC',
        //'select'=>'SELECT DISTINCT(theme) FROM '.CFG_TABLE_USER.' ORDER BY nom, prenom ASC',
    ),
    'data' => array(
        array('field'=>'nom',       'label'=>STR_UTILISATEUR_LIBELLE_NOM,       'order'=>TRUE, 'export'=>TRUE,  'rowspan'=>TRUE),
        array('field'=>'prenom',    'label'=>STR_UTILISATEUR_LIBELLE_PRENOM,    'order'=>TRUE, 'export'=>TRUE,  'rowspan'=>TRUE),
        array('field'=>'poste',     'label'=>STR_UTILISATEUR_LIBELLE_POSTE,     'order'=>TRUE, 'export'=>TRUE,  'rowspan'=>TRUE),
    ),
    'navigation' => array(
        'page' => 10,
        'item' => 5,
        'choix_item' => array(5,10,15,20,0),
    ),
    'export' => array(
        'format'    => array('pdf', 'excel'),    // pdf ou excel
    ),
);

backoffice($structure_beta);

//
// Fin du traitement
//

require 'stop_php.php';
?>