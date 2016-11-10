<?php
// Chargement du framework

require 'start_php.php';

// Construction du titre de la page <title>

define('RUBRIQUE_TITRE', STR_UTILISATEUR_TITRE);

// Embarquement des scripts coté client <script javascript>

if(!empty($_GET['action']) && ($_GET['action']=='add' || $_GET['action']=='edit'))
    define('LOAD_JAVASCRIPT','autocomplete/jquery.autocomplete.js|multiselect/jquery.multiselect.js');
else
    define('LOAD_JAVASCRIPT','backoffice/jquery.backoffice.js|notice/jquery.notice.js');

// Début de l'affichage

require 'start_html.php';

//
// Début du traitement
//

// Formulaire

//
// Si le mode token n'est pas utilisé dans la brique backoffice
// if(!empty($_GET['action']))
// Sinon...
//
// Si action en GET

if(!empty($_GET['action']))
//if(!empty($_GET['action']) && !empty($_GET['token']))
{

    switch ($_GET['action'])
    {
        case 'del_group' :

            foreach($_POST['action_group'] as $v) {
                $sql = 'DELETE FROM '.CFG_TABLE_USER.' WHERE user_id = '.$db->qstr($v);
                $db->execute($sql);
            }

            unset($_GET['action']);
            unset($_POST);

            echo message(STR_FORM_E_WARNING, STR_UTILISATEUR_E_INFO_3, 'success', 'session');

            break;

        case 'active_group' :

            foreach($_POST['action_group'] as $v) {
                $sql = 'UPDATE '.CFG_TABLE_USER.' SET actif = \'1\' WHERE user_id = '.$db->qstr($v);
                $db->execute($sql);
            }

            unset($_GET['action']);
            unset($_POST);

            break;

        case 'desactive_group' :

            foreach($_POST['action_group'] as $v) {
                $sql = 'UPDATE '.CFG_TABLE_USER.' SET actif = \'0\' WHERE user_id = '.$db->qstr($v);
                $db->execute($sql);
            }

            unset($_GET['action']);
            unset($_POST);

            break;

        case 'del' :

            $sql = 'DELETE FROM '.CFG_TABLE_USER.' WHERE user_id = '.$db->qstr($_GET['id']);
            $db->execute($sql);

            unset($_GET['action']);
            unset($_GET['id']);

            echo message(STR_FORM_E_WARNING, STR_UTILISATEUR_E_INFO_3, 'success', 'session');

            break;

        case 'active' :

            $sql = 'UPDATE '.CFG_TABLE_USER.' SET actif = \'1\' WHERE user_id = '.$db->qstr($_GET['id']);
            $db->execute($sql);

            unset($_GET['action']);
            unset($_GET['id']);

            break;

        case 'desactive' :

            $sql = 'UPDATE '.CFG_TABLE_USER.' SET actif = \'0\' WHERE user_id = '.$db->qstr($_GET['id']);
            $db->execute($sql);

            unset($_GET['action']);
            unset($_GET['id']);

            break;

        case 'view' :
        case 'edit' :

            if(empty($_POST))
            {
                $sql = 'SELECT actif, acl, poste, nom, prenom, email, couleur, langue, theme FROM '.CFG_TABLE_USER.' ';
                $sql.= 'WHERE ';
                $sql.= 'user_id = '.$db->qstr($_GET['id']);

                $edit = $db->getrow($sql);

                $edit['password']='';
                $edit['password_confirmation']='';
                $edit['couleur']=explode('|', $edit['couleur']);
            }

        default :

            // Construction de l'url <form>

            $url=get_url(basename($_SERVER['PHP_SELF']));

            // Initialisation des valeurs par défaut

            $data = array(
                'actif' => '',
                'acl' => '',
                'poste' => '',
                'nom' => '',
                'prenom' => '',
                'username' => '',
                'password' => '',
                'password_confirmation' => '',
                'email' => '',
                'couleur' => '',
                'theme' => '',
            );

            // Mise a jour des données selon ce que contient $_POST

            $data = form($data, !empty($edit) ? $edit : array());

            // Construction du Formulaire

            $form_structure = array(
                'form' => array(
                    'item' => 'form',
                    'action' => $url,
                    'legende' => STR_FORM_LEGENDE.' / '.STR_UTILISATEUR_EXPLICATION_1,
                ),

                'actif' => array(
                    'item' => 'radiobox',
                    'tpl' => '[(3){libelle}(4){form}(5){legende}]',
                    'libelle' => STR_UTILISATEUR_LIBELLE_ACTIF,
                    'checked' => $data['actif'],
                    'radiobox' => array(
                        array('value' => '0', 'label' => STR_NON, 'tpl' => ' {form} {label} '),
                        array('value' => '1', 'label' => STR_OUI, 'tpl' => ' {form} {label} '),
                    ),
                    'require' => TRUE,
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
                    'format' => 'vertical',
                    'require' => TRUE,
                ),

                'poste' => array(
                    'item' => 'input',
                    'tpl' => '[(3){libelle}(3){form}(6){legende}]',
                    'libelle' => STR_UTILISATEUR_LIBELLE_POSTE,
                    'value' => $data['poste'],
                    'type' => 'text',
                    'maxlength' => 255,
                    'require' => TRUE,
                ),

                'nom' => array(
                    'item' => 'input',
                    'tpl' => '[(3){libelle}(4){form}(5){legende}]',
                    'libelle' => STR_UTILISATEUR_LIBELLE_NOM,
                    'legende' =>'',
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

                'couleur' => array(
                    'item' => 'select',
                    'tpl' => '[(3){libelle}(9){form}]',
                    'libelle' => STR_UTILISATEUR_LIBELLE_COULEUR,
                    'value' => get_couleur(),
                    'selected' => $data['couleur'],
                    'multiple' => TRUE,
                    'size' => 6,
                    'require' => TRUE,
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
                    'type' => 'submit',
                    'class' => 'btn btn-primary',
                ),

                'retour' => array(
                    'item' => 'button',
                    'tpl' => ' {form}]',
                    'value' => STR_RETOUR,
                    'type' => 'button',
                    'js' => 'onclick="window.location=\''.$session->url(CFG_PATH_HTTP.'/utilisateur/simple.php').'\'"',
                ),
            );

            // Application des plugins jQuery

            echo '
                <script type="text/javascript"><!--
                    $(function() {
                        $("#poste").autocomplete(["'.implode('","', get_poste()).'"], {max:20, scroll:false});
                        $("#prenom").autocomplete("prenom.php", {minChars:2, max:20, scroll:false});
                        $("#couleur").multiselect({width:250});
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
                
                    if(!empty($_GET['id']))
                        $sql = 'SELECT user_id FROM '.CFG_TABLE_USER.' WHERE user_id <> '.$db->qstr($_GET['id']).' AND email = '.$db->qstr($_POST['email']);
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

                // Dans cette exemple, on retourne sur la brique backoffice, si pas de warning, que nous soyons en action edit ou add
                // Ce comportement permet d'éviter les F5 lors d'ajout, par exemple.
                // On utilise la fonction redirect() pour effectuer la redirection proprement.

                if(empty($form_error['fatal'])) {

                    // Construction de la requête avec les éléments en commun

                    $sql = 'actif =     '.$db->qstr($_POST['actif']).', ';
                    $sql.= 'acl =       '.$db->qstr($_POST['acl']).', ';
                    $sql.= 'poste =     '.$db->qstr($_POST['poste']).', ';
                    $sql.= 'nom =       '.$db->qstr(mb_strtoupper(delete_accent($_POST['nom']),'UTF-8')).', ';
                    $sql.= 'prenom =    '.$db->qstr($_POST['prenom']).', ';
                    $sql.= 'email =     '.$db->qstr($_POST['email']).', ';

                    if(!empty($_POST['password'])) {
                        $sql.= 'password = '.$db->qstr(hash('sha512', $_POST['password'])).', ';
                    }

                    if(isset($_POST['couleur']) && isset($_POST['theme'])) {
                        $sql.= 'couleur =   '.$db->qstr(implode('|',$_POST['couleur'])).', ';
                        $sql.= 'theme =     '.$db->qstr($_POST['theme']).', ';
                    }
                    
                    $sql.= 'langue =    '.$db->qstr('fr');

                    // Traitement des modifications

                    if($_GET['action'] == 'edit') {
                        $sql = 'UPDATE '.CFG_TABLE_USER.' SET '.$sql.' ';
                        $sql.= 'WHERE ';
                        $sql.= 'user_id = 	'.$db->qstr($_GET['id']);
                    }

                    // Traitement des ajouts

                    elseif($_GET['action'] == 'add') {
                        $sql = 'INSERT INTO '.CFG_TABLE_USER.' SET '.$sql.', ';
                        $sql.= 'last =      '.$db->qstr(date('Y-m-d H:i:s')).' ';
                    }

                    $db->execute($sql);

                    // On doit prévoir de basculer en action edit, si présence de warning alors que nous sommes en action add

                    if($_GET['action']=='add')
                        $form_structure['form']['action']=str_replace('action=add', 'action=edit', $form_structure['form']['action']).'&amp;id='.$db->insert_id();

                    // Traitement erreur non bloquante

                    if(empty($form_error['warning'])) {
                        if($_GET['action']=='add') {
                            echo form_message(STR_FORM_E_WARNING, STR_UTILISATEUR_E_INFO_1, 'success', 'session');
                            redirect(CFG_PATH_HTTP.'/utilisateur/simple.php');
                        }
                        else {
                            echo form_message(STR_FORM_E_WARNING, STR_UTILISATEUR_E_INFO_2, 'success', 'session');
                            echo form_parser($form_structure);
                        }
                    }
                    else {
                        echo form_message(STR_FORM_E_WARNING, $form_error['warning'], 'warning', 'normal');
                        echo form_parser($form_structure);
                    }
                }

                // Si erreur bloquante

                else {
                    echo form_message(STR_FORM_E_FATAL, $form_error['fatal']);
                    echo form_parser($form_structure);
                }
            }

            // Si _POST est vide

            else {
                echo form_parser($form_structure, (isset($_GET['action']) && $_GET['action']=='view'));
            }
    }
}

// Backoffice
// Si pas d'action en GET

if(empty($_GET['action'])){

    $structure= array(
        'context' => array(
            'name' => 'utilisateur_simple',
        ),
        'script' => array(
            'name'=> basename($_SERVER['PHP_SELF']),
            'action'=> array('label' => 'action'),
            'id' =>    array('label' => 'id', 'value' => 'user_id'),
        ),
        'config' => array(
            'css' => 'backoffice',
            'action'=> array('empty'=>TRUE, 'hide'=>TRUE, 'width'=>'35%', 'token'=>TRUE),
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
            'ajax' => TRUE,
        ),
        'filtre' => array(
            array('field'=>'nom',       'label'=>STR_UTILISATEUR_LIBELLE_NOM,       'type'=>'input',    'like'=>TRUE),
            array('field'=>'prenom',    'label'=>STR_UTILISATEUR_LIBELLE_PRENOM,    'type'=>'input',    'like'=>TRUE),
            array('field'=>'poste',     'label'=>STR_UTILISATEUR_LIBELLE_POSTE,     'type'=>'liste',    'value'=>array('Chef de projet'=>'Chef de projet', 'Développeur'=>'Développeur', 'Démonstrateur'=>'Démonstrateur', 'Commercial' => 'Commercial', 'Stagiaire' => 'Stagiaire', 'Directeur' => 'Directeur'), 'like'=>TRUE, 'logical'=>'OR'),
            array('field'=>'actif',     'label'=>STR_UTILISATEUR_LIBELLE_STATUS,    'type'=>'select',   'value'=>array(''=>STR_UTILISATEUR_LIBELLE_TOUS, '1'=>STR_UTILISATEUR_LIBELLE_ACTIF, '0'=>STR_UTILISATEUR_LIBELLE_INACTIF), 'like'=>FALSE),
        ),
        'requete' => array(
            'select'=>'SELECT user_id, actif, nom, prenom, poste, last FROM '.CFG_TABLE_USER.' ORDER BY nom, prenom ASC',
            'result_user_function'=> 'result_utilisateur',
        ),
        'data' => array(
            array('field'=>'last',      'label'=>STR_UTILISATEUR_LIBELLE_DATE,      'order'=>TRUE, 'export'=>TRUE,  'rowspan'=>TRUE),
            array('field'=>'poste',     'label'=>STR_UTILISATEUR_LIBELLE_POSTE,     'order'=>TRUE, 'export'=>FALSE, 'rowspan'=>TRUE),
            array('field'=>'nom',       'label'=>STR_UTILISATEUR_LIBELLE_NOM,       'order'=>TRUE, 'export'=>TRUE,  'rowspan'=>TRUE),
            array('field'=>'prenom',    'label'=>STR_UTILISATEUR_LIBELLE_PRENOM,    'order'=>TRUE, 'export'=>TRUE,  'rowspan'=>TRUE),
        ),
        'action' => array(
            array('field'=>'add',                       'label'=>STR_FORM_ADD,                              'type'=>'outside',	'format' => 'icon', 'src'=>'glyphicon glyphicon-pencil'),
            array('field'=>'add',                       'label'=>STR_FORM_ADD,                              'type'=>'global',	'format' => 'icon', 'src'=>'glyphicon glyphicon-pencil'),
            array('field'=>'edit',                      'label'=>STR_FORM_EDIT,                             'type'=>'local', 'format' => 'button'),
            array('field'=>'view',                      'label'=>STR_FORM_VIEW,                             'type'=>'local', 'format' => 'button'),
            array('field'=>array('active', 'desactive'),'label'=>array(STR_FORM_ACTIVE, STR_FORM_DESACTIVE),'type'=>'local', 'format' => 'button'),
            array('field'=>'del',                       'label'=>STR_FORM_DELETE,                           'type'=>'local', 'format' => 'button'),
            array('field'=>'del_group',                 'label'=>STR_FORM_DELETE_ALL,                       'type'=>'group', 'format' => 'button'),
            array('field'=>'active_group',              'label'=>STR_FORM_ACTIVE_ALL,                       'type'=>'group', 'format' => 'button'),
            array('field'=>'desactive_group',           'label'=>STR_FORM_DESACTIVE_ALL,                    'type'=>'group', 'format' => 'button'),
        ),
        'navigation' => array(
            'page' => 10,
            'item' => 10,
            'choix_item' => array(5,10,15,20,0),
        ),
        'export' => array(
            'format'    => array('pdf', 'csv', 'yml'),    // pdf, csv ou yml
            'all'       => TRUE,                          // export de tous les résultats
        ),
        'js' => '
            <script type="text/javascript">
                $(function() {   
                    // Confirmation sur action del
                    
                    $(".del_local").backoffice_confirm("'.STR_FORM_DELETE_CONFIRMATION.' %5 %4 ?");
                    
                    // Confirmation sur action group
                    
                    $(".del_group").backoffice_group("'.STR_FORM_DELETE_ALL.' ?");
                    $(".active_group").backoffice_group("'.STR_FORM_ACTIVE_ALL.' ?");
                    $(".desactive_group").backoffice_group("'.STR_FORM_DESACTIVE_ALL.' ?");
                });
            </script>
        ',
    );

    backoffice($structure);
}

//
// Fin du traitement
//

require 'stop_php.php';

/*
 * Fonction result_utilisateur(&$data, &$action)
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

function result_utilisateur(&$data, &$action) {

    while(list($k,)=each($data)) {
        if($data[$k]['actif']==1)
            $data[$k]['active,desactive']=2;
        else
            $data[$k]['active,desactive']=1;

        if($data[$k]['poste']=='Stagiaire') {
            $data[$k]['edit']=0;
            $data[$k]['del']=0;
            $data[$k]['del_group']=0;
            $data[$k]['active_group']=0;
            $data[$k]['desactive_group']=0;
        }

        $data[$k]['last']=date_iso_to($data[$k]['last']);
    }
}
?>