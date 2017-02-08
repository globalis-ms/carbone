<?php
// Chargement du framework

require 'start_php.php';

// Construction du titre de la page <title>

if(!empty($_GET['action']) && $_GET['action']=='add')
    define('RUBRIQUE_TITRE', STR_ONGLET_TITRE.' > '.STR_ONGLET_TITRE_ETAPE_1);
elseif(!empty($_GET['action']) && $_GET['action']=='edit')  {
        $sql = 'SELECT nom, prenom FROM '.CFG_TABLE_USER.' WHERE user_id = '.$db->qstr($_GET['user_id']);
        $edit = $db->getrow($sql);
        define('RUBRIQUE_TITRE', STR_ONGLET_TITRE.' > '.STR_ONGLET_TITRE_ETAPE_3.' > '.$edit['prenom'].' '.$edit['nom']);
}
else
    define('RUBRIQUE_TITRE', STR_ONGLET_TITRE.' > '.STR_ONGLET_SOUS_TITRE);

// Embarquement des scripts et styles additionnels nécessaires

define('LOAD_JAVASCRIPT', '');
define('LOAD_CSS', '');

// Début de l'affichage

require 'start_html.php';

//
// Début du traitement
//

// Formulaire
// Si action en GET

if(!empty($_GET['action']))
{

    switch ($_GET['action'])
    {
        case 'edit' :

            if(empty($_POST))
            {
                $sql = 'SELECT theme FROM '.CFG_TABLE_USER.' WHERE user_id = '.$db->qstr($_GET['user_id']);
                $edit = $db->getrow($sql);
            }

        default :

            // Construction de l'url <form>

            $url=get_url(basename($_SERVER['PHP_SELF']));

            // Initialisation des valeurs par défaut

            $data = array(
                'theme' => '',
            );

            // Mise a jour des données selon ce que contient $_POST

            $data = form($data, !empty($edit) ? $edit : array());

            // Construction du Formulaire

            $form_structure = array(
                'form' => array(
                    'item' => 'form',
                    'action' => $url
                ),

                'theme' => array(
                    'item' => 'select',
                    'tpl' => '[(3){libelle}(4){form}(5){legende}]',
                    'libelle' => STR_UTILISATEUR_LIBELLE_THEME,
                    'value' => array(''=>'&nbsp;') + get_theme(),
                    'multiple' => FALSE,
                    'selected' => $data['theme'],
                    'require' => TRUE,
                ),

                'submit' => array(
                    'item' => 'button',
                    'tpl' => '[(3){libelle}(9){form} ...',
                    'value' => STR_FORM_SUBMIT,
                    'class' => 'btn btn-primary',
                    'type' => 'submit',
                ),

                'retour' => array(
                    'item' => 'button',
                    'tpl' => ' {form}]',
                    'value' => STR_RETOUR,
                    'type' => 'button',
                    'js' => 'onclick="window.location=\''.$session->url(CFG_PATH_HTTP.'/onglet/index.php').'\'"',
                ),
            );

            // form_check retourne un tableau ayant la structure suivante :
            //
            //     $form_error=array(
            //        'fatal' => array(),
            //        'warning' => array(),
            //     );
            //
            //     'fatal' contient les erreurs bloquantes
            //     'warning' contiendra les erreurs non bloquantes éventuellements issues des tests secondaires

            $form_error=form_check($form_structure);

            // Si _POST n'est pas vide

            if(!empty($_POST)) {

                // Si pas d'erreur bloquante -> Traitement complémentaire

                // if(empty($form_error['fatal'])) {
                //
                // }

                // Si toujours pas d'erreur bloquante -> Traitement sgbd

                if(empty($form_error['fatal'])) {

                    // Traitement des modifications

                    $sql = 'UPDATE '.CFG_TABLE_USER.' SET ';
                    $sql.= 'theme = 	'.$db->qstr($_POST['theme']).' ';
                    $sql.= 'WHERE ';
                    $sql.= 'user_id = 	'.$db->qstr($_GET['user_id']);

                    $db->execute($sql);

                    // Ici on reste sur le formulaire

                    echo(get_menu_local(get_menu_acl($navigation_onglet, $cfg_profil['acl']), 'user_id='.$_GET['user_id']));
                    echo form_parser($form_structure);
                }

                // Si erreur bloquante

                else {
                    if(!empty($_GET['user_id']))
                        echo(get_menu_local(get_menu_acl($navigation_onglet, $cfg_profil['acl']), 'user_id='.$_GET['user_id']));

                    echo form_message(STR_FORM_E_FATAL, $form_error['fatal']);
                    echo form_parser($form_structure);
                }
            }

            // Si _POST est vide

            else {
                if(!empty($_GET['user_id']))
                    echo(get_menu_local(get_menu_acl($navigation_onglet, $cfg_profil['acl']), 'user_id='.$_GET['user_id']));

                echo form_parser($form_structure, (isset($_GET['view'])));
            }
    }
}

//
// Fin du traitement
//

require 'stop_php.php';
?>