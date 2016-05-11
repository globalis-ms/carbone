<?php
//
// Chargement initiaux
//

require 'local_config.php';
require 'local_lib.php';
require '../../include/config/config.php';          // Fichier de Configuration Globale
require '../../include/lib/lib_carbone.php';        // Librairie Carbone
require '../../include/lib/class_db.php';           // Class Abstraction SGBD

//
// Vérification des usages
//

$arg=argument($argv);

$help=FALSE;
$data=FALSE;
$session=FALSE;
$user=FALSE;

$arg_valide=array();
foreach($cfg_arg['database'] as $list => $label) {
    $tmp=explode(',', $list);
    foreach($tmp as $option) {
        $option=str_replace(' ', '', $option);
        $option=str_replace('-', '', $option);
        $arg_valide[]=$option;
    }
}

if(isset($arg['argument'])) {
    foreach($arg['argument'] as $key => $value) {
        if(!in_array($key, $arg_valide)) {
            $arg=FALSE;
            break;
        }
        else {
            if($key=='h' || $key=='help')
                $help=TRUE;
            elseif($key=='u' || $key=='user')
                $user=TRUE;
            elseif($key=='d' || $key=='data')
                $data=TRUE;
            elseif($key=='s' || $key=='session')
                $session=TRUE;
        }
    }
}

if(!$arg || $help==TRUE) {
    $flux = '';
    $flux.= "usage : php database.php [OPTION]\n";
    $flux.= "Purge database\n\n";

    foreach($cfg_arg['database'] as $list => $label) {
        $flux.=$list."\t".$label."\n";
    }

    die($flux);
}

//
// Début de traitement
//

//
// Ouvertue de la connexion SGBD
//

$db = ADONewConnection(CFG_TYPE);
$db->connect(CFG_HOST, CFG_USER, CFG_PASS, CFG_BASE);

if(!$db){
    die("Pas de connexion");
}

//
// Passage en UTF-8
//

$db->Execute("SET NAMES 'utf8', character_set_results = 'utf8', character_set_client = 'utf8', character_set_connection = 'utf8', character_set_database = 'utf8', character_set_server = 'utf8'");

//
// Purge de la table session
//

if($session) {
    $sql="TRUNCATE TABLE `session`;";
    $db->execute($sql);
}

//
// Purge de la table data
//

elseif($data) {
    $sql = "SELECT upload_1, upload_2 FROM `data`;";
    $recordset = $db->execute($sql);  
    while ($row = $recordset->fetchrow()) {
        if(!empty($row['upload_1']) && file_exists(CFG_PATH_FILE_UPLOAD.'/'.$row['upload_1']))
            unlink(CFG_PATH_FILE_UPLOAD.'/'.$row['upload_1']);
        if(!empty($row['upload_2']) && file_exists(CFG_PATH_FILE_UPLOAD.'/foo/'.$row['upload_2']))
            unlink(CFG_PATH_FILE_UPLOAD.'/foo/'.$row['upload_2']);
    }

    $sql="TRUNCATE TABLE `data`;";
    $db->execute($sql);
}

//
// Purge de la table user
//

elseif($user) {

    $sql="TRUNCATE TABLE `user`;";

    $db->execute($sql);

    $sql="
    INSERT INTO `user` (`user_id`, `actif`, `acl`, `poste`, `nom`, `prenom`, `email`, `password`, `couleur`, `theme`, `langue`, `last`) VALUES
    ( 1, 1, 'admin', 'Directeur',   'FAUVEAU', 'Armel', 'armel.fauveau@globalis-ms.com', '".hash('sha512','armel')."', 'bleu', 'bootstrap', 'fr', '2009-07-06 03:05:44'),
    ( 2, 1, 'admin', 'Directeur',   'HOVART', 'Frédéric', 'fred.hovart@globalis-ms.com', '".hash('sha512','frederic')."', 'bleu', 'bootstrap', 'fr', '2006-03-14 09:59:22'),
    ( 3, 1, 'admin', 'Chef de projet', 'OGER', 'Julien', 'julien.oger@globalis-ms.com', '".hash('sha512','julien')."', 'bleu', 'bootstrap', 'fr', '2006-11-27 15:35:59'),
    ( 4, 1, 'admin', 'Chef de projet', 'REIGNAULT', 'Carine', 'carine.reignault@globalis-ms.com', '".hash('sha512','carine')."', 'bleu', 'bootstrap', 'fr', '2009-07-06 02:55:26'),
    ( 5, 1, 'admin', 'Chef de projet', 'WERWINSKI', 'Ludovic', 'ludovic.werwinski@globalis-ms.com', '".hash('sha512','ludovic')."', 'bleu', 'bootstrap', 'fr', '2009-06-30 04:27:33'),
    ( 6, 1, 'admin', 'Développeur', 'RALU', 'Magali', 'magali.ralu@globalis-ms.com', '".hash('sha512','magali')."', 'bleu', 'bootstrap', 'fr', '2009-07-06 03:00:19'),
    ( 7, 1, 'admin', 'Développeur', 'HOURDEAUX', 'Christophe', 'christophe.hourdeaux@globalis-ms.com', '".hash('sha512','christophe')."', 'bleu', 'bootstrap', 'fr', '2009-07-06 02:57:48'),
    ( 8, 1, 'admin', 'Commercial', 'GUIAS', 'Stéphane', 'stephane.guias@globalis-ms.com', '".hash('sha512','stephane')."', 'bleu', 'bootstrap', 'fr', '2009-07-06 02:57:48'),
    ( 9, 1, 'admin', 'Développeur', 'LAURENCE', 'Aurelie', 'aurelie.laurence@globalis-ms.com', '".hash('sha512','aurelie')."', 'bleu', 'bootstrap', 'fr', '2009-07-06 03:01:12'),
    (10, 1, 'admin', 'Développeur', 'LIARD', 'Julien', 'julien.liard@globalis-ms.com', '".hash('sha512','julien')."', 'bleu', 'bootstrap', 'fr', '2009-07-06 03:01:12'),
    (11, 1, 'admin', 'Développeur', 'DUBUS', 'Sylvain', 'sylvain.dubus@globalis-ms.com', '".hash('sha512','sylvain')."', 'bleu', 'bootstrap', 'fr', '2009-07-06 03:01:12'),
    (12, 1, 'admin', 'Développeur', 'GUERRY', 'Matthieu', 'matthieu.guerry@globalis-ms.com', '".hash('sha512','matthieu')."', 'bleu', 'bootstrap', 'fr', '2009-07-06 03:01:12'),
    (13, 1, 'admin', 'Chef de projet', 'JAKUBIAK', 'Alexandre', 'alexandre.jakubiak@globalis-ms.com', '".hash('sha512','alexandre')."', 'bleu', 'bootstrap', 'fr', '2009-07-06 03:01:12'),
    (14, 1, 'admin', 'Développeur', 'DORR', 'Romain', 'romain.dorr@globalis-ms.com', '".hash('sha512','romain')."', 'bleu', 'bootstrap', 'fr', '2009-07-06 03:01:12'),
    (15, 1, 'admin', 'Développeur', 'GUGERT', 'Romain', 'romain.gugert@globalis-ms.com', '".hash('sha512','romain')."', 'bleu', 'bootstrap', 'fr', '2009-07-06 03:01:12'),
    (16, 1, 'admin', 'Développeur', 'LEPLAT', 'Pierre', 'pierre.leplat@globalis-ms.com', '".hash('sha512','pierre')."', 'bleu', 'bootstrap', 'fr', '2009-07-06 03:01:12'),
    (17, 1, 'admin', 'Développeur', 'CHERNOVA', 'Natalia', 'natalia.chernova@globalis-ms.com', '".hash('sha512','natalia')."', 'bleu', 'bootstrap', 'fr', '2009-07-06 03:01:12'),
    (18, 1, 'admin', 'Chef de projet', 'AUBERT', 'Mathieu', 'mathieu.aubert@globalis-ms.com', '".hash('sha512','mathieu')."', 'bleu', 'bootstrap', 'fr', '2009-07-06 03:01:12'),
    (19, 1, 'admin', 'Développeur', 'MULLER', 'Sébastien', 'sebastien.muller@globalis-ms.com', '".hash('sha512','sebastien')."', 'bleu', 'bootstrap', 'fr', '2009-07-06 03:01:12'),
    (20, 1, 'admin', 'Développeur', 'YANG', 'Zaifeng', 'zaifeng.yang@globalis-ms.com', '".hash('sha512','zaifeng')."', 'bleu', 'bootstrap', 'fr', '2009-07-06 03:01:12'),
    (21, 1, 'admin', 'Développeur', 'RINVILLE', 'Evans', 'evans.rinville@globalis-ms.com', '".hash('sha512','evans')."', 'bleu', 'bootstrap', 'fr', '2009-07-06 03:01:12'),
    (22, 1, 'admin', 'Développeur', 'BRODIN', 'Stanislas', 'stanislas.brodin@globalis-ms.com', '".hash('sha512','stanislas')."', 'bleu', 'bootstrap', 'fr', '2009-07-06 03:01:12'),
    (23, 1, 'admin', 'Développeur', 'DELCROIX', 'Lucas', 'lucas.delcroix@globalis-ms.com', '".hash('sha512','lucas')."', 'bleu', 'bootstrap', 'fr', '2009-07-06 03:01:12'),
    (24, 1, 'admin', 'Commercial', 'FRACHON', 'Aurelia', 'aurelia.frachon@globalis-ms.com', '".hash('sha512','aurelia')."', 'bleu', 'bootstrap', 'fr', '2009-07-06 03:01:12'),
    (25, 1, 'admin', 'Développeur', 'EURANIE', 'Mickael', 'mickael.euranie@globalis-ms.com', '".hash('sha512','mickael')."', 'bleu', 'bootstrap', 'fr', '2009-07-06 03:01:12'),
    (26, 1, 'admin', 'Développeur', 'LELIEVRE', 'Laurent', 'laurent.lelievre@globalis-ms.com', '".hash('sha512','laurent')."', 'bleu', 'bootstrap', 'fr', '2009-07-06 03:01:12'),
    (27, 1, 'admin', 'Développeur', 'BROCHANT', 'Sulivan', 'sulivan.brochan@globalis-ms.com', '".hash('sha512','sulivan')."', 'bleu', 'bootstrap', 'fr', '2009-07-06 03:01:12'),
    (28, 1, 'admin', 'Développeur', 'DARGHAM', 'Pierre', 'pierre.dargham@globalis-ms.com', '".hash('sha512','pierre')."', 'bleu', 'bootstrap', 'fr', '2009-07-06 03:01:12'),

    (50, 1, 'user', 'Stagiaire', 'STAGE1', 'Stage1', 'stage1@globalis-ms.com', '".hash('sha512','stage1')."', 'rouge', 'bootstrap', 'fr', '2009-07-06 03:01:12'),
    (51, 1, 'user', 'Stagiaire', 'STAGE2', 'Stage2', 'stage2@globalis-ms.com', '".hash('sha512','stage2')."', 'rouge', 'bootstrap', 'fr', '2009-07-06 03:01:12'),
    (52, 1, 'user', 'Stagiaire', 'STAGE3', 'Stage3', 'stage3@globalis-ms.com', '".hash('sha512','stage3')."', 'rouge', 'bootstrap', 'fr', '2009-07-06 03:01:12'),
    (53, 1, 'user', 'Stagiaire', 'STAGE4', 'Stage4', 'stage4@globalis-ms.com', '".hash('sha512','stage4')."', 'rouge', 'bootstrap', 'fr', '2009-07-06 03:01:12'),
    (54, 1, 'user', 'Stagiaire', 'STAGE5', 'Stage5', 'stage5@globalis-ms.com', '".hash('sha512','stage5')."', 'rouge', 'bootstrap', 'fr', '2009-07-06 03:01:12'),
    (55, 1, 'user', 'Stagiaire', 'STAGE6', 'Stage6', 'stage6@globalis-ms.com', '".hash('sha512','stage6')."', 'rouge', 'bootstrap', 'fr', '2009-07-06 03:01:12'),

    (75, 1, 'guest', 'Démonstrateur', 'BAR', 'Foo', 'foo.bar@globalis-ms.com', '".hash('sha512','foo')."', 'magenta', 'bootstrap', 'fr', '2009-07-06 03:01:12');
    ";

    $db->execute($sql);

    //
    // Initialisation des tableaux de valeurs
    //

    $num=substr_count($sql, '(');

    $tableau_nom = file('../../data/prenom.dat');
    $tableau_couleur = file('../../data/couleur.dat');
    $tableau_theme = array('bootstrap', 'cerulean', 'simplex', 'slate', 'united', 'bootstrap_vertical');
    $tableau_langue = array('fr', 'uk');
    $tableau_poste =  array(
                    'Démonstrateur',
                    );

    //
    // Insertion des comptes annexes
    //

    srand((double)microtime()*1000000);

    for($loop=$num; $loop<=500; $loop++) {
        $actif=rand(0,0);
        $acl='guest';
        $poste=$tableau_poste[array_rand($tableau_poste, 1)];
        $nom=trim($tableau_nom[array_rand($tableau_nom, 1)]);
        $prenom=trim($tableau_nom[array_rand($tableau_nom, 1)]);
        $email=mb_strtolower(delete_accent($prenom).'.'.delete_accent($nom).'@globalis-ms.com','UTF-8');
        $password=mb_strtolower(delete_accent($prenom),'UTF-8');
        $couleur=trim($tableau_couleur[array_rand($tableau_couleur, 1)]);
        $theme=$tableau_theme[array_rand($tableau_theme, 1)];
        $langue=$tableau_langue[array_rand($tableau_langue, 1)];
        $date='';
        do {
            $date=rand(2010,date('Y')).'-'.rand(1,12).'-'.rand(1,28).' '.date('H:i:s');
            $now=date('Y-m-d H:i:s');
        } while ($date>$now);

        $sql = 'INSERT INTO '.CFG_TABLE_USER.' ';
        $sql.= '(actif, acl, poste, nom, prenom, email, password, couleur, theme, langue, last) ';
        $sql.= 'VALUES (';
        $sql.= $db->qstr($actif).', ';
        $sql.= $db->qstr($acl).', ';
        $sql.= $db->qstr($poste).', ';
        $sql.= $db->qstr(mb_strtoupper(delete_accent($nom),'UTF-8')).', ';
        $sql.= $db->qstr($prenom).', ';
        $sql.= $db->qstr($email).', ';
        $sql.= $db->qstr(hash('sha512', $password)).', ';
        $sql.= $db->qstr($couleur).', ';
        $sql.= $db->qstr($theme).', ';
        $sql.= $db->qstr($langue).', ';
        $sql.= $db->qstr($date);
        $sql.= ')';

        $db->execute($sql);
    }
}
?>