<?php
// Chargement du framework

require 'start_php.php';

// Construction du titre de la page <title>

define('RUBRIQUE_TITRE', STR_PROFIL_TITRE);

// Embarquement des scripts et styles additionnels nécessaires

define('LOAD_JAVASCRIPT', '');
define('LOAD_CSS', '');

// Début de l'affichage

require 'start_html.php';

//
// Début du traitement
//

if(!isset($_GET['action']))
    $_GET['action']='edit';

// Si Form

if(!empty($_GET['action'])) {

    switch ($_GET['action']) {
        case 'view' :
        case 'edit' :

            if(empty($_POST)) {

                $sql = 'SELECT actif, nom, prenom, email, acl, langue, theme FROM '.CFG_TABLE_USER.' ';
                $sql.= 'WHERE ';
                $sql.= 'user_id = '.$db->qstr($session_user_id);

                $edit = $db->getrow($sql);

                $edit['password']='';
                $edit['password_confirmation']='';
            }

        default :

            // Construction de l'url <form>

            $url=get_url(basename($_SERVER['PHP_SELF']));

            // Initialisation des valeurs par défaut

            $data = array(
                'nom' => '',
                'prenom' => '',
                'username' => '',
                'password' => '',
                'password_confirmation' => '',
                'theme' => CFG_THEME_DEFAULT,
                'langue' => CFG_LANGUE_DEFAULT,
            );

            // Mise a jour des données selon ce que contient $_POST

            $data = form($data, !empty($edit) ? $edit : array());

            // Construction du Formulaire

            $form_structure = array(
                'form' => array(
                    'item' => 'form',
                    'action' => $url,
                    'name' => 'profil_form',
                    'legende' => STR_FORM_LEGENDE,
                ),

                'nom' => array(
                    'item' => 'input',
                    'tpl' => '[(3){libelle}(4){form}(5){legende}]',
                    'libelle' => STR_UTILISATEUR_LIBELLE_NOM,
                    'value' => $data['nom'],
                    'maxlength' => 255,
                    'require' => TRUE,
                    'js' => 'style="text-transform: uppercase;" ',
                ),

                'prenom' => array(
                    'item' => 'input',
                    'tpl' => '[(3){libelle}(4){form}(5){legende}]',
                    'libelle' => STR_UTILISATEUR_LIBELLE_PRENOM,
                    'value' => $data['prenom'],
                    'maxlength' => 255,
                    'require' => TRUE,
                ),

                'password' => array(
                    'item' => 'input',
                    'tpl' => '[(3){libelle}(4){form}(5){legende}]',
                    'libelle' => STR_UTILISATEUR_LIBELLE_PASSWORD,
                    'legende' => STR_UTILISATEUR_LEGENDE_PASSWORD_MODIF,
                    'value' => $data['password'],
                    'type' => 'password',
                    'maxlength' => 255,
                    'prepend' => 'glyphicon glyphicon-lock',
                    'placeholder' => STR_LOGIN_LIBELLE_PASSWORD_PLACEHOLDER,
                    'test' => array(
                        'test_user_function' => 'test_password',
                        'test_error_message' => STR_UTILISATEUR_E_FATAL_2,
                    ),
                ),

               'password_confirmation' => array(
                    'item' => 'input',
                    'tpl' => '[(3){libelle}(4){form}(5){legende}]',
                    'legende' => STR_UTILISATEUR_LEGENDE_PASSWORD_CONFIRMATION,
                    'value' => $data['password_confirmation'],
                    'type' => 'password',
                    'maxlength' => 255,
                    'prepend' => 'glyphicon glyphicon-lock',
                    'placeholder' => STR_LOGIN_LIBELLE_PASSWORD_PLACEHOLDER,
                ),

                'theme_on' => array(
                    'item' => 'fieldset',
                    'tpl' => '[(12){libelle}{form}{legende}]',
                    'legende' => STR_UTILISATEUR_FIELDSET_THEME,
                    'type' => 'on'
                ),

                'theme' => array(
                    'item' => 'select',
                    'tpl' => '[(3){libelle}(4){form}(5){legende}]',
                    'libelle' => STR_UTILISATEUR_LIBELLE_THEME,
                    'value' => array(''=>'&nbsp;') + get_theme(),
                    'multiple' => FALSE,
                    'selected' => $data['theme'],
                    'require' => TRUE,
                    'optgroup' => TRUE,
                    'accesskey' => 'T',
                ),

                'langue' => array(
                    'item' => 'select',
                    'tpl' => '[(3){libelle}(4){form}(5){legende}]',
                    'libelle' => STR_UTILISATEUR_LIBELLE_LANGUE,
                    'value' => $cfg_langue,
                    'selected' => $data['langue'],
                    'multiple' => FALSE,
                ),

                'theme_off' => array(
                    'item' => 'fieldset',
                    'type' => 'off'
                ),

                'profil' => array(
                    'item' => 'hidden',
                    'value' => 'TRUE',
                ),

                'submit' => array(
                    'item' => 'button',
                    'tpl' => '[(3){libelle}(4){form}&nbsp;...',
                    'value' => STR_FORM_SUBMIT,
                    'type' => 'submit',
                    'class' => 'btn btn-primary',
                ),

                'back' => array(
                    'item' => 'button',
                    'tpl' => '{form}(5){legende}]',
                    'value' => STR_FORM_VIEW,
                    'type' => 'button',
                    'js' => 'onclick="window.location=\''.$session->url('profil.php?action=view').'\'"',
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

                if(empty($form_error['fatal'])) {

                    // On controle le champ password

                    if(!empty($_POST['password']) || !empty($_POST['password_confirmation'])) {
                        if($_POST['password'] != $_POST['password_confirmation'])
                            $form_error['fatal']['password'][] = STR_UTILISATEUR_E_FATAL_4;
                    }

                }

                // Si toujours pas d'erreur bloquante -> Traitement sgbd

                if(empty($form_error['fatal'])) {

                    // Traitement des modifications

                    if($_GET['action'] == 'edit') {

                        $sql = 'UPDATE '.CFG_TABLE_USER.' SET ';
                        if(!empty($_POST['password'])) {
                            $sql.= 'password = '.$db->qstr(hash('sha512', $_POST['password'])).', ';
                        }
                        $sql.= 'nom = 		'.$db->qstr(mb_strtoupper(delete_accent($_POST['nom']),'UTF-8')).', ';
                        $sql.= 'prenom = 	'.$db->qstr($_POST['prenom']).', ';
                        $sql.= 'theme = 	'.$db->qstr($_POST['theme']).', ';
                        $sql.= 'langue = 	'.$db->qstr($_POST['langue']).' ';
                        $sql.= 'WHERE ';
                        $sql.= 'user_id = 	'.$db->qstr($session_user_id);
                    }

                    $db->execute($sql);

                    //
                    // Profil : Mise a jour en cas de changement de theme ou langue
                    //
                    
                    if($session->session_expired == FALSE && (!empty($_POST['profil'])))
                    {
                        if(!empty($_POST['theme']))
                            $cfg_profil['theme']=$_POST['theme'];
                        if(!empty($_POST['langue']))
                            $cfg_profil['langue']=$_POST['langue'];

                        $session->register("cfg_profil");

                        // Redirection pour comiter les changements

                        echo form_message(STR_FORM_E_WARNING, STR_UTILISATEUR_E_INFO_2, 'success', 'session');
                        redirect(CFG_PATH_HTTP.'/profil.php?action=edit');
                    }
                    
                }

                // Si erreur bloquante

                else {
                    echo form_message(STR_FORM_E_FATAL, $form_error['fatal']);
                }
            }
    }
}

if(isset($_GET['action']) && $_GET['action']=='view') {
    echo form_parser($form_structure, (isset($_GET['action']) && $_GET['action']=='view'));
}
else
    echo form_parser($form_structure);

//
// Fin du traitement
//

require 'stop_php.php';
?>