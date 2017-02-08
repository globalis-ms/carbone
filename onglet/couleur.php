<?php
// Chargement du framework

require 'start_php.php';

// Construction du titre de la page <title>

if(!empty($_GET['action']) && $_GET['action']=='add')
    define('RUBRIQUE_TITRE', STR_ONGLET_TITRE.' > '.STR_ONGLET_TITRE_ETAPE_1);
elseif(!empty($_GET['action']) && $_GET['action']=='edit')  {
        $sql = 'SELECT nom, prenom FROM '.CFG_TABLE_USER.' WHERE user_id = '.$db->qstr($_GET['user_id']);
        $edit = $db->getrow($sql);
        define('RUBRIQUE_TITRE', STR_ONGLET_TITRE.' > '.STR_ONGLET_TITRE_ETAPE_2.' > '.$edit['prenom'].' '.$edit['nom']);
}
else
    define('RUBRIQUE_TITRE', STR_ONGLET_TITRE.' > '.STR_ONGLET_SOUS_TITRE);

// Embarquement des scripts et styles additionnels nécessaires

define('LOAD_JAVASCRIPT', 'backoffice/jquery.backoffice.js|multiselect/jquery.multiselect.js');
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
        case 'del' :

            $sql = 'SELECT couleur FROM '.CFG_TABLE_USER.' WHERE user_id = '.$db->qstr($_GET['user_id']);
            $edit['couleur'] = $db->getone($sql);

            //print_rh($edit['couleur']);

            $edit['couleur']=str_replace('|'.$_GET['couleur'], '', '|'.$edit['couleur']);
            $edit['couleur']=substr($edit['couleur'], 1);

            //print_rh($edit['couleur']);

            $sql = 'UPDATE '.CFG_TABLE_USER.' SET couleur = '.$db->qstr($edit['couleur']).' WHERE user_id = '.$db->qstr($_GET['user_id']);

            $db->execute($sql);

        case 'edit' :

            if(empty($_POST))
            {
                $sql = 'SELECT couleur FROM '.CFG_TABLE_USER.' WHERE user_id = '.$db->qstr($_GET['user_id']);
                $edit = $db->getrow($sql);

                $edit['couleur']=explode('|', $edit['couleur']);
            }

        default :

            // Construction de l'url <form>

            $url=get_url(basename($_SERVER['PHP_SELF']));

            // Initialisation des valeurs par défaut

            $data = array(
                'couleur' => '',
            );

            // Mise a jour des données selon ce que contient $_POST

            $data = form($data, !empty($edit) ? $edit : array());

            // Construction du Formulaire

            $form_structure = array(
                'form' => array(
                    'item' => 'form',
                    'action' => $url
                ),

                'couleur' => array(
                    'item' => 'select',
                    'tpl' => '[(3){libelle}(9){form} {legende}]',
                    'libelle' => STR_UTILISATEUR_LIBELLE_COULEUR,
                    'value' => get_couleur(),
                    'selected' => $data['couleur'],
                    'multiple' => TRUE,
                    'size' => 10,
                    'require' => FALSE,
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

            // Application des plugins jQuery

            echo '
                <script type="text/javascript"><!--
                    $(function() {
                        $("#couleur").multiselect();
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
                    if(!isset($_POST['couleur']))
                        $_POST['couleur']=array();
                }

                // Si toujours pas d'erreur bloquante -> Traitement sgbd

                if(empty($form_error['fatal'])) {

                    $sql = 'UPDATE '.CFG_TABLE_USER.' SET ';
                    $sql.= 'couleur = 	'.$db->qstr(implode('|',$_POST['couleur'])).' ';
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

// Backoffice
// Si pas d'action en GET

$structure= array(
    'context' => array(
        'name' => 'droit',
    ),
    'script' => array(
        'name'=> basename($_SERVER['PHP_SELF']).'?user_id='.$_GET['user_id'],
        'action'=> array('label' => 'action', 'colspan'=>TRUE),
        'id' =>    array('label' => 'couleur', 'value' => 'couleur'),
    ),
    'config' => array(
        'css' => 'backoffice',
        'view_empty' => FALSE,
        'type' => 'string',
    ),
    'requete' => array(
        'select'=>'SELECT user_id, couleur FROM '.CFG_TABLE_USER.' WHERE user_id='.$db->qstr($_GET['user_id']),
        'result_user_function'=> 'couleur',
    ),
    'data' => array(
        array('field'=>'couleur_libelle', 'label'=>STR_UTILISATEUR_LIBELLE_COULEUR, 'order'=>FALSE, 'export'=>FALSE, 'rowspan'=>FALSE),
    ),
    'action' => array(
        array('field'=>'del', 'label'=>STR_FORM_DELETE, 'type'=>'local', 'js'=>'onclick="return confirm(\''.STR_FORM_DELETE_CONFIRMATION.' %s\');"', 'on'=>array('couleur')),
    ),
);

print '<br/>'.backoffice($structure);

//
// Fin du traitement
//

require 'stop_php.php';

/*
 * Fonction droit($data, $action)
 * -----
 * Exemple de call user func défini dans la structure backoffice et appelé par la fonction backoffice()
 * -----
 * @param   array       $data                   le flux des données remontées par le SELECT
 * @param   array       $action                 les actions
 * @global  array       $navigation             le tableau de navigation
 * -----
 * @return  array       $data                   le flux des données éventuellement modifié
 * -----
 * $Author: armel $
 * $Copyright: GLOBALIS media systems $
 */

function couleur(&$data, &$action) {
    global $navigation;

    $new_data=array();
    $couleur=get_couleur();

    if($data[0]['couleur']!='') {
        $tmp=explode('|', $data[0]['couleur']);

        while(list($k, $v)=each($tmp)) {
            $new_data[$k]['user_id']=$data[0]['user_id'];
            $new_data[$k]['couleur']=$v;
            $new_data[$k]['couleur_libelle']=$couleur[$v];
            $new_data[$k]['add']='1';
            $new_data[$k]['del']='1';
        }
        $data=$new_data;
    }
    else
        $data=array();
}
?>