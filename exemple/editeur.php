<?php
// Chargement du framework

require 'start_php.php';

// Construction du titre de la page <title>

define('RUBRIQUE_TITRE', STR_EXEMPLE_TITRE.'::'.STR_EDITEUR_TITRE);

// Embarquement des scripts coté client <script javascript>

define('LOAD_JAVASCRIPT','wysihtml5/wysihtml5-0.3.0.min.js|wysihtml5/bootstrap.wysihtml5.js');

// Début de l'affichage

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
                'editor'    => STR_EDITEUR_MESSAGE,
            );

            // Mise a jour des données selon ce que contient $_POST

            $data = form($data, !empty($edit) ? $edit : array());

            // Construction du Formulaire

            $form_structure = array(
                'form' => array(
                    'item' => 'form',
                    'action' => $url,
                    'method' => 'post',
                    'legende' => STR_FORM_LEGENDE,
                ),

                'editor' => array(
                    'item' => 'textarea',
                    'tpl' => '[(3){libelle}(9){form}]',
                    'libelle' => STR_RUBRIQUE_EDITEUR,
                    'value' => $data['editor'],
                    'rows' => '10',
                    'cols' => '80',
                    'require' => TRUE,
                ),

                'submit' => array(
                    'item' => 'button',
                    'tpl' => '[(3){libelle}(9){form}]',
                    'class' => 'btn btn-primary',
                    'value' => STR_FORM_SUBMIT,
                    'type' => 'submit',
                ),
            );

            // Application des plugins jQuery

            echo '
                <script type="text/javascript"><!--
                    $(function() {
                        $("#editor").wysihtml5();
                    });
                // --></script>
            ';

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
                    $form_error['warning']['editor']=$_POST['editor'];
                }

                // Si toujours pas d'erreur bloquante -> Traitement sgbd

                if(empty($form_error['fatal'])) {

                    // Traitement des modifications

                    // if($_GET['action'] == 'edit') {
                    //
                    // }

                    // Traitement des ajouts

                    // elseif($_GET['action'] == 'add') {
                    //
                    // }

                    echo form_message(STR_FORM_E_WARNING, $form_error['warning'], 'info');
                    unset($_GET['action']);
                }

                // Si erreur bloquante

                else {
                    echo form_message(STR_FORM_E_FATAL, $form_error['fatal']);
                    $form_view=form_parser($form_structure);
                    echo $form_view;
                }
            }

            // Si _POST est vide

            else {
                $form_view=form_parser($form_structure);
                echo $form_view;
            }
    }
}

// Si Data

if(empty($_GET['action'])){
    $form_view=form_parser($form_structure);
    echo $form_view;
}

//
// Fin du traitement
//

require 'stop_php.php';
?>