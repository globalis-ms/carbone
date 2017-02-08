<?php
// Chargement du framework

require 'start_php.php';

// Construction du titre de la page <title>

define('RUBRIQUE_TITRE',  STR_EXEMPLE_TITRE.'::'.STR_FILE_TITRE);

// Embarquement des scripts et styles additionnels nécessaires

if (!empty($_GET['action']) && ($_GET['action']=='add' || $_GET['action']=='edit')) {
    define('LOAD_JAVASCRIPT', '');
    define('LOAD_CSS', '');
} else {
    define('LOAD_JAVASCRIPT', 'backoffice/jquery.backoffice.js');
    define('LOAD_CSS', '');
}

// Attention :
// Cas particulier de la brique UPLOAD
// On gère ici les suppressions de fichiers

$tmp_upload=array ('upload_1', 'upload_2');

foreach($tmp_upload as $v) {
    if(!empty($_POST[$v]) && strstr($_POST[$v], 'delete|')) {
        $filename= substr($_POST[$v],7);
        $sql = 'UPDATE '.CFG_TABLE_DATA.' SET '.$v.' = \'\' WHERE data_id = '.$db->qstr($_GET['id']);
        if($v=='upload_2')  // Le chemin de stockage étant différent de celui par défaut, il faut le préciser
            del_upload($filename, $sql, CFG_PATH_FILE_UPLOAD.'/foo');
        else
            del_upload($filename, $sql);
        $_POST[$v]='';
    }
}

// Début de l'affichage

require 'start_html.php';

//
// Début du traitement
//

// Formulaire
// Si action en GET

if(!empty($_GET['action'])) {

    switch ($_GET['action']) {
        case 'delete' :

            $sql = 'SELECT upload_1, upload_2 FROM '.CFG_TABLE_DATA.' WHERE data_id = '.$db->qstr($_GET['id']);
            $recordset = $db->execute($sql);

            $row = $db->getrow($sql);

            del_upload($row['upload_1']);
            del_upload($row['upload_2'], '', CFG_PATH_FILE_UPLOAD.'/foo'); // Le chemin de stockage étant différent de celui par défaut, il faut le préciser

            $sql = 'DELETE FROM '.CFG_TABLE_DATA.' WHERE data_id = '.$db->qstr($_GET['id']);
            $db->execute($sql);

            unset($_GET['action']);
            unset($_GET['id']);

        break;

        case 'view' :
        case 'edit' :

            if(empty($_POST)) {
                $sql = 'SELECT online, titre AS _titre, upload_1, upload_2, url FROM '.CFG_TABLE_DATA.' ';
                $sql.= 'WHERE ';
                $sql.= 'data_id = '.$db->qstr($_GET['id']);

                $edit = $db->getrow($sql);
            }

        default :

            // Construction de l'url <form>

            $url=get_url(basename($_SERVER['PHP_SELF']));

            // Initialisation des valeurs par défaut

            $data = array(
                'online' => '1',
                '_titre' => '',
                'upload_1' => '',
                'upload_2' => '',
                'url' => '',
            );

            // Mise a jour des données selon ce que contient $_POST

            $data = form($data, !empty($edit) ? $edit : array());

            // Construction du Formulaire

            $form_structure = array(
                'form' => array(
                    'item' => 'form',
                    'action' => $url,
                ),

                'online' => array(
                    'item' => 'radiobox',
                    'tpl' => '[(3){libelle}(4){form}(5){legende}]',
                    'libelle' => STR_FILE_LIBELLE_ONLINE,
                    'checked' => $data['online'],
                    'radiobox' => array(
                        array('value' => '0', 'label' => STR_NON, 'tpl' => ' {form} {label} '),
                        array('value' => '1', 'label' => STR_OUI, 'tpl' => ' {form} {label} '),
                    )
                ),

                '_titre' => array(
                    'item' => 'textarea',
                    'tpl' => '[(3){libelle}(9){form}]',
                    'libelle' => STR_FILE_LIBELLE_TITRE,
                    'value' => $data['_titre'],
                    'rows' => '4',
                    'cols' => '60',
                    'require' => TRUE,
                ),

                'upload_1' => array(
                    'item' => 'upload',
                    'tpl' => '[(3){libelle}(9){form}]',
                    'libelle' => STR_FILE_LIBELLE_FICHIER.' 1',
                    'value' => $data['upload_1'],
                    'path_file' => CFG_PATH_FILE_UPLOAD,
                    'path_http' => CFG_PATH_HTTP_UPLOAD,
                    'maxsize'=>200*1024,                                                            // Taille max (100 Ko)
                    'type'=>array('application/pdf', 'image/jpeg', 'image/pjpeg', 'image/gif'),     // Format(s) accepté(s)
                    'extension'=>array('pdf', 'jpeg', 'jpg', 'gif'),                                // Extension(s) acceptée(s)
                    'rename'=>'rename_example',                                                     // Renomage (vide par défaut, sinon le nom d'un fonction specifique)
                    'test' => array(
                        'test_user_function' => 'test_upload',
                        'test_error_message' => STR_FORM_E_FATAL_FIELD_SAISIE,
                    ),
                    'require'=>TRUE,
                ),

                'upload_2' => array(
                    'item' => 'upload',
                    'tpl' => '[(3){libelle}(9){form}]',
                    'libelle' => STR_FILE_LIBELLE_FICHIER.' 2',
                    'value' => $data['upload_2'],
                    'path_file' => CFG_PATH_FILE_UPLOAD.'/foo',
                    'path_http' => CFG_PATH_HTTP_UPLOAD.'/foo',
                    'maxsize'=>100*1024,                                                            // Taille max (100 Ko)
                    'test' => array(
                        'test_user_function' => 'test_upload',
                        'test_error_message' => STR_FORM_E_FATAL_FIELD_SAISIE,
                    ),
                    'require'=>FALSE,
                ),

                'url' => array(
                    'item' => 'input',
                    'tpl' => '[(3){libelle}(9){form}]',
                    'libelle' => STR_FILE_LIBELLE_URL,
                    'value' => $data['url'],
                    'maxlength' => 200,
                    'test' => array(
                        'test_user_function' => 'test_url',
                        'test_error_message' => STR_FORM_E_FATAL_FIELD_SAISIE,
                    ),
                ),

                'submit' => array(
                    'item' => 'button',
                    'tpl' => '[(3){libelle}(9){form}]',
                    'value' => STR_OK,
                    'class' => 'btn btn-primary',
                    'type' => 'submit',
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
                    add_upload('upload_1');
                    add_upload('upload_2');
                }

                // Si toujours pas d'erreur bloquante -> Traitement sgbd

                if(empty($form_error['fatal'])) {

                   // Construction de la requête avec les éléments en commun

                    $sql = 'online =    '.$_POST['online'].', ';
                    $sql.= 'titre =     '.$db->qstr($_POST['_titre']).', ';
                    $sql.= 'upload_1 =  '.$db->qstr($_POST['upload_1']).', ';
                    $sql.= 'upload_2 =  '.$db->qstr($_POST['upload_2']).', ';
                    $sql.= 'url =       '.$db->qstr($_POST['url']);

                    // Traitement des modifications

                    if($_GET['action'] == 'edit') {
                        $sql = 'UPDATE '.CFG_TABLE_DATA.' SET '.$sql.' ';
                        $sql.= 'WHERE ';
                        $sql.= 'data_id = 	'.$db->qstr($_GET['id']);
                    }

                    // Traitement des ajouts

                    elseif($_GET['action'] == 'add') {
                        $sql = 'INSERT INTO '.CFG_TABLE_DATA.' SET '.$sql;
                    }

                    $db->execute($sql);

                    unset($_GET['action']);
                    unset($_GET['id']);
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
                echo form_parser($form_structure, (isset($_GET['action']) && $_GET['action']=='view'));
                //echo $form_view;
            }
    }
}

// Backoffice
// Si pas d'action en GET

if(empty($_GET['action'])){
    $structure= array(
        'context' => array(
            'name'      => 'exemple_file',
        ),
        'script' => array(
            'name'      => basename($_SERVER['PHP_SELF']),
            'action'    => array('label' => 'action'),
            'id'        => array('label' => 'id', 'value' => 'data_id'),
        ),
        'config' => array(
            'css'       => 'backoffice',
            'action'    => array('empty' => TRUE, 'hide' => FALSE, 'width' => '30%'),
            'total'     => TRUE,
        ),
        'filtre' => array(
            array('field' => 'titre', 'label' => STR_FILE_LIBELLE_TITRE, 'type' => 'input', 'like' => TRUE),
        ),
        'requete' => array(
            'select' => 'SELECT data_id, titre AS _titre FROM '.CFG_TABLE_DATA,
        ),
        'data' => array(
            array('field' => '_titre', 'label' => STR_FILE_LIBELLE_TITRE, 'order' => TRUE),
        ),
        'action' => array(
            array('field' => 'add',               'label' => STR_FORM_ADD,      'type' => 'outside', 'format' => 'button'),
            array('field' => 'edit',              'label' => STR_FORM_EDIT,     'type' => 'local', 'format' => 'button'),
            array('field' => 'delete',            'label' => STR_FORM_DELETE,   'type' => 'local', 'format' => 'button'),
            array('field' => 'view',              'label' => STR_FORM_VIEW,     'type' => 'local', 'format' => 'button'),
        ),
        'navigation' => array(
            'page'          => 10,
            'item'          => 10,
            'choix_item'    => array(5,10,15,20),
        ),
    );

    backoffice($structure);
}

//
// Fin du traitement
//

require 'stop_php.php';
?>
