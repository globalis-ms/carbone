<?php
//
// Ouvertue de la connexion SGBD
//

$db =ADONewConnection(CFG_TYPE);
$indicateur_db = $db->connect(CFG_HOST, CFG_USER, CFG_PASS, CFG_BASE);

//
// Si une erreur de connexion à la base de données est détectée, il est inutile d'aller plus loin => Traitement alternatif
//
if ($indicateur_db === FALSE){
    // Si on n'est pas sur la page "nobdd" ou sur la page de demande d'export vers intersection, on redirige vers cette page
    $page=basename($_SERVER['PHP_SELF']);
    if ($page != 'nobdd.php'){
        if(!headers_sent()) {
            header("Location: ".CFG_PATH_HTTP.'/nobdd.php');
            exit();
        }
        else {
            echo '<script type="text/javascript">window.location.href="'.CFG_PATH_HTTP.'/nobdd.php"</script>';
            exit();
        }
    }
    
    //
    // Initialisation de la navigation à blanc
    //
    $navigation = array();
    
    //
    // Chargement du fichier de langue par défaut
    //
    require 'langue/langue_'.CFG_LANGUE_DEFAULT.'.php';

    //
    // Chargement du fichier de navigation
    //
    require dirname(__FILE__).'/../'.'web/theme/'.$cfg_profil['theme'].'/navigation.php';
}
else {

    //
    // Passage en UTF-8
    //
    $db->Execute("SET NAMES 'utf8', character_set_results = 'utf8', character_set_client = 'utf8', character_set_connection = 'utf8', character_set_database = 'utf8', character_set_server = 'utf8'");

    //
    // Profiling des requêtes SQL
    //
    if(CFG_DEBUG) {
        $db->Execute("SET profiling = 1");
    }

    //
    // Ouverture de la session
    //
    $session = new session;

    //
    // Si la session a expirée on va vers la page de login
    //
    if(!in_array(basename($_SERVER['PHP_SELF']), array('login.php', 'reset.php'))) {
        if(($session->session_expired == TRUE) || empty($session_user_id)) {
            $url=CFG_PATH_HTTP.'/login.php';
            $session->close();
            $db->close();
            header("Location: $url");
            exit();
        }
    }

    //
    // Chargement du fichier de langue
    //
    require 'langue/langue_'.$cfg_profil['langue'].'.php';

    //
    // Chargement du fichier de navigation
    //
    require 'config/navigation.php';


    //
    // Si la session n'a pas expirée
    //

    //
    // Securité : Verification  du forcage éventuel d'URL
    // On regarde si l'intersection (ACL utilisateur / ACL rubrique ou script) n'est pas vide
    // En cas de problem, on redirige vers une page d'erreur
    //

    if($session->session_expired == FALSE) {
        // Vérification de l'accès à la ressource URL et à la ressource GET

        if(!check_acl() || !check_get($cfg_profil['acl'])) {
           $url=$session->url(CFG_PATH_HTTP.'/erreur.php', FALSE);
           header("Location: $url");
           exit();
        }
        check_post($cfg_profil['acl']);
    }
}
?>