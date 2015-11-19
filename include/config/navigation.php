<?php
//
// Navigation
//
// Remarque :
// Définition des rubriques et sous rubrique
// On distingue le cas de l'utilisateur connecté et non connecté
//
// La structure d'une entrée navigation est la suivante
//
// 'level'     => integer (requis), niveau de l'arborescence 1 = rubrique, 2 = sous rubrique, etc.
// 'libelle'   => string  (requis), libelle de la rubrique (ou sous rubrique, etc.) qui sera affiché
// 'url'       => string  (requis), url vers laquelle pointer
// 'acl'       => string  (requis), acl (access control list, éventuellement composée de plusieurs valeurs séparées par des pipe)
// 'class'     => string  (option), class ou image pour illustrer la rubrique
// 'titre'     => string  (option), titre (à afficher dans un attribut title, par exemple)
// 'js'        => string  (option), bout de code javascript (pour un évenement onclick, par exemple)
//

if($session->session_expired == FALSE && (!empty($session_user_id))) {

    //
    // Utilisateur connecté
    //

    $navigation = array(
        array(
            'level'     => 1,
            'libelle'   => STR_RUBRIQUE_DECONNEXION,
            'url'       => $session->url(CFG_PATH_HTTP.'/logout.php'),
            'acl'       => '',
            'class'     => 'glyphicon glyphicon-off',
        ),
        array(
            'level'     => 1,
            'libelle'   => '',
            'url'       => '',
            'acl'       => '',
        ),
        array(
            'level'     => 1,
            'libelle'   => STR_RUBRIQUE_ACCUEIL,
            'url'       => $session->url(CFG_PATH_HTTP.'/index.php'),
            'acl'       => '',
            'class'     => 'glyphicon glyphicon-home',
        ),
        array(
            'level'     => 1,
            'libelle'   => STR_RUBRIQUE_PROFIL,
            'url'       => $session->url(CFG_PATH_HTTP.'/profil.php?action=edit'),
            'acl'       => 'admin|user',
            'class'     => 'glyphicon glyphicon-adjust',
        ),
        array(
            'level'     => 1,
            'libelle'   => '',
            'url'       => '',
            'acl'       => '',
        ),
        array(
            'level'     => 1,
            'libelle'   => STR_RUBRIQUE_UTILISATEUR,
            'url'       => '',
            'acl'       => 'admin|user',
            'class'     => 'glyphicon glyphicon-user',
        ),

        //
        // Début sous rubrique utilisateur
        //

        array(
            'level'     => 2,
            'libelle'   => STR_RUBRIQUE_UTILISATEUR,
            'url'       => $session->url(CFG_PATH_HTTP.'/utilisateur/simple.php'),
            'acl'       => 'admin|user',
            'user'      => array(
                'get'   => array(
                    'action'    => 'view|active,desactive|edit',
                    //'id'        => '1'
                ),
                'post'  => 'utilisateur_simple_reset|form|actif|acl|poste|nom|prenom|email|password|submit|retour',
                'data'  => 'nom|prenom',
                'filtre'=> 'nom|poste|prenom',
                'export'=> 'pdf'
            ),
        ),
        array(
            'level'     => 2,
            'libelle'   => STR_RUBRIQUE_UTILISATEUR.' (3 scripts)',
            'url'       => $session->url(CFG_PATH_HTTP.'/utilisateur/split.php'),
            'acl'       => 'admin',
        ),
        array(
            'level'     => 2,
            'libelle'   => STR_RUBRIQUE_UTILISATEUR.' (2 briques backoffice)',
            'url'       => $session->url(CFG_PATH_HTTP.'/utilisateur/double.php'),
            'acl'       => 'admin',
        ),

        //
        // Fin sous rubrique utilisateur
        //

        array(
            'level'     => 1,
            'libelle'   => STR_RUBRIQUE_EXEMPLE,
            'url'       => '',
            'acl'       => '',
            'class'     => 'glyphicon glyphicon-list-alt',
        ),

        //
        //  Début sous rubrique exemple
        //

        array(
            'level'     => 2,
            'libelle'   => STR_RUBRIQUE_FORMULAIRE,
            'url'       => $session->url(CFG_PATH_HTTP.'/exemple/formulaire.php?action=add'),
            'acl'       => '',
        ),
        array(
            'level'     => 2,
            'libelle'   => STR_RUBRIQUE_FILE,
            'url'       => $session->url(CFG_PATH_HTTP.'/exemple/file.php'),
            'acl'       => '',
            'user'      => array(
                'action'   => 'view',
            ),
        ),
        array(
            'level'     => 2,
            'libelle'   => STR_RUBRIQUE_EDITEUR,
            'url'       => $session->url(CFG_PATH_HTTP.'/exemple/editeur.php?action=add'),
            'acl'       => '',
        ),
        array(
            'level'     => 2,
            'libelle'   => '',
            'url'       => '',
            'acl'       => '',
        ),
        array(
            'level'     => 2,
            'libelle'   => STR_RUBRIQUE_DIVERS,
            'url'       => '',
            'acl'       => '',
        ),
        array(
            'level'     => 2,
            'libelle'   => STR_RUBRIQUE_SPECIAL,
            'url'       => $session->url(CFG_PATH_HTTP.'/exemple/special.php?action=add'),
            'acl'       => '',
        ),

        //
        //  Fin sous rubrique exemple
        //

        array(
            'level'     => 1,
            'libelle'   => STR_RUBRIQUE_ONGLET,
            'url'       => $session->url(CFG_PATH_HTTP.'/onglet/index.php'),
            'acl'       => 'admin',
            'class'     => 'glyphicon glyphicon-tasks',
        ),
    );

    //
    //  Sous rubrique onglet
    //

    $navigation_onglet = array(
        array(
            'level'     => 3,
            'libelle'   => STR_RUBRIQUE_ONGLET_RETOUR,
            'url'       => $session->url(CFG_PATH_HTTP.'/onglet/index.php'),
            'acl'       => 'admin',
        ),
        array(
            'level'     => 3,
            'libelle'   => STR_RUBRIQUE_ONGLET_COMPTE,
            'url'       => $session->url(CFG_PATH_HTTP.'/onglet/index.php?action=edit%s'),
            'acl'       => 'admin',
        ),
        array(
            'level'     => 3,
            'libelle'   => STR_RUBRIQUE_ONGLET_COULEUR,
            'url'       => $session->url(CFG_PATH_HTTP.'/onglet/couleur.php?action=edit%s'),
            'acl'       => 'admin',
        ),
        array(
            'level'     => 3,
            'libelle'   => STR_RUBRIQUE_ONGLET_THEME,
            'url'       => $session->url(CFG_PATH_HTTP.'/onglet/theme.php?action=edit%s'),
            'acl'       => 'admin',
        ),
    );

    $navigation = array_merge($navigation, $navigation_onglet);

} else {

    //
    // Utilisateur non connecté
    //

    $navigation = array(
        array(
            'level'     => 1,
            'libelle'   => STR_RUBRIQUE_CONNEXION,
            'url'       => CFG_PATH_HTTP.'/login.php',
            'acl'       => '',
            'class'     => 'glyphicon glyphicon-off',
        ),
    );
}

require dirname(__FILE__).'/../../'.'web/theme/'.$cfg_profil['theme'].'/navigation.php';
?>