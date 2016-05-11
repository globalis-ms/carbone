<?php
// Chargement du framework

require 'start_php.php';

// Construction du titre de la page <title>

define('RUBRIQUE_TITRE', STR_RESET_TITRE);

// Embarquement des scripts coté client <script javascript>

define('LOAD_JAVASCRIPT','');

// Début de l'affichage

require 'start_html.php';

//
// Début du traitement
//

// Redirection vers la page de login si pas de password_reset en GET

if(!isset($_GET['password_reset'])) {
    $session->destroy();
    $url='login.php';
    header("Location: $url");
}      

if(!isset($_GET['action']))
    $_GET['action']='edit';

// Si Form

if(!empty($_GET['action'])) {

    switch ($_GET['action']) {
        case 'view' :
        case 'edit' :

            if(empty($_POST)) {

                $edit['email']='';
                $edit['password']='';
                $edit['password_confirmation']='';
            }

        default :

            // Construction de l'url <form>

            $url=get_url(basename($_SERVER['PHP_SELF']));

            // Initialisation des valeurs par défaut

            $data = array(
                'email' => '',
                'password' => '',
                'password_confirmation' => '',
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

                'email' => array(
                    'item' => 'input',
                    'tpl' => '[(1,20){libelle}(1,40){form}(1,40){legende}]',
                    'libelle' => STR_UTILISATEUR_LIBELLE_EMAIL,
                    'value' => $data['email'],
                    'size' => 30,
                    'maxlength' => 255,
                    'require' => TRUE,
                    'test' => array(
                        'test_user_function' => 'test_mail',
                        'test_error_message' => STR_FORM_E_FATAL_FIELD_SAISIE,
                    ),
                ),

                'password' => array(
                    'item' => 'input',
                    'tpl' => '[(1,20){libelle}(1,40){form}(1,40){legende}]',
                    'libelle' => STR_UTILISATEUR_LIBELLE_PASSWORD,
                    'legende' => STR_UTILISATEUR_LEGENDE_PASSWORD,
                    'value' => $data['password'],
                    'type' => 'password',
                    'size' => 30,
                    'maxlength' => 255,
                    'require' => TRUE,
                ),

               'password_confirmation' => array(
                    'item' => 'input',
                    'tpl' => '[(1,20){libelle}(1,40){form}(1,40){legende}]',
                    'legende' => STR_UTILISATEUR_LEGENDE_PASSWORD_CONFIRMATION,
                    'value' => $data['password_confirmation'],
                    'type' => 'password',
                    'size' => 30,
                    'maxlength' => 255,
                ),
                
                'submit' => array(
                    'item' => 'button',
                    'tpl' => '[(1,20){libelle}(1,40){form}(1,40){legende}]',
                    'value' => STR_FORM_SUBMIT,
                    'type' => 'submit',
                    'class' => 'btn btn-primary',
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

                    $sql = 'SELECT password_reset FROM '.CFG_TABLE_USER.' WHERE email = '.$db->qstr($_POST['email']);        
                    $password_reset=$db->getone($sql);

                    if($password_reset!=$_GET['password_reset'])
                        $form_error['fatal'][] = STR_RESET_E_FATAL_1;
                    else {
                        // On controle le champ password

                        if(!empty($_POST['password']) || !empty($_POST['password_confirmation'])) {

                            if($_POST['password'] != $_POST['password_confirmation'])
                                $form_error['fatal']['password'][] = STR_UTILISATEUR_E_FATAL_4;
                            else {
                                if(preg_match('/[^a-zA-Z0-9]/',$_POST['password']))
                                    $form_error['fatal']['password'][] = STR_UTILISATEUR_E_FATAL_1;

                                if(mb_strlen($_POST['password'],'UTF-8')<4)
                                    $form_error['fatal']['password'][] = STR_UTILISATEUR_E_FATAL_2;
                            }
                        }
                    }

                }

                // Si toujours pas d'erreur bloquante -> Traitement sgbd

                if(empty($form_error['fatal'])) {

                    // Traitement des modifications

                    if($_GET['action'] == 'edit') {

                        $sql = 'UPDATE '.CFG_TABLE_USER.' SET ';
                        $sql.= 'password = '.$db->qstr(hash('sha512', $_POST['password'])).', ';
                        $sql.= 'password_reset = \'\'';
                        $sql.= 'WHERE ';
                        $sql.= 'email = 	'.$db->qstr($_POST['email']);
                    }

                    $db->execute($sql);

                    // Redirection vers la page de login

                    $session->destroy();
                    $url='login.php';
                    header("Location: $url");                    
                }

                // Si erreur bloquante

                else {
                    echo form_message(STR_FORM_E_FATAL, $form_error['fatal']);
                }
            }
    }
}

echo form_parser($form_structure);

//
// Fin du traitement
//

$session->destroy();
require 'stop_php.php';
?>