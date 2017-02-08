<?php
// Chargement du framework

require 'start_php.php';

// Construction du titre de la page <title>

define('RUBRIQUE_TITRE', STR_SPECIAL_TITRE);

// Embarquement des scripts et styles additionnels nécessaires

define('LOAD_JAVASCRIPT', '');
define('LOAD_CSS', '');

// Début de l'affichage

// Information en marge

$marge='<p>'.STR_PROFIL_MESSAGE.'</p>';

require 'start_html.php';

//
// Début du traitement
//

// Si Form

if(!empty($_GET['action'])) {

    switch ($_GET['action']) {
        case 'delete' :

            // Rien ici

        break;

        case 'edit' :

            // Rien ici

        default :

            // Construction de l'url <form>

            $url=get_url(basename($_SERVER['PHP_SELF']));

            // Initialisation des valeurs par défaut

            $data = array(
                'nom' => '',
                'prenom' => '',
                'password' => '',
                'ip_1' => '',
                'ip_2' => '',
                'ip_3' => '',
                'ip_4' => '',
            );

            // Mise a jour des données selon ce que contient $_POST

            $data = form($data, !empty($edit) ? $edit : array());

            // Construction du Formulaire

            $form_structure = array(
                'form' => array(
                    'item' => 'form',
                    'action' => $url,
                    'legende' => STR_FORM_LEGENDE,
                ),

                'nom' => array(
                    'item' => 'input',
                    'tpl' => '[(3){libelle}(4){form}(5){legende}]',
                    'libelle' => STR_UTILISATEUR_LIBELLE_NOM,
                    'value' => $data['nom'],
                    'size' => 30,
                    'require' => FALSE,
                    'js' => 'style="text-transform: uppercase;" ',
                ),

                'prenom' => array(
                    'item' => 'input',
                    'tpl' => '[(3){libelle}(4){form}(5){legende}]',
                    'libelle' => STR_UTILISATEUR_LIBELLE_PRENOM,
                    'value' => $data['prenom'],
                    'size' => 30,
                    'require' => FALSE,
                ),

                'ip_1' => array(
                    'item' => 'hidden',
                    'value' => $data['ip_1']
                ),

                'ip_2' => array(
                    'item' => 'hidden',
                    'value' => $data['ip_2']
                ),

                'ip_3' => array(
                    'item' => 'hidden',
                    'value' => $data['ip_3']
                ),

                'ip_4' => array(
                    'item' => 'hidden',
                    'value' => $data['ip_4']
                ),

                'password' => array(
                    'item' => 'input',
                    'tpl' => '[(3){libelle}(4){form}(5){legende}]',
                    'libelle' => STR_UTILISATEUR_LIBELLE_PASSWORD,
                    'value' => $data['password'],
                    'type' => 'password',
                    'size' => 30,
                    'require' => TRUE,
                ),

                'submit' => array(
                    'item' => 'button',
                    'tpl' => '[(3){libelle}(4){form}(5){legende}]',
                    'class' => 'btn btn-primary',
                    'value' => STR_FORM_SUBMIT,
                    'type' => 'submit',
                ),
            );

            //
            // Premier exemple de règle de gestion spéciale
            // Ici on masque l'option de choix du password si le user_id vaut 1
            //

            if($session_user_id==1) {
                unset($form_structure['password']);
            }

            //
            // Second exemple de règle de gestion spéciale
            // Si le champ nom est saisie, le champ prénom devient requis
            //

            if(isset($_POST['nom']) && $_POST['nom']!='') {
                $form_structure['prenom']['require']=TRUE;
            }

            //
            // Troisième exemple de règle de gestion spéciale
            // Si le champ nom est saisie & le champ prénom sont saisis, le champ ip apparait
            //

            if(isset($_POST['nom']) && $_POST['nom']!='' && isset($_POST['prenom']) && $_POST['prenom']!='') {
                for($i=1; $i<=4; $i++) {
                    $form_structure['ip_'.$i]=array(
                        'item' => 'input',
                        'tpl' => '(1){form}',
                        'libelle' => STR_SPECIAL_IP_LIBELLE,
                        'value' => $data['ip_'.$i],
                        'maxlength' => 3,
                        'require' => TRUE,
                    );
                }

                $form_structure['ip_1']['tpl']='[(3){libelle}(1){form}';
                $form_structure['ip_4']['tpl']='(1){form}(5){legende}]';
                $form_structure['ip_4']['legende']='_'.STR_SPECIAL_IP_LEGENDE;
            }
            else {
                for($i=1; $i<=4; $i++) {
                    if(isset($_POST['ip_'.$i]))
                        unset($_POST['ip_'.$i]);
                    unset($form_structure['ip_'.$i]);
                }
            }

            // print_rh($form_structure);
            // print_rh($_POST);

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

            if(!empty($form_error['fatal']['ip_1'])) {
                unset($form_error['fatal']['ip_2']);
                unset($form_error['fatal']['ip_3']);
                unset($form_error['fatal']['ip_4']);
            }
            // Si _POST n'est pas vide

            if(!empty($_POST)) {

                // Si pas d'erreur bloquante -> Traitement complémentaire

                if(empty($form_error['fatal'])) {

                    // Traitement
                    if(isset($_POST['ip_1']) && isset($_POST['ip_2']) && isset($_POST['ip_3']) && isset($_POST['ip_4'])) {
                        $ip=$_POST['ip_1'].'.'.$_POST['ip_2'].'.'.$_POST['ip_3'].'.'.$_POST['ip_4'];
                        if(!ip2long($ip)) {
                            $form_error['fatal']['ip_1']=STR_SPECIAL_IP_ERREUR;
                        }
                    }
                }

                // Si toujours pas d'erreur bloquante -> Traitement sgbd

                if(empty($form_error['fatal'])) {
                // Traitement
                }

                // Si erreur bloquante

                else {
                    echo form_message(STR_FORM_E_FATAL, $form_error['fatal']);
                }
            }
    }
}

echo form_parser($form_structure, (isset($_GET['view'])));

//
// Fin du traitement
//

require 'stop_php.php';
?>