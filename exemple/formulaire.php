<?php
// Chargement du framework

require 'start_php.php';

// Construction du titre de la page <title>

define('RUBRIQUE_TITRE', STR_EXEMPLE_TITRE.'::'.STR_FORMULAIRE_TITRE);

// Embarquement des scripts coté client <script javascript>

define('LOAD_JAVASCRIPT','autocomplete/jquery.autocomplete.js|datepicker/bootstrap.datepicker.js|datepicker/locales/bootstrap-datepicker.fr.js|multiselect/jquery.multiselect.js|textarea/jquery.textarea.js');

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
                'champ1'    => '1',
                'champ2'    => array('0','2'),
                'champ3'    => 'foo',
                'champ4'    => 'bar',
                'champ5'    => '',
                'champ6'    => '',
                'champ7_1'   => '',
                'champ7_2'   => '',
                'champ8'    => '1',
                'champ9'    => 'Ceci est du text',
                'champ10'   => date('d-m-Y'),
                'champ11'   => '',
                'champ12'   => '',
                'champ13'   => '<span class="label label-info">Lorem ipsum dolor sit amet, consectetur adipisicing elit.</span>',
                'champ14'   => 'Champ textarea compteur',
            );

            // Mise a jour des données selon ce que contient $_POST

            $data = form($data, !empty($edit) ? $edit : array());

            // Construction du Formulaire

            $form_structure = array(
                'champ0' => array(
                    'item' => 'form',
                    'action' => $url,
                    'method' => 'post',
                    'legende' => STR_FORM_LEGENDE,
                ),

                'champ1' => array(
                    'item' => 'radiobox',
                    'tpl' => '[(3){libelle}(4){form}(5){legende}]',
                    'libelle' => STR_FORMULAIRE_LIBELLE_RADIOBOX,
                    'checked' => $data['champ1'],
                    'radiobox' => array(
                        array('value' => '0', 'label' => STR_NON, 'tpl' => ' {form} {label} '),
                        array('value' => '1', 'label' => STR_OUI, 'tpl' => ' {form} {label} '),
                    ),
                    'require' => TRUE,
                ),

                'champ2' => array(
                    'item' => 'checkbox',
                    'tpl' => '[(3){libelle}(4){form}(5){legende}]',
                    'libelle' => STR_FORMULAIRE_LIBELLE_CHECKBOX,
                    'checked' => $data['champ2'],
                    'checkbox' => array(
                        array('value' => '0', 'label' => STR_NON, 'tpl' => ' {form} {label} '),
                        array('value' => '1', 'label' => STR_OUI, 'tpl' => ' {form} {label} '),
                        array('value' => '2', 'label' => STR_MAYBE, 'tpl' => ' {form} {label} '),
                    ),
                    'require' => TRUE,
                ),

                'champ3' => array(
                    'item' => 'input',
                    'tpl' => '[(3){libelle}(3){form}(6){legende}]',
                    'libelle' => STR_FORMULAIRE_LIBELLE_INPUT,
                    'value' => $data['champ3'],
                    'require' => TRUE,
                ),

                'champ4' => array(
                    'item' => 'input',
                    'tpl' => '[(3){libelle}(4){form}(5){legende}]',
                    'libelle' => STR_FORMULAIRE_LIBELLE_INPUT_PASSWORD,
                    'value' => $data['champ4'],
                    'type' => 'password',
                    'require' => TRUE,
                ),

                'champ5' => array(
                    'item' => 'select',
                    'tpl' => '[(3){libelle}(4){form}(5){legende}]',
                    'libelle' => STR_FORMULAIRE_LIBELLE_SELECT,
                    'value' => array(''=>'&nbsp;') + get_theme(),
                    'selected' => $data['champ5'],
                    'multiple' => FALSE,
                    'require' => TRUE,
                ),

                'champ6' => array(
                    'item' => 'select',
                    'tpl' => '[(3){libelle}(4){form}(5){legende}]',
                    'libelle' => STR_FORMULAIRE_LIBELLE_SELECT_MULTIPLE,
                    'value' => get_theme(),
                    'selected' => $data['champ6'],
                    'multiple' => TRUE,
                    'require' => TRUE,
                    'size' => 5,
                ),

                'champ7_on' => array(
                    'item' => 'fieldset',
                    'tpl' => '[(12){libelle}{form}{legende}]',
                    'legende' => STR_FORMULAIRE_FIELDSET_OPTGROUP,
                    'type' => 'on'
                ),

                'champ7_1' => array(
                    'item' => 'select',
                    'tpl' => '[(3){libelle}(4){form}(5){legende}]',
                    'libelle' => STR_FORMULAIRE_LIBELLE_SELECT,
                    'value' => array('<Fruits' => 'Fruits', 'F1'=>'Pomme', 'F2'=>'Poire', 'F3'=>'Orange', 'Fruits>' => '', '<Légumes' => 'Légumes', 'L1'=>'Carotte', 'L2'=>'Haricots', 'L3'=>'Pomme de terre', 'Légumes>' => ''),
                    'selected' => $data['champ7_1'],
                    'multiple' => FALSE,
                    'require' => TRUE,
                    'optgroup' => TRUE,
                ),

                'champ7_2' => array(
                    'item' => 'select',
                    'tpl' => '[(3){libelle}(4){form}(5){legende}]',
                    'libelle' => STR_FORMULAIRE_LIBELLE_SELECT_MULTIPLE,
                    'value' => array('<Fruits' => 'Fruits', 'F1'=>'Pomme', 'F2'=>'Poire', 'F3'=>'Orange', 'Fruits>' => '', '<Légumes' => 'Légumes', 'L1'=>'Carotte', 'L2'=>'Haricots', 'L3'=>'Pomme de terre', 'Légumes>' => ''),
                    'selected' => $data['champ7_2'],
                    'multiple' => TRUE,
                    'require' => TRUE,
                    'size' => 5,
                    'optgroup' => TRUE,
                ),

                'champ7_off' => array(
                    'item' => 'fieldset',
                    'type' => 'off'
                ),

                'champ8' => array(
                    'item' => 'hidden',
                    'value' => $data['champ8'],
                ),

                'champ9' => array(
                    'item' => 'textarea',
                    'tpl' => '[(3){libelle}(4){form}(5){legende}]',
                    'libelle' => STR_FORMULAIRE_LIBELLE_TEXTAREA,
                    'value' => $data['champ9'],
                    'rows' => '10',
                    'cols' => '30',
                    'require' => TRUE,
                    'legende' => '_'.STR_LOREM,
                ),

                'champ10' => array(
                    'item' => 'input',
                    'tpl' => '[(3){libelle}(4){form}(5){legende}]',
                    'libelle' => STR_FORMULAIRE_LIBELLE_CALENDAR,
                    'value' => $data['champ10'],
                    'size' => 16,
                    'prepend' => 'glyphicon glyphicon-calendar',
                ),

                'champ11' => array(
                    'item' => 'upload',
                    'tpl' => '[(3){libelle}(4){form}(5){legende}]',
                    'libelle' => STR_FORMULAIRE_LIBELLE_UPLOAD,
                    'value' => $data['champ11'],
                    'path_file' => CFG_PATH_FILE_UPLOAD,
                    'path_http' => CFG_PATH_HTTP_UPLOAD,
                    'maxsize'=>100*1024,                                            // Taille max (100 Ko)
                    'type'=>array('application/pdf', 'image/pjpeg', 'image/gif'),   // Format(s) accepté(s)
                    'extension'=>array('pdf', 'jpeg', 'jpg', 'gif'),                // Extension(s) acceptée(s)
                    'rename'=>'rename_example',                                     // Renomage (vide par défaut, sinon le nom d'un fonction specifique)
                    'test' => array(
                        'test_user_function' => 'test_upload',
                        'test_error_message' => STR_FORM_E_FATAL_FIELD_SAISIE,
                    ),
                    'require'=>FALSE,
                ),

                'champ12' => array(
                    'item' => 'select',
                    'tpl' => '[(3){libelle}(9){form}]',
                    'libelle' => STR_FORMULAIRE_LIBELLE_MULTISELECT,
                    'value' => get_declared_classes(),
                    'selected' => $data['champ12'],
                    'multiple' => TRUE,
                    'size' => 10,
                    'require' => TRUE,
                ),

                'champ13' => array(
                    'item' => 'info',
                    'tpl' => '[(3){libelle}(9){form}]',
                    'value' => $data['champ13'],
                ),

                'champ14' => array(
                    'item' => 'textarea',
                    'tpl' => '[(3){libelle}(9){form}]',
                    'libelle' => STR_FORMULAIRE_LIBELLE_TEXTAREA.' '.STR_FORMULAIRE_LIBELLE_TEXTAREA_COMPTEUR,
                    'value' => $data['champ14'],
                    'rows' => '10',
                    'cols' => '30',
                    'require' => TRUE,
                ),

                'submit' => array(
                    'item' => 'button',
                    'tpl' => '[(3){libelle}(4){form}(5){legende}]',
                    'value' => STR_FORM_SUBMIT,
                    'class' => 'btn btn-primary',
                    'type' => 'submit',
                ),
            );

            // Application des plugins jQuery

            echo '
                <script type="text/javascript"><!--
                    $(function() {
                        $("#champ3").autocomplete(["Allen","Albert","Alberto","Alladin"], {minChars:3});
                        $("#champ10").datepicker({format: "dd-mm-yyyy", weekStart: 1, language: "fr"});
                        $("#champ12").multiselect({width:250});
                        $("#champ14").counter(128,"'.sprintf(STR_FORM_CARACTERE_LEFT, '128').'");
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

                // if(empty($form_error['fatal'])) {
                //
                // }

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
                echo form_parser($form_structure, (isset($_GET['view'])));
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