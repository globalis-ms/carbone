<?php
// Brique BackOffice externe

$structure= array(
    'context' => array(
        'name' => 'utilisateur_split',
    ),
    'script' => array(
        'name'=> basename($_SERVER['PHP_SELF']),
        'action'=> array('label' => 'action'),
        'id' =>    array('label' => 'id', 'value' => 'user_id'),
    ),
    'config' => array(
        'css' => 'backoffice',
        'tpl' => '{filtre}{total}{outside}{data}{navig_export}',
        'action'=> array('empty'=>TRUE, 'hide'=>TRUE, 'width'=>'30%', 'legende'=>TRUE),
        'total' => TRUE,
        'total_string' => STR_UTILISATEUR_LIBELLE_NOMBRE,
        'type' => 'string',
        'logical' => 'AND',
        'ajax' => FALSE,
    ),
    'filtre' => array(
        array('field'=>'foo',       'label'=>STR_UTILISATEUR_LIBELLE_DATE_DEBUT,    'type'=>'input',    'like'=>FALSE),
        array('field'=>'bar',       'label'=>STR_UTILISATEUR_LIBELLE_DATE_FIN,      'type'=>'input',    'like'=>FALSE),
        array('field'=>'nom',       'label'=>STR_UTILISATEUR_LIBELLE_NOM,           'type'=>'input',    'like'=>FALSE),
        array('field'=>'poste',     'label'=>STR_UTILISATEUR_LIBELLE_POSTE,         'type'=>'liste',    'value'=>array('Chef de projet'=>'Chef de projet', 'Développeur'=>'Développeur', 'Démonstrateur'=>'Démonstrateur', 'Commercial' => 'Commercial', 'Stagiaire' => 'Stagiaire', 'Directeur' => 'Directeur'), 'like'=>TRUE, 'logical'=>'OR'),
        array('field'=>'actif',     'label'=>STR_UTILISATEUR_LIBELLE_STATUS,        'type'=>'select',   'value'=>array(''=>STR_UTILISATEUR_LIBELLE_TOUS, '1'=>STR_UTILISATEUR_LIBELLE_ACTIF, '0'=>STR_UTILISATEUR_LIBELLE_INACTIF), 'like'=>FALSE),
    ),
    'requete' => array(
        'select'=>'SELECT user_id, actif, nom, prenom, last, poste FROM '.CFG_TABLE_USER.' ORDER BY nom, prenom ASC',
        'select_user_function'=> 'select_utilisateur',
        'result_user_function'=> 'result_utilisateur',
    ),
    'data' => array(
        array('field'=>'last',      'label'=>STR_UTILISATEUR_LIBELLE_DATE,      'order'=>TRUE, 'export'=>FALSE,  'rowspan'=>TRUE),
        array('field'=>'poste',     'label'=>STR_UTILISATEUR_LIBELLE_POSTE,     'order'=>TRUE, 'export'=>TRUE,   'rowspan'=>TRUE),
        array('field'=>'nom',       'label'=>STR_UTILISATEUR_LIBELLE_NOM,       'order'=>TRUE, 'export'=>TRUE,   'rowspan'=>TRUE),
        array('field'=>'prenom',    'label'=>STR_UTILISATEUR_LIBELLE_PRENOM,    'order'=>TRUE, 'export'=>TRUE,   'rowspan'=>TRUE),
    ),
    'action' => array(
        array('field'=>'add',                       'label'=>STR_FORM_ADD,                              'type'=>'outside',	'format' => 'icon', 'src'=>'glyphicon glyphicon-pencil'),
        array('field'=>'add',                       'label'=>STR_FORM_ADD,                              'type'=>'global',	'format' => 'icon', 'src'=>'glyphicon glyphicon-pencil'),
        array('field'=>'edit',                      'label'=>STR_FORM_EDIT,                             'type'=>'local',    'format' => 'icon', 'src'=>'glyphicon glyphicon-edit'),
        array('field'=>'view',                      'label'=>STR_FORM_VIEW,                             'type'=>'local',    'format' => 'icon', 'src'=>'glyphicon glyphicon-eye-open'),
        array('field'=>array('active', 'desactive'),'label'=>array(STR_FORM_ACTIVE, STR_FORM_DESACTIVE),'type'=>'local',    'format' => 'icon', 'src'=>array('glyphicon glyphicon-ok', 'glyphicon glyphicon-remove')),
        array('field'=>'del',                       'label'=>STR_FORM_DELETE,                           'type'=>'local',    'format' => 'icon', 'src'=>'glyphicon glyphicon-trash'),
        array('field'=>'del_group',                 'label'=>STR_FORM_DELETE_ALL,                       'type'=>'group'),
        array('field'=>'active_group',              'label'=>STR_FORM_ACTIVE_ALL,                       'type'=>'group'),
        array('field'=>'desactive_group',           'label'=>STR_FORM_DESACTIVE_ALL,                    'type'=>'group'),
    ),
    'navigation' => array(
        'page' => 10,
        'item' => 10,
        'choix_item' => array(5,10,15,20,0),
    ),
    'export' => array(
        'format'    => array('pdf', 'csv'),    // pdf ou csv
    ),
    'js' => '
        <script type="text/javascript">
            $(function() {
                // Calendrier
      
                $("#foo").datepicker({format: "dd-mm-yyyy", weekStart: 1});
                $("#bar").datepicker({format: "dd-mm-yyyy", weekStart: 1});
                      
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

//echo strlen(backoffice($structure));
$foo=backoffice($structure);

//
// Exemple de traitement en mode string
//

$session_context=$session->get_var($structure['context']['name']);
if($session_context['total']>1)
    echo str_replace(STR_UTILISATEUR_LIBELLE_NOMBRE, str_replace('(s)', 's', STR_UTILISATEUR_LIBELLE_NOMBRE), $foo);
else
    echo str_replace(STR_UTILISATEUR_LIBELLE_NOMBRE, str_replace('(s)', '', STR_UTILISATEUR_LIBELLE_NOMBRE), $foo);

/*
 * Fonction select_utilisateur(&$sql)
 * -----
 * Exemple de call user func défini dans la structure backoffice et appelé par la fonction backoffice()
 * -----
 * @param   array       $sql                    la requête
 * @global  array       $cfg_profil             le profil de l'utilisateur courant
 * -----
 * @return  array       $sql                    la requête éventuellement modifiée
 * -----
 * $Author: armel $
 * $Copyright: GLOBALIS media systems $
 */

function select_utilisateur(&$sql) {
    global $cfg_profil;

    // Traitement du champ nom, pour rechercher plusieurs nom en même temps

    $pattern='/(nom = \')(.*?)(\')/i';
    preg_match($pattern, $sql, $matches);
    if(!empty($matches)) {
        $replace='nom = \''.str_replace(',', '\' OR nom = \'', str_replace(' ', '', $matches[2])).'\'';
        $sql=preg_replace($pattern, $replace, $sql);
    }

    // Traitement des champs foo et bar pour générer une clause sur le champ last

    $pattern='/(foo = \')(.*?)(\')/i';
    preg_match($pattern, $sql, $matches);
    if(!empty($matches)) {
        $replace='last >= \''.date_to_iso($matches[2], 'd-m-Y').'\'';
        $sql=preg_replace($pattern, $replace, $sql);
    }

    $pattern='/(bar = \')(.*?)(\')/i';
    preg_match($pattern, $sql, $matches);
    if(!empty($matches)) {
        $replace='last <= \''.date_to_iso($matches[2], 'd-m-Y').'\'';
        $sql=preg_replace($pattern, $replace, $sql);
    }
}

/*
 * Fonction result_utilisateur(&$data, &$action)
 * -----
 * Exemple de call user func défini dans la structure backoffice et appelé par la fonction backoffice()
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

        $data[$k]['last']=date_iso_to($data[$k]['last']);
    }
}
?>