<?php
/*
 * Fonction print_rh($data)
 * -----
 * Fonctionnalité d'aide au debug
 * Affichage d'une variable avec une mise en forme HTML
 * -----
 * @param   string      $data                   nom de la variable à afficher
 * -----
 * @return  string                              le contenu de la variable (entre balise <pre> et </pre>)
 * -----
 * $Author: armel $
 * $Copyright: GLOBALIS media systems $
 */

function print_rh($data) {
    echo "<pre>\n";
    print_r($data);
    echo "</pre>\n";
}

/*
 * Fonction print_rc($data)
 * -----
 * Fonctionnalité d'aide au debug
 * Affichage d'une variable avec une mise en forme dans la console
 * -----
 * @param   string      $data                   nom de la variable à afficher
 * -----
 * @return  string                              le contenu de la variable (dans la console)
 * -----
 * $Author: armel $
 * $Copyright: GLOBALIS media systems $
 */

function print_rc($data) {
    if(is_array($data) || is_object($data))
        echo("<script>console.log('Carbone: ".json_encode($data)."');</script>");
    else
        echo("<script>console.log('Carbone: ".$data."');</script>");
}

/*
 * Fonction load_head($css, $js)
 * -----
 * Chargement des script JS
 * -----
 * @param   array       $css                    tableau contenant les feuilles CSS à charger
 * @param   array       $js                     tableau contenant les scripts JS à charger
 * @global  const       LOAD_JAVASCRIPT         constante contenant les scripts JS à charger
 * -----
 * @return  string                              flux HTML de chargement des feuilles CSS et scripts JS
 * -----
 * $Author: armel $
 * $Copyright: GLOBALIS media systems $
 */

function load_head($css, $js) {
    global $session;
    global $cfg_profil;

    $head='';

    if(defined('LOAD_JAVASCRIPT') && LOAD_JAVASCRIPT!='') {
        $tmp=explode('|', LOAD_JAVASCRIPT);
        $tmp=array_unique($tmp);
        // On charge les scripts en cherchant éventuellement les versions minified
        foreach($tmp as $value) {
            $filename_file=substr(CFG_PATH_FILE_WEB."/js/".$value, 0, -3);
            $filename_http=substr(CFG_PATH_HTTP_WEB."/js/".$value, 0, -3);
            if(file_exists($filename_file.".min.js"))
                $js[]="\t<script type=\"text/javascript\" src=\"".$filename_http.".min.js"."\"></script>\n";
            else
                $js[]="\t<script type=\"text/javascript\" src=\"".$filename_http.".js"."\"></script>\n";

            if(strstr($value, 'wysihtml5'))
                //$js[]="\t<script type=\"text/javascript\" src=\"".CFG_PATH_HTTP_WEB."/js/bootstrap_wysihtml5/wysihtml5-0.3.0.min.js\"></script>\n";
                $css[]="\t<link rel=\"stylesheet\" href=\"".CFG_PATH_HTTP_WEB."/theme/".$cfg_profil['theme']."/css/bootstrap-wysihtml5.css\" type=\"text/css\" />\n";
            elseif(strstr($value, 'datepicker'))
                $css[]="\t<link rel=\"stylesheet\" href=\"".CFG_PATH_HTTP_WEB."/theme/".$cfg_profil['theme']."/css/datepicker.css\" type=\"text/css\" />\n";
            elseif(strstr($value, 'autocomplete'))
                $css[]="\t<link rel=\"stylesheet\" href=\"".CFG_PATH_HTTP_WEB."/theme/".$cfg_profil['theme']."/css/autocomplete.css\" type=\"text/css\" />\n";
            elseif(strstr($value, 'notice'))
                $css[]="\t<link rel=\"stylesheet\" href=\"".CFG_PATH_HTTP_WEB."/theme/".$cfg_profil['theme']."/css/notice.css\" type=\"text/css\" />\n";

        }
    }

    // Supression des doublons eventuels et chainage

    $css=implode('', array_unique($css));
    $js=implode('', array_unique($js));

    // On contrôle s'il faut charger jquery

    if(!(strstr($js, 'jquery-1.11.0.min.js')) && strstr($js, 'jquery.'))
        $js="\t<script type=\"text/javascript\" src=\"".CFG_PATH_HTTP_WEB."/js/jquery-1.11.0.min.js\"></script>\n".$js;

    // Mise en cache eventuelle

    if((CFG_OPTIMISATION_LEVEL&1)==1) {
        $head.="\n\t<!--css-->\n";
        $head.=optimize_head($css, $type="css");
        $head.="\t<!--start js-->\n";
        $head.=optimize_head($js, $type="js");
    }
    else {
        $head.="\n\t<!--css-->\n";
        $head.=$css;
        $head.="\t<!--start js-->\n";
        $head.=$js;
    }

    $head.="\t<!--stop js-->\n";

    return $head;
}

/*
 * Fonction optimize_head($js)
 * -----
 * Optimisation du <head></head>
 * -----
 * @param   string      $flux                   variable contenant le flux html
 * @param   string      $type                   variable contenant le type (css ou js)
 * -----
 * @return  string                              flux optimisé a mettre en cache
 * -----
 * $Author: armel $
 * $Copyright: GLOBALIS media systems $
 */

function optimize_head($flux='', $type) {
    global $cfg_profil;

    $html='';
    $file='';
    preg_match_all('/="'.addcslashes(CFG_PATH_HTTP_WEB, '/').'(.*)"/Uims', $flux, $tmp);

    // on tri la liste pour eviter les doublons et garder des crc conformes

    $list=$tmp[1];
    sort($list);
    $crc32=CFG_VERSION.'.'.sprintf("%u",crc32(implode(':', $list)));
    $filename=CFG_PATH_FILE_WEB.'/cache/'.$crc32.'.'.$type;

    if(!file_exists($filename)) {
        foreach($tmp[1] as $value) {
            $file.="\n\n";
            $file.=file_get_contents(CFG_PATH_FILE_WEB.$value);
        }

        if($type=='css') {
            // Changement des url : a adapter si besoin
            //$file = preg_replace("/url(.*)(['\"]\.\.|\.\.)(.*)(['\"]\))(.*)/", "url(..$3)$5", $file);
            //$file = preg_replace("/url(.*)(\.\.)(.*)\)(.*)/", "url(../theme/".$cfg_profil['theme']."$3)$4", $file);
            $file = str_replace('../fonts/', '../theme/'.$cfg_profil['theme'].'/fonts/', $file);
            $file = str_replace('../image/', '../theme/'.$cfg_profil['theme'].'/image/', $file);
            // Suppression des blancs multiples
            $file = preg_replace('# +#', ' ', $file);
            // Suppression des tabulations et des nouvelles lignes
            $file = str_replace(array("\n\r", "\r\n", "\r", "\n", "\t"), '', $file);
            // Suppression des commentaires
            $file = preg_replace('~/\*(?s:.*?)\*/|^\s*//.*~m', '', $file);
            // Traitement des "espace , espace", "espace ; espace", des "espace : espace", des "espace {"
            $file = str_replace(array(' ,',', ',' , '), ',', $file);
            $file = str_replace(array(' ;','; ',' ; '), ';', $file);
            $file = str_replace(array(' :',': ',' : '), ':', $file);
            $file = str_replace(array(' {','{ ',' { '), '{', $file);
            $file = str_replace(array(' }','} ',' } '), '}', $file);
            // Traitement des 0px vers 0
            $file = str_replace(array(': 0px',':0px'), ':0', $file);
            $file = str_replace(' 0px', ' 0', $file);
        }

        file_put_contents($filename, $file);
    }

    if($type=='css') {
        $html.="\t<link rel=\"stylesheet\" href=\"".CFG_PATH_HTTP_WEB."/cache/".$crc32.'.'.$type."\" type=\"text/css\" />\n";
    }
    elseif($type=='js') {
        $html.= "\t<script type=\"text/javascript\" src=\"".CFG_PATH_HTTP_WEB."/cache/".$crc32.'.'.$type."\"></script>\n";
        /*
        $html.= "\t<script type=\"text/javascript\"><!--\n";
        $html.= "\t    var script = document.createElement('script');\n";
        $html.= "\t    script.src = '".CFG_PATH_HTTP_WEB."/cache/".$crc32.'.'.$type."';\n";
        $html.= "\t    script.type = 'text/javascript';\n";
        $html.= "\t    document.getElementsByTagName('head')[0].appendChild(script);\n";
        $html.= "\t// --></script>\n";
        */

    }
    return $html;
}

/*
 * Fonction load_profil($session_user_id)
 * -----
 * Chargement du profil d'un utilisateur d'après son id
 * -----
 * @param   int         $session_user_id        id de l'utilisateur
 * @global  string      $db                     instance de connexion SGBD
 * @global  array       $cfg_profil             tableau associatif du profil
 * -----
 * @return  array                               tableau associatif du profil
 * -----
 * $Author: armel $
 * $Copyright: GLOBALIS media systems $
 */

function load_profil($session_user_id) {
    global $db;
    global $cfg_profil;

    $sql = 'SELECT * FROM '.CFG_TABLE_USER.' WHERE user_id='.$db->qstr($session_user_id);

    $recordset = $db->execute($sql);

    if(!$recordset) {
        foreach($cfg_profil as $key => $value)
            $tmp["$key"]=$value;
    }
    else {
        $row = $recordset->fetchrow();
        foreach($cfg_profil as $key => $value)
            //$tmp["$key"]=htmlentities($row["$key"], ENT_COMPAT | ENT_HTML5, 'UTF-8');
            $tmp["$key"]=htmlentities($row["$key"], ENT_COMPAT, 'UTF-8');
    }

    return  $tmp;
}

/*
 * Fonction get_user()
 * -----
 * Permet de lister les utilisateurs connecté
 * -----
 * @param   array           $output             le tableau de données de sortie (doit pointer sur des clefs retournées par $session-user())
 * @param   boolean         $print              le mode de sortie (TRUE par défaut, sinon retour du flux)
 * @param   string          $titre              le titre eventuel
 * -----
 * @return  string								le flux html
 * -----
 * $Author: armel $
 * $Copyright: GLOBALIS media systems $
 */

function get_user($output, $print=TRUE, $titre='') {
    global $session;

    $tmp=$session->user();

    if(!empty($tmp)) {
        $flux = '';
        $flux.= "<div class=\"well\">\n";

        foreach($tmp as $value) {
            $flux.="<p>";
            foreach($output as $data) {
                $flux.=$value[$data].' ';
            }
            $flux.="</p>\n";
        }
        $flux.= "</div>\n";
        if($titre!='')
            $flux='<h3>'.$titre.'</h3>'.$flux;
        if($print===TRUE)
            echo $flux;
        else
            return $flux;
    }
}

/*
 * Fonction get_theme()
 * -----
 * Permet de charger le tableau des themes disponibles
 * -----
 *
 * -----
 * @return  array								le tableau
 * -----
 * $Author: armel $
 * $Copyright: GLOBALIS media systems $
 */

function get_theme() {
    $data=array();
    $dir=scandir(CFG_PATH_FILE_WEB.'/theme/');

    foreach($dir as $v) {
        if($v[0]!='.' && is_dir(CFG_PATH_FILE_WEB.'/theme/'.$v))
            $data[$v] = trim($v);
    }
    return $data;
}

/*
 * Fonction get_file_info($path)
 * -----
 * Permet de récuppérer les infos liées à un fichier : taille, poids, etc.
 * -----
 * @param   string      $path                   le chemin complet du fichier
 * -----
 * @return  array                               le tableau des informations
 * -----
 * $Author: Carine $
 * $Copyright: GLOBALIS media systems $
 */

function get_file_info($path) {
    if(file_exists($path))
    {
        $size=(int) filesize($path);
        $return['extension']=substr(strrchr($path,'.'), 1);
        $return['size']=(int) ($size/1024);

        // Si c'est une image, on va également rechercher sa largeur et sa hauteur
        if (in_array($return['extension'],array('gif','jpg','png','bmp')))
        {
            $dimension=getimagesize($path);
            if(is_array($dimension))
            {
                    $return['width']=(int) $dimension[0];
                    $return['height']=(int) $dimension[1];
            }
            else
            {
                    $return['width']=FALSE;
                    $return['height']=FALSE;
            }
        }
    }
    else
    {
        $return=FALSE;
    }
    return $return;
}
/*
 * Fonction form($data, $edit)
 * -----
 * Construction de la brique form
 * Cette fonction sert à merger les données par défauts
 * avec celles éventuellement en base ou contenues dans $_POST
 * Cette fonction sert également à inclure la lib form (par double bond)
 * -----
 * @param   array       $data                   la structure des données par défaut
 * @param   array       $edit                   la structure des données éventuellement en base
 * -----
 * @param   array       $data                   la structure des données mergées
 * -----
 * $Author: armel $
 * $Copyright: GLOBALIS media systems $
 */

function form($data, $edit) {
    require_once 'lib_form.php';           // Lib de gestion des formulaires
    require_once 'lib_test.php';      	    // Lib de gestion des tests unitaires

    if(empty($_POST) && !empty($edit))
        $data = array_merge($data, $edit);
    else
        $data = array_merge($data, $_POST);

    return $data;
}

/*
 * Fonction backoffice($structure)
 * -----
 * Construction de la brique backoffice
 * Cette fonction sert également à inclure la lib backoffice (par double bond)
 * -----
 * @param   array       $structure              la structure
 * @param   mixed       $db                     instance de connexion SGBD ($db par défaut)
 * -----
 * @return  mixed                               le flux HTML (string) ou affichage direct (print) [default]
 * -----
 * $Author: armel $
 * $Copyright: GLOBALIS media systems $
 */

function backoffice($structure, $db=FALSE) {
    require_once 'lib_backoffice.php';     // Lib de gestion des briques backoffice (CRUD)

    global $session;
    if($db==FALSE)
        global $db;

    //
    // Si ['config']['type'] n'existe pas, on l'initialise à print
    //

    if(!isset($structure['config']['type']))
        $structure['config']['type']='print';

    //
    // Si ['config']['ajax'] n'existe pas ou qu'il n'est pas à 'on'
    //

    if (!(isset($_GET['ajax']) && $_GET['ajax']=='on')) {
        if($structure['config']['type']=='string')
            return backoffice_kernel($structure, $db);
        else
            print backoffice_kernel($structure, $db);
    }

    //
    // Sinon
    //

    else {
        if(strstr($_SERVER['REQUEST_URI'], $structure['context']['name'].'_')) {
            // On purge le flux

            ob_clean();

            // Paramétrage de l'entête

            header('Content-Type: text/html; charset=utf-8');

            // On bascule forcément en mode print

            print backoffice_kernel($structure, $db);

            // On ferme tout

            require 'close.php';

            die();
        }
    }
}

/*
 * Fonction get_url($url, $name='', $value='')
 * -----
 * Construction d'une url avec reprise des variables en GET.
 * -----
 * @param   string      $url                    l'URL de base (index.php, etc.)
 * @param   string      $name                   nom eventuel d'une variable passée en GET à modifier (optionnel)
 * @param   string      $value                  valeur a affecter à cette variable (optionnel)
 * -----
 * @return  string                              l'URL
 * -----
 * $Author: armel $
 * $Copyright: GLOBALIS media systems $
 */

function get_url($url, $name='', $value='') {
    global $session;

    $tmp_get='';
    $flag=FALSE;

    if(!empty($_GET)) {
        reset($_GET);

        // Si CFG_SESSION_TRANS en mode url
        // Et
        // Si CFG_SESSION_LEVEL en mode volatile

        if(isset($_GET[CFG_SESSION_NAME]) && CFG_SESSION_TRANS=='url' && (CFG_SESSION_LEVEL&2)==2) {
            $_GET[CFG_SESSION_NAME]=$session->get_session_id();
        }

        // Suite du traitement

        foreach($_GET as $k=>$v) {
            //
            // Protection xss
            //
            strip_tags($v);
            $v=htmlspecialchars($v, ENT_QUOTES);

            if($k==$name) {
                $flag=TRUE;
                $tmp_get.=$k.'='.$value.'&amp;';
            }
            else
                $tmp_get.=$k.'='.$v.'&amp;';
        }
    }

    if(!$flag && $name!='' && $value!='')
        $tmp_get.=$name.'='.$value.'&amp;';

    if($tmp_get!='')
        $tmp_get=$url.'?'.substr($tmp_get,0,-5);
    else
        $tmp_get=$url;

    return $tmp_get;
}

/*
 * Fonction add_upload($data)
 * -----
 * Ajout d'un fichier par upload (pour le moment, le fichier est dans le repertoire temporaire)
 * A noter qu'en enrichissant cette fonction ou en en créant un nouvelle, il est possible, par exemple, de retailler une image, etc.
 * On peut également choisir d'enrichir ou de créer une autre fonction de test_upload
 * -----
 * @param   array       $data                   nom de la variable upload
 * -----
 *
 * -----
 * $Author: armel $
 * $Copyright: GLOBALIS media systems $
 */

function add_upload($data) {

    // Lors de l'étape de test (test_upload), la structure $_FILES a été enrichie
    // Deux clefs ont été ajoutée : rename (fonction de renommage) et path (chemin de stockage final)

    // print_rh($_FILES);

    if(isset($_FILES[$data.'_tmp']['error']) && ($_FILES[$data.'_tmp']['error']==0)) {
        $final_name='';

        // Renommage

        if(function_exists($_FILES[$data.'_tmp']['rename']))
            $final_name=call_user_func($_FILES[$data.'_tmp']['rename'], $data);
        else
            $final_name = strtolower(uniqid('').strrchr($_FILES[$data.'_tmp']['name'], '.'));

        // On supprime eventuellement l'ancien fichier

        if(isset($_POST[$data]) && $_POST[$data]!='')
            unlink($_FILES[$data.'_tmp']['path'].'/'.$_POST[$data]);

        // On déplace le fichier

        move_uploaded_file($_FILES[$data.'_tmp']['tmp_name'], $_FILES[$data.'_tmp']['path'].'/'.$final_name);
        $_POST[$data]=$final_name;
    }
}

/*
 * Fonction del_upload($filename='', $sql='')
 * -----
 * Suppression d'un fichier par upload
 * -----
 * @param   string      $filename               nom du fichier à supprimer (vide par défaut)
 * @param   string      $sql                    requete à jouer en base (vide par défaut)
 * @param   string      $path_file              le chemin fichier (par défaut CFG_PATH_FILE_UPLOAD)
 * @global  string      $db                     instance de connexion SGBD
 * -----
 *
 * -----
 * $Author: armel $
 * $Copyright: GLOBALIS media systems $
 */

function del_upload($filename='', $sql='', $path_file=CFG_PATH_FILE_UPLOAD) {

    global $db;

    // Suppression Fichier

    if($filename!='') @unlink ($path_file.'/'.$filename);

    // Suppression Base

    if($sql!='')    $db->execute($sql);
}

/*
 * Fonction redirect($url)
 * -----
 * Effectue une redirection HTTP ou JS vers l'url spécifiée
 * -----
 * @param   string      $url                    URL de redirection
 * @global  string      $session                instance de session
 * -----
 * @return  void
 * -----
 * $Author: armel $
 * $Copyright: GLOBALIS media systems $
 */

function redirect($url) {
    global $session;
    global $db;

    // Si pas de header envoyé (par exemple, mode ob), redirection mode php
    if(!headers_sent()) {
        $url=$session->url($url, FALSE);
        $session->close();
        $db->close();

        header('Location: '.$url);
        exit();
    }
    // Sinon, redirection mode JS
    else {
        $url=$session->url($url);
        $session->close();
        $db->close();

        echo '<script type="text/javascript">window.location.href=\''.$url.'\'</script>';
        exit();
    }

    die();
}

/*
 * Fonction date_iso_to($date_iso, $format)
 * -----
 * Convertit une date au format ISO vers un format donné
 * -----
 * @param   string      $date_iso               la date au format ISO
 * @param   string      $format                 le format de conversion (par défaut d-m-Y H:i:s)
 * -----
 * @return  string                              la date dans le format donnée
 * -----
 * $Author: armel $
 * $Copyright: GLOBALIS media systems $
 */

function date_iso_to($date_iso, $format="d-m-Y H:i:s") {
    if(preg_match('#(\d{4})-(\d{2})-(\d{2})(?: (\d{2}):(\d{2}):(\d{2}))?#', $date_iso, $match)){
        $timestamp = mktime(@$match[4], @$match[5], @$match[6], $match[2], $match[3], $match[1]);
        return date($format, $timestamp);
    }
}

/*
 * Fonction date_to_iso($date, $format)
 * -----
 * Convertit une date dans un format donné vers un format ISO
 * -----
 * @param   string      $date                   la date au format donné
 * @param   string      $format                 le format de la date en entrée (par défaut d-m-Y H:i:s)
 * -----
 * @return  string                              la date dans le format donnée
 * -----
 * $Author: armel $
 * $Copyright: GLOBALIS media systems $
 */

function date_to_iso($date, $format="d-m-Y H:i:s") {
    $j = 0;
    for($i = 0; $i < mb_strlen($format,'UTF-8'); $i++){
        switch($format{$i}){
            case 'Y' :  $date_iso['Y'] = $date{$j++};
                        $date_iso['Y'].= $date{$j++};
                        $date_iso['Y'].= $date{$j++};
                        $date_iso['Y'].= $date{$j++};
                        break;
            case 'm' :  $date_iso['m'] = $date{$j++};
                        $date_iso['m'].= $date{$j++};
                        break;
            case 'd' :  $date_iso['d'] = $date{$j++};
                        $date_iso['d'].= $date{$j++};
                        break;
            case 'H' :  $date_iso['H'] = $date{$j++};
                        $date_iso['H'].= $date{$j++};
                        break;
            case 'i' :  $date_iso['i'] = $date{$j++};
                        $date_iso['i'].= $date{$j++};
                        break;
            case 's' :  $date_iso['s'] = $date{$j++};
                        $date_iso['s'].= $date{$j++};
                        break;
            default  :  $j++;
        }
    }
    if(!isset($date_iso['m'])){
        $date_iso['m'] = 1;
    }
    if(!isset($date_iso['d'])){
        $date_iso['d'] = 1;
    }

    $timestamp = mktime(@$date_iso['H'], @$date_iso['i'], @$date_iso['s'], @$date_iso['m'], @$date_iso['d'], @$date_iso['Y']);
    return date('Y-m-d H:i:s', $timestamp);
}

/*
 * Fonction abstract_string($param)
 * -----
 * Permet de couper proprement une chaine trop longue
 * -----
 * @param   array       $param
 *                                              ['string']  => la chaine dont ont veut obtenir un extrait
 *                                              ['end']     => la chaine qui vient completer l'extrait
 *                                              ['length']  => longueur souhaite de l'extrait
 *                                              ['fixed']   => type de césure (si TRUE, l'extrait aura pour longueur length)
 *                                              ['liste']   => liste des caractères pouvant faire office de césure
 * -----
 * @return  string      $string                 la chaine césurée
 * -----
 * $Author: armel $
 * $Copyright: GLOBALIS media systems $
 */

function abstract_string($param) {
    // Recuperation des parametres

    extract($param);

    // Test des parametres

    if(!isset($string)) die("Erreur lors du passage de paramètres");
    if(!isset($end)) $end='...';
    if(!isset($liste)) $liste=array(' ',',',';',"\n","\r",'.');
    if(!isset($length)) $length=30;
    if(!isset($fixed)) $flag=FALSE;

    $string=substr($string, 0, $length);

    if(isset($fixed))
        return $string.$end;

    // Correction Armel (22/03/2004)

    if (mb_strlen($string,'UTF-8') < $length)
        return $string;
    else
        $length = mb_strlen($string,'UTF-8');

    $length--;
    while (!in_array($string[$length],$liste) && $length!==0)
        $length--;

    if($length>0)
        return substr($string, 0, $length+1).$end;
    else
        return $string.$end;
}

/*
 * Fonction delete_accent($chaine)
 * -----
 * Supprime les caractères accentués d'une chaine
 * -----
 * @param   string      $chaine                 la chaine à traiter
 * -----
 * @return  string                              la chaine filtrée de ses accents
 * -----
 * $Author: armel $
 * $Copyright: GLOBALIS media systems $
 */

function delete_accent($chaine) {
    return(utf8_encode(strtr(utf8_decode($chaine),
        utf8_decode('ÀÁÂÃÄÅàáâãäåÒÓÔÕÖØòóôõöøÈÉÊËèéêëÇçÌÍÎÏìíîïÙÚÛÜùúûüÿÑñ'),
        utf8_decode('AAAAAAaaaaaaOOOOOOooooooEEEEeeeeCcIIIIiiiiUUUUuuuuyNn'))));
}

/*
 * Fonction email($to, $subject, $message, $type='text', $header='', $param='', $pj=array())
 * -----
 * Permet d'envoyer un mail
 * En utilisant cette fonction email() plutôt que la fonction native mail()
 * Il sera plus facile de wrapper de nouveaux paramètres, par exemple dans le champ header
 * Afin de contourner les problèmes de messages de spam (par exemple)
 * -----
 * @param   string      $to                     adresse du destinataire
 * @param   string      $subject                sujet
 * @param   string      $message                corps du message
 * @param   string      $type                   le type :
 *                                              - text : mail au format text (par défaut)
 *                                              - html : mail au format html
 * @param   string      $header                 header specifique (par defaut, From, Reply-To et X-Mailer seront renseignés)
 * @param   string      $param                  paramètre(s) optionnel(s)
 * @param   string      $pj                     tableau de pièce(s) jointe(s) éventuelle(s)
 * -----
 * @return  void
 * -----
 * $Author: arnaud $
 * $Copyright: GLOBALIS media systems $
 */
function email($to, $subject, $message, $type='text', $header='', $param='', $pj = array()) {
    // Interception de l'email si la constante CFG_EMAIL_INTERCEPTION est définie et non vide dans la config
    if (defined('CFG_EMAIL_INTERCEPTION') && !empty(CFG_EMAIL_INTERCEPTION) && filter_var(CFG_EMAIL_INTERCEPTION, FILTER_VALIDATE_EMAIL)) {
        $to = CFG_EMAIL_INTERCEPTION;
    }

    // Vérification de l'existence des pièces jointes
    if(!empty($pj)){
        $tmp = $pj;
        unset($pj);

        foreach($tmp as $file){
            if(file_exists($file) && is_readable($file)){
                $pj[] = $file;
            }
        }
    }

    /* Ajout de Lionel pour gérer l'utf8 */

    $charset = 'iso-8859-1';
    // Vérification de l'usage d'utf8
    if(mb_detect_encoding($subject . $message, 'UTF-8', true)) {
        $charset = 'utf-8';
    }

    // S'il y a des caractères spéciaux dans le sujet, on l'encode
    if(preg_match_all('/[\000-\010\013\014\016-\037\177-\377]/', $subject, $matches))
        $subject = " =?$charset?B?".base64_encode($subject)."?=";

    /* Fin de l'ajout */

    if($header == '') {
        $header = 'From: '.CFG_EMAIL."\r\n";
        $header.= 'Reply-To: '.CFG_EMAIL."\r\n";
        $header.= 'X-Mailer: '.CFG_TITRE.' '.CFG_VERSION.' - PHP ' . phpversion()."\r\n";
    }

    $header.= 'MIME-Version: 1.0'."\r\n";

    if(!empty($pj)){
        $boundary = md5(uniqid(rand(), TRUE));
        $header.= 'Content-Type: multipart/mixed; boundary="'.$boundary.'";'."\r\n";
        //$header.= 'Content-Transfer-Encoding: 7bit'."\r\n";

        $body = '--'.$boundary."\r\n";
        if($type == 'text'){
            $body.= 'Content-Type: text/plain; charset="'.$charset.'"'."\r\n";
        }elseif($type == 'html'){
            $body.= 'Content-Type: text/html'."\r\n";
        }
        $body.= 'Content-Transfer-Encoding: 7bit'."\r\n";
        //$body.= 'Content-Transfer-Encoding: quoted-printable'."\r\n";
        $body.= "\r\n";
        $body.= $message."\r\n";
        $body.= '--'.$boundary."\r\n";
        for($i = 0; $i < count($pj); $i++){
            if(function_exists('mime_content_type')){
                $content_type = mime_content_type($pj[$i]);
            }else{
                $content_type = 'application/octet-stream';
            }
            $body.= 'Content-Type: '.$content_type.'; name='.basename($pj[$i])."\r\n";
            $body.= 'Content-Transfer-Encoding: base64'."\r\n";
            $body.= 'Content-ID: image'.md5(uniqid(rand(), TRUE))."\r\n";
            $body.= "\r\n";
            $body.= chunk_split(base64_encode(file_get_contents($pj[$i])))."\r\n";
            $body.= '--'.$boundary;
            if($i == count($pj) - 1){
                $body.= '--';
            }
            $body.= "\r\n";
        }
    }elseif($type == 'text'){
        $header.= 'Content-Type: text/plain; charset="'.$charset.'"'."\r\n";
        //$header.= 'Content-Transfer-Encoding: 7bit'."\r\n";
        //$header.= 'Content-Transfer-Encoding: quoted-printable'."\r\n";
        $body = $message;
    }elseif($type == 'html'){
        $header.= 'Content-Type: text/html; charset="'.$charset.'"'."\r\n";
        //$header.= 'Content-Transfer-Encoding: 7bit'."\r\n";
        //$header.= 'Content-Transfer-Encoding: quoted-printable'."\r\n";
        $body = $message;
    }
    $header.= 'Content-Transfer-Encoding: 7bit'."\r\n";

    //echo $header;
    //echo $body, "\n\n";

    // Envoi du message
    mail($to, $subject, $body, $header, $param);
}

/*
 * Fonction utf8_encode_mixed($param, $encode_key=FALSE)
 * -----
 * Permet d'encoder un tableau (et eventuellement les clés) en UTF8
 * -----
 * @param   mixed       $param                  la valeur à convertir
 * @param   boolean     $encode_key             si c'est un tableau, les clés doivent-elles être converties
 * -----
 * @return  mixed       $result                 la valeur convertie
 * -----
 * $Author: armel $
 * $Copyright: GLOBALIS media systems $
 */

function utf8_encode_mixed($param, $encode_key=FALSE) {
   if(is_array($param)) {
        $result = array();
        foreach($param as $k => $v) {
            $key = ($encode_key)? utf8_encode($k) : $k;
            $result[$key] = utf8_encode_mixed( $v, $encode_key);
        }
    }
    else
    {
        $result = utf8_encode($param);
    }

    return $result;
}

/*
 * Fonction utf8_decode_mixed($param, $decode_key=FALSE)
 * -----
 * Permet de decoder un tableau (et eventuellement les clés) en UTF8
 * -----
 * @param   mixed       $param                  la valeur à convertir
 * @param   boolean     $encode_key             si c'est un tableau, les clés doivent-elles être converties
 * -----
 * @return  mixed       $result                 la valeur convertie
 * -----
 * $Author: armel $
 * $Copyright: GLOBALIS media systems $
 */

function utf8_decode_mixed($param, $decode_key=FALSE) {
   if(is_array($param)) {
        $result = array();
        foreach($param as $k => $v) {
            $key = ($decode_key)? utf8_decode($k) : $k;
            $result[$key] = utf8_decode_mixed( $v, $decode_key);
        }
    }
    else
    {
        $result = utf8_decode($param);
    }

    return $result;
}

/*
 * Fonction check_acl()
 * -----
 * Permet de vérifier l'accès à la ressource URL
 * -----
 *
 * -----
 * @return  bool                                TRUE en cas de succès, FALSE dans le cas contraire
 * -----
 * $Author: armel $
 * $Copyright: GLOBALIS media systems $
 */

function check_acl() {

    global $navigation;
    global $cfg_profil;

    $url=parse_url(CFG_PATH_HTTP);
    $script=(!empty($url['path']))?str_replace($url['path'], '', $_SERVER['SCRIPT_NAME']):$_SERVER['SCRIPT_NAME'];
    //$script=str_replace($url['path'], '', $_SERVER['SCRIPT_NAME']);
    $url=CFG_PATH_HTTP.$script;

    foreach($navigation as $k => $v) {
        if(strstr($v['url'], $url)) {

            if($v['acl']=='')
                return TRUE;
            else {
                $acl_user=explode('|',$cfg_profil['acl']);
                $acl_rubrique=explode('|',$v['acl']);

                $intersection=array_intersect($acl_rubrique, $acl_user);

                if(empty($intersection))
                    return FALSE;
                else
                    return TRUE;
            }
        }
    }

    return TRUE;
}

/*
 * Fonction check_get($acl, $return=TRUE)
 * -----
 * Permet de vérifier l'accès à la ressource GET
 * -----
 * @param   string       $acl                   $acl de l'utilisateur
 * @param   bool                                TRUE par défaut, si FALSE retourne le tableau des actions autorisées
 * -----
 * @return  bool                                TRUE en cas de succès, FALSE dans le cas contraire
 * -----
 * $Author: armel $
 * $Copyright: GLOBALIS media systems $
 */

function check_get($acl, $return=TRUE) {

    global $navigation;

    $url=parse_url(CFG_PATH_HTTP);
    $script=(!empty($url['path']))?str_replace($url['path'], '', $_SERVER['SCRIPT_NAME']):$_SERVER['SCRIPT_NAME'];
    //$script=str_replace($url['path'], '', $_SERVER['SCRIPT_NAME']);
    $url=CFG_PATH_HTTP.$script;

    // Capture de l'entrée URL dans le tableau de navigation

    foreach($navigation as $k => $v) {
        if(strstr($v['url'], $url)) {
            if(isset($v[$acl]['get'])) {
                $get=$v[$acl]['get'];
                break;
            }
        }
    }

    // Cas ou l'on retourne qu'un booleen

    if($return==TRUE) {

        // Si pas d'ACL, on retourne TRUE,
        // Sinon, on construit un tableau avec les données en GET à purger

        if(empty($get))
            return TRUE;
        else
            $clean=array();

        foreach($get as $a => $b) {
            $b=str_replace(',', '|', $b);
            $tmp=explode('|', $b);
            if(isset($_GET[$a]) && !in_array($_GET[$a], $tmp)) {
                $clean[]=$a;
            }
        }

        // Si le tableau avec les données en GET à purger est vide, on retourne TRUE,
        // Sinon, on purge toutes les données avant de retourner TRUE

        if(empty($clean))
            return TRUE;
        else {
            foreach($get as $a => $b) {
                unset($_GET[$a]);
            }
            return TRUE;
        }
    }

    // Cas ou l'on retourne le tableau des actions autorisées (utile pour la brique BO)

    else {
        $return=array();

        if(empty($get))
            return $return;
        else
            return $get;

    }
}

/*
 * Fonction check_post($acl, $return=TRUE)
 * -----
 * Permet de vérifier l'accès à la ressource POST
 * -----
 * @param   string       $acl                   $acl de l'utilisateur
 * @param   bool                                TRUE par défaut, si FALSE retourne le tableau des actions autorisées
 * -----
 * @return  bool                                TRUE en cas de succès, FALSE dans le cas contraire
 * -----
 * $Author: armel $
 * $Copyright: GLOBALIS media systems $
 */

function check_post($acl, $return=TRUE) {

    global $navigation;

    $url=parse_url(CFG_PATH_HTTP);
    $script=(!empty($url['path']))?str_replace($url['path'], '', $_SERVER['SCRIPT_NAME']):$_SERVER['SCRIPT_NAME'];
    //$script=str_replace($url['path'], '', $_SERVER['SCRIPT_NAME']);
    $url=CFG_PATH_HTTP.$script;

    // Trim du POST

    foreach($_POST as $k => $v) {
        if(!is_array($v))
             $_POST[$k] = trim($v);
    }

    // Capture de l'entrée URL dans le tableau de navigation

    foreach($navigation as $k => $v) {
        if(strstr($v['url'], $url)) {
            if(isset($v[$acl]['post'])) {
                $post=$v[$acl]['post'];
                break;
            }
        }
    }

    // Cas ou l'on retourne qu'un booleen

    if($return==TRUE) {

        // Si pas d'ACL, on retourne TRUE,
        // Sinon, on construit un tableau avec les données en GET à purger

        if(empty($post))
            return TRUE;
        else
            $clean=array();

        $tmp=explode('|', $post);
        foreach($_POST as $a => $b) {
            if(!in_array($a, $tmp)) {
                $clean[]=$a;
            }
        }

        // Si le tableau avec les données en GET à purger est vide, on retourne TRUE,
        // Sinon, on purge les données avant de retourner TRUE

        if(empty($clean))
            return TRUE;
        else {
            foreach($clean as $a => $b) {
                unset($_POST[$b]);
            }
            return TRUE;
        }
    }

    // Cas ou l'on retourne le tableau des actions autorisées (utile pour la brique BO)

    else {
        $return=array();

        if(empty($post))
            return $return;
        else
            return $post;

    }
}

/*
 * Fonction check_bo($acl, $ressource)
 * -----
 * Permet de vérifier l'accès à la ressource data ou filtre (brique backoffice)
 * -----
 * @param   string       $acl                   $acl de l'utilisateur
 * @param   string       $ressoure              $ressource a controler ('data' ou 'filtre')
 * -----
 * @return  array                               Tableau des datas autorisées
 * -----
 * $Author: armel $
 * $Copyright: GLOBALIS media systems $
 */

function check_bo($acl, $ressource) {

    global $navigation;

    $url=parse_url(CFG_PATH_HTTP);
    $script=(!empty($url['path']))?str_replace($url['path'], '', $_SERVER['SCRIPT_NAME']):$_SERVER['SCRIPT_NAME'];
    //$script=str_replace($url['path'], '', $_SERVER['SCRIPT_NAME']);
    $url=CFG_PATH_HTTP.$script;

    // Capture de l'entrée URL dans le tableau de navigation

    $result = array();

    foreach($navigation as $k => $v) {
        if(strstr($v['url'], $url)) {
            if(isset($v[$acl][$ressource])) {
                $result=$v[$acl][$ressource];
                break;
            }
        }
    }

    return $result;
}

/*
 * Fonction clean_string($string, $strip_tags=TRUE)
 * -----
 * Permet de nettoyer une chaine
 * -----
 * @param   string       $string                la chaine à nettoyer
 * @param   string       $strip_tags            TRUE (par défaut) ou FALSE
 * -----
 * @return  string                              la chaine nettoyée
 * -----
 * $Author: armel $
 * $Copyright: GLOBALIS media systems $
 */

function clean_string($string, $strip_tags=TRUE) {
    if ($strip_tags)
        return htmlspecialchars(strip_tags($string), ENT_QUOTES);
    else
        return htmlspecialchars($string, ENT_QUOTES);
}

/*
 * Fonction message($libelle, $message, $css='error', $type='normal', $selecteur=TRUE)
 * -----
 * Affichage de messages
 * -----
 * @param   string       $libelle               le titre
 * @param   midex        $message               le message ou le tableau de messages
 * @param   string       $css                   la css d'affichage: error (par défaut), warning, info ou success
 * @param   string       $type                  le type de message: normal (par défaut), modal ou session
 * @param   boolean      $selecteur             colorisation des labels: TRUE (par défaut) ou FALSE
 * -----
 * @return  string                              le flux HTML
 * -----
 * $Author: armel $
 * $Copyright: GLOBALIS media systems $
 */

function message($libelle, $message, $css='danger', $type='normal', $selecteur=TRUE) {

    global $session;

    $flux='';
    $tmp='';
    $jquery='';
    $growl=array();

    // Si $message est vide

    if(empty($message))
        return FALSE;

    // Si $message est un tableau

    if(is_array($message)) {
        foreach($message as $a => $item) {

            $a = explode(',', $a);
            if(is_array($a)) {
                foreach($a as $label) {
                    $jquery.='label[for='.$label.'], ';
                }
            }
            else
                $jquery.='label[for='.$a.'], ';

            if(is_array($item)) {
                foreach($item as $b => $value) {
                    $tmp.='<li>'.$value.'</li>';
                }
            }
            else {
                $tmp.='<li>'.$item.'</li>';
            }
        }
    }

    // Sinon $message est une chaine

    else
        $tmp=$message;

    // Mise en forme de l'affichage

    switch($css) {
        case 'warning':
            $label='warning';
            $color='#f89406';
            break;
        case 'danger':
            $label='danger';
            $color='#b94a48';
            break;
        case 'info':
            $label='info';
            $color='#3a87ad';
            break;
        case 'success':
            $label='success';
            $color='#468847';
            break;
    }

    if($tmp !='') {
        switch($type) {
            case 'session':
                $growl=$session->get_var('growl');
                $growl[]=array(
                    'css'=>$css,
                    'label'=>$label,
                    'color'=>$color,
                    'libelle'=>$libelle,
                    'message'=>$message,
                    );
                $session->register('growl', $growl);
                return;
                break;
            case 'modal':
                $flux.="\n\n<div class=\"alert alert-".$css." modal shown\">\n";
                break;
            case 'normal':
                $flux.="\n\n<div class=\"alert alert-".$css."\">\n";
                break;
        }
    }

    $flux.="<a class=\"close\" data-dismiss=\"alert\" href=\"#\">&times;</a>\n";
    $flux.="<span class=\"label label-".$label."\">".$libelle."</span>\n";
    if(substr($tmp,0,4)=='<li>')
        $flux.="<ul>\n".$tmp."</ul>";
    else
        $flux.="<p>\n".$tmp."</p>\n";
    $flux.="\n</div>\n\n";

    // Colorisation des labels de champs

    if($selecteur && $jquery!='') {
        $flux.= "
        <script type=\"text/javascript\"><!--
        $(document).ready(function() {
            $('".substr($jquery, 0, -2)."').css('color', '".$color."');
        });
        // --></script>
        ";
    }

    return $flux;
}

/*
 * Fonction growl()
 * -----
 * Ajout du js de gestion des messages en session (growl)
 * -----
 *
 * -----
 *
 * -----
 * $Author: armel $
 * $Copyright: GLOBALIS media systems $
 */

function growl() {
    global $session;

    $growl=$session->get_var('growl');

    if(!empty($growl)) {
        foreach($growl as $key => $value) {
            $tmp='';

           // Si $message est un tableau

            if(is_array($value['message'])) {
                foreach($value['message'] as $keybis => $valuebis) {
                    if(is_array($valuebis)) {
                        foreach($valuebis as $keyter => $valueter) {
                            $tmp.='<li>'.$valueter.'</li>';
                        }
                    }
                    else {
                        $tmp.='<li>'.$valuebis.'</li>';
                    }
                }
                $tmp="<ul>".$tmp."</ul>";
            }

            // Sinon $message est une chaine

            else
                $tmp="<p>".$value['message']."</p>";

            echo '
                <script type="text/javascript" src="'.CFG_PATH_HTTP_WEB.'/js/growl/bootstrap.growl.min.js"></script>
                <script type="text/javascript"><!--
                    $.bootstrapGrowl("<span class=\"label label-'.$value['label'].'\">'.$value['libelle'].'</span>'.$tmp.'", {
                      ele: "body",
                      type: "'.$value['css'].'",
                      offset: {from: "top", amount: 20},
                      align: "right",
                      width: "auto",
                      delay: 5000,
                      allow_dismiss: true,
                      stackup_spacing: 10
                    });
                // --></script>
            ';
        }
        $session->unregister('growl');
    }
}
?>
