<?php
// Chargement du framework

require 'start_php.php';

// Construction du titre de la page <title>

if(!empty($_GET['action']) && $_GET['action']=='add')
    define('RUBRIQUE_TITRE', STR_ONGLET_TITRE.' > '.STR_ONGLET_TITRE_ETAPE_1);
elseif(!empty($_GET['action']) && $_GET['action']=='edit')  {
    if(empty($_POST)) {
        $sql = 'SELECT nom, prenom FROM '.CFG_TABLE_USER.' WHERE user_id = '.$db->qstr($_GET['user_id']);
        $edit = $db->getrow($sql);
        define('RUBRIQUE_TITRE', STR_ONGLET_TITRE.' > '.STR_ONGLET_TITRE_ETAPE_1.' > '.$edit['prenom'].' '.$edit['nom']);
    }
    else
        define('RUBRIQUE_TITRE', STR_ONGLET_TITRE.' > '.STR_ONGLET_TITRE_ETAPE_1.' > '.$_POST['prenom'].' '.$_POST['nom']);
}
else
    define('RUBRIQUE_TITRE', STR_ONGLET_TITRE.' > '.STR_ONGLET_SOUS_TITRE);

// Embarquement des scripts coté client <script javascript>

if(!empty($_GET['action']) && ($_GET['action']=='add' || $_GET['action']=='edit'))
    define('LOAD_JAVASCRIPT','autocomplete/jquery.autocomplete.js|multiselect/jquery.multiselect.js');
else
    define('LOAD_JAVASCRIPT','backoffice/jquery.backoffice.js');

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
        case 'del' :

            $sql = 'DELETE FROM '.CFG_TABLE_USER.' WHERE user_id = '.$db->qstr($_GET['user_id']);
            $db->execute($sql);

            unset($_GET['action']);
            unset($_GET['id']);

            echo message(STR_FORM_E_WARNING, STR_UTILISATEUR_E_INFO_3, 'success', 'session');

            break;

        case 'view' :
        case 'edit' :

            if(empty($_POST))
            {
                $sql = 'SELECT actif, acl, poste, nom, prenom, email, langue FROM '.CFG_TABLE_USER.' ';
                $sql.= 'WHERE ';
                $sql.= 'user_id = '.$db->qstr($_GET['user_id']);

                $edit = $db->getrow($sql);

                $edit['password']='';
                $edit['password_confirmation']='';
            }

        default :

            // Construction de l'url <form>

            $url=get_url(basename($_SERVER['PHP_SELF']));

            // Initialisation des valeurs par défaut

            $data = array(
                'actif' => '1',
                'acl' => '',
                'poste' => '',
                'nom' => '',
                'prenom' => '',
                'username' => '',
                'password' => '',
                'password_confirmation' => '',
                'email' => '',
            );

            // Mise a jour des données selon ce que contient $_POST

            $data = form($data, !empty($edit) ? $edit : array());

            // Construction du Formulaire

            $form_structure = array(
                'form' => array(
                    'item' => 'form',
                    'action' => $url
                ),

                'actif' => array(
                    'item' => 'radiobox',
                    'tpl' => '[(3){libelle}(4){form}(5){legende}]',
                    'libelle' => STR_UTILISATEUR_LIBELLE_ACTIF,
                    'checked' => $data['actif'],
                    'radiobox' => array(
                        array('value' => '0', 'label' => STR_NON, 'tpl' => ' {form} {label} '),
                        array('value' => '1', 'label' => STR_OUI, 'tpl' => ' {form} {label} '),
                    )
                ),

               'acl' => array(
                    'item' => 'radiobox',
                    'tpl' => '[(3){libelle}(4){form}(5){legende}]',
                    'libelle' => STR_UTILISATEUR_LIBELLE_ACL,
                    'checked' => $data['acl'],
                    'radiobox' => array(
                        array('value' => 'admin', 'label' => STR_UTILISATEUR_LIBELLE_ACL_ADMIN, 'tpl' => ' {form} {label} '),
                        array('value' => 'user',  'label' => STR_UTILISATEUR_LIBELLE_ACL_USER,  'tpl' => ' {form} {label} '),
                        array('value' => 'guest', 'label' => STR_UTILISATEUR_LIBELLE_ACL_GUEST, 'tpl' => ' {form} {label} '),
                    ),
                    'require' => TRUE,
                ),

                'poste' => array(
                    'item' => 'input',
                    'tpl' => '[(3){libelle}(3){form}(6){legende}]',
                    'libelle' => STR_UTILISATEUR_LIBELLE_POSTE,
                    'value' => $data['poste'],
                    'type' => 'text',
                    'size' => 30,
                    'maxlength' => 255,
                    'require' => TRUE,
                ),

                'nom' => array(
                    'item' => 'input',
                    'tpl' => '[(3){libelle}(4){form}(5){legende}]',
                    'libelle' => STR_UTILISATEUR_LIBELLE_NOM,
                    'legende' =>'',
                    'value' => $data['nom'],
                    'size' => 30,
                    'maxlength' => 255,
                    'require' => TRUE,
                    'js' => 'style="text-transform: uppercase;" ',
                ),

                'prenom' => array(
                    'item' => 'input',
                    'tpl' => '[(3){libelle}(4){form}(5){legende}]',
                    'libelle' => STR_UTILISATEUR_LIBELLE_PRENOM,
                    'value' => $data['prenom'],
                    'size' => 30,
                    'maxlength' => 255,
                    'require' => TRUE,
                ),

                'email' => array(
                    'item' => 'input',
                    'tpl' => '[(3){libelle}(4){form}(5){legende}]',
                    'libelle' => STR_UTILISATEUR_LIBELLE_EMAIL,
                    'value' => $data['email'],
                    'maxlength' => 255,
                    'prepend' => 'glyphicon glyphicon-envelope',
                    'placeholder' => STR_LOGIN_LIBELLE_EMAIL_PLACEHOLDER,
                    'require' => TRUE,
                    'test' => array(
                        'test_user_function' => 'test_mail',
                        'test_error_message' => STR_FORM_E_FATAL_FIELD_SAISIE,
                    ),
                ),

               'password' => array(
                    'item' => 'input',
                    'tpl' => '[(3){libelle}(4){form}(5){legende}]',
                    'libelle' => STR_UTILISATEUR_LIBELLE_PASSWORD,
                    'legende' => $_GET['action'] == 'edit' ? STR_UTILISATEUR_LEGENDE_PASSWORD_MODIF : STR_UTILISATEUR_LEGENDE_PASSWORD,
                    'value' => $data['password'],
                    'type' => 'password',
                    'maxlength' => 255,
                    'prepend' => 'glyphicon glyphicon-lock',
                    'placeholder' => STR_LOGIN_LIBELLE_PASSWORD_PLACEHOLDER,
                    'require' => $_GET['action'] == 'edit' ? FALSE : TRUE,
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
                    'js' => 'onclick="window.location=\''.$session->url(basename($_SERVER['PHP_SELF'])).'\'"',
                ),
            );

            // Application des plugins jQuery

            echo '
                <script type="text/javascript"><!--
                    $(function() {
                        $("#poste").autocomplete(["'.implode('","', get_poste()).'"], {minChars:3});
                        $("#prenom").autocomplete("../utilisateur/prenom.php", {minChars:3});
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

                    if(!empty($_GET['user_id']))
                        $sql = 'SELECT user_id FROM '.CFG_TABLE_USER.' WHERE user_id <> '.$db->qstr($_GET['user_id']).' AND email = '.$db->qstr($_POST['email']);
                    else
                        $sql = 'SELECT user_id FROM '.CFG_TABLE_USER.' WHERE email = '.$db->qstr($_POST['email']);

                    if($db->getone($sql))
                        $form_error['fatal'][] = STR_UTILISATEUR_E_FATAL_3;

                    // On controle le champ password

                    if(!empty($_POST['password']) || !empty($_POST['password_confirmation'])) {
                        if($_POST['password'] != $_POST['password_confirmation'])
                            $form_error['fatal']['password'][] = STR_UTILISATEUR_E_FATAL_4;
                    }

                    if($_POST['prenom'] == $_POST['password'])
                        $form_error['warning']['prenom, password'] = STR_UTILISATEUR_E_WARNING_1;
                }

                // Si toujours pas d'erreur bloquante -> Traitement sgbd

                if(empty($form_error['fatal'])) {

                    // Construction de la requête avec les éléments en commun

                    $sql = 'actif =     '.$db->qstr($_POST['actif']).', ';
                    $sql.= 'acl =       '.$db->qstr($_POST['acl']).', ';
                    $sql.= 'poste =     '.$db->qstr($_POST['poste']).', ';
                    $sql.= 'nom =       '.$db->qstr(mb_strtoupper(delete_accent($_POST['nom'],'UTF-8'))).', ';
                    $sql.= 'prenom =    '.$db->qstr($_POST['prenom']).', ';

                    if(!empty($_POST['password'])) {
                        $sql.= 'password = '.$db->qstr(hash('sha512', $_POST['password'])).', ';
                    }

                    $sql.= 'email =     '.$db->qstr($_POST['email']);

                    // Traitement des modifications

                    if($_GET['action'] == 'edit') {
                        $sql = 'UPDATE '.CFG_TABLE_USER.' SET '.$sql.' ';
                        $sql.= 'WHERE ';
                        $sql.= 'user_id = 	'.$db->qstr($_GET['user_id']);

                        $db->execute($sql);
                    }

                    // Traitement des ajouts

                    elseif($_GET['action'] == 'add') {
                        $sql = 'INSERT INTO '.CFG_TABLE_USER.' SET '.$sql.', ';
                        $sql.= 'couleur =   '.$db->qstr('').', ';
                        $sql.= 'theme =     '.$db->qstr(CFG_THEME_DEFAULT).', ';
                        $sql.= 'langue =    '.$db->qstr('fr').', ';
                        $sql.= 'last =      '.$db->qstr(date('Y-m-d H:i:s'));

                        $db->execute($sql);

                        // Gestion des onglets

                        $id=$db->insert_id(CFG_TABLE_USER, 'user_id');
                        $_GET['user_id']=$id;

                        $form_structure['form']['action']=str_replace('action=add', 'action=edit', $form_structure['form']['action']);
                        $form_structure['form']['action'].='&user_id='.$id;
                    }

                    // Ici on reste sur le formulaire

                    if(!empty($_GET['user_id'])) {
                        echo(get_menu_local(get_menu_acl($navigation_onglet, $cfg_profil['acl']), 'user_id='.$_GET['user_id']));

                        if(!empty($form_error['warning'])) {
                            echo form_message(STR_FORM_E_WARNING, $form_error['warning'], 'warning', 'session');
                        }
                        echo form_message(STR_FORM_E_WARNING, STR_UTILISATEUR_E_INFO_2, 'success', 'session');

                        echo form_parser($form_structure);
                    }
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

                echo form_parser($form_structure, (isset($_GET['action']) && $_GET['action']=='view'));
            }
    }
}

// Backoffice
// Si pas d'action en GET

if(empty($_GET['action'])){

    $structure= array(
        'context' => array(
            'name' => 'onglet',
        ),
        'script' => array(
            'name'=> basename($_SERVER['PHP_SELF']),
            'action'=> array('label' => 'action'),
            'id' =>    array('label' => 'user_id', 'value' => 'user_id'),
        ),
        'config' => array(
            'css' => 'backoffice',
            'action'=> array('empty'=>TRUE, 'hide'=>FALSE, 'width'=>'25%'),
            'total' => TRUE,
            'total_string' => STR_UTILISATEUR_LIBELLE_NOMBRE,
            'help' => array(
                'outil' =>      STR_BACKOFFICE_HELP_OUTIL,
                'filtre' =>     STR_BACKOFFICE_HELP_FILTRE,
                'data' =>       STR_BACKOFFICE_HELP_DATA,
                'order' =>      STR_BACKOFFICE_HELP_ORDER,
                'action' =>     STR_BACKOFFICE_HELP_ACTION,
                'navigation' => STR_BACKOFFICE_HELP_NAVIGATION,
                'export' =>     STR_BACKOFFICE_HELP_EXPORT,
            ),
            'type' => 'print',
            'logical' => 'AND'
        ),
        'filtre' => array(
            array('field'=>'nom',       'label'=>STR_UTILISATEUR_LIBELLE_NOM,       'type'=>'input',    'like'=>TRUE),
            array('field'=>'prenom',    'label'=>STR_UTILISATEUR_LIBELLE_PRENOM,    'type'=>'input',    'like'=>TRUE),
            array('field'=>'theme',     'label'=>STR_UTILISATEUR_LIBELLE_THEME,     'type'=>'alpha',    'like'=>TRUE, 'logical' => 'OR'),
            array('field'=>'actif',     'label'=>STR_UTILISATEUR_LIBELLE_STATUS,    'type'=>'select',   'value'=>array(''=>STR_UTILISATEUR_LIBELLE_TOUS, '1'=>STR_UTILISATEUR_LIBELLE_ACTIF, '0'=>STR_UTILISATEUR_LIBELLE_INACTIF), 'like'=>FALSE),
        ),
        'requete' => array(
            'select'=>'SELECT user_id, actif, nom, prenom, theme, last FROM '.CFG_TABLE_USER.' ORDER by nom, prenom ASC',
            'result_user_function'=> 'result_onglet',
        ),
        'data' => array(
            array('field'=>'theme',     'label'=>STR_UTILISATEUR_LIBELLE_THEME,     'order'=>TRUE, 'export'=>FALSE, 'rowspan'=>TRUE),
            array('field'=>'last',      'label'=>STR_UTILISATEUR_LIBELLE_DATE,      'order'=>TRUE, 'export'=>TRUE,  'rowspan'=>TRUE),
            array('field'=>'nom',       'label'=>STR_UTILISATEUR_LIBELLE_NOM,       'order'=>TRUE, 'export'=>TRUE,  'rowspan'=>TRUE),
            array('field'=>'prenom',    'label'=>STR_UTILISATEUR_LIBELLE_PRENOM,    'order'=>TRUE, 'export'=>TRUE,  'rowspan'=>TRUE),
        ),
        'action' => array(
            array('field'=>'add',               'label'=>STR_FORM_ADD,          'type'=>'outside',	'format' => 'icon', 'src'=>'glyphicon glyphicon-pencil'),
            array('field'=>'add',               'label'=>STR_FORM_ADD,          'type'=>'global',	'format' => 'icon', 'src'=>'glyphicon glyphicon-pencil'),
            array('field'=>'edit',              'label'=>STR_FORM_EDIT,         'type'=>'local',    'format' => 'text'),
            array('field'=>'del',               'label'=>STR_FORM_DELETE,       'type'=>'local',    'format' => 'text', 'js'=>'onclick="return confirm(\''.STR_FORM_DELETE_CONFIRMATION.' %s %s\');"', 'on'=>array('prenom', 'nom')),
        ),
        'navigation' => array(
            'page' => 10,
            'item' => 10,
            'choix_item' => array(5,10,15,20),
        ),
        'export' => array(
            'format'    => array('pdf', 'excel'),    // pdf ou excel
        ),
    );

    backoffice($structure);
}

//
// Fin du traitement
//

require 'stop_php.php';

/*
 * Fonction result_onglet(&$data, &$action)
 * -----
 * Exemple de call user func défini dans la structure backoffice et appelé par la fonction backoffice()
 * Par exemple, entre autre, on reformate le champ date au format francais (elles sont au format ISO en base)
 * -----
 * @param   array       $data                   le flux des données remontées par le SELECT
 * @param   array       $action                 les actions
 * -----
 * @return  array       $data                   le flux des données éventuellement modifié
 * -----
 * $Author: armel $
 * $Copyright: GLOBALIS media systems $
 */

function result_onglet(&$data, &$action) {

    while(list($k,)=each($data)) {
        $data[$k]['last']=date_iso_to($data[$k]['last']);
    }
}
?>