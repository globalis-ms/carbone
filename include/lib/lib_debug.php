<?php
/*
 * Fonction debug_convert_memory($value)
 * -----
 * Retourne une taille mémoire en kilo
 * -----
 * @param   int         $value      la valeur a convertir
 * -----
 * @return  string                  la valeur convertie
 * -----
 * $Author: armel $
 * $Copyright: GLOBALIS media systems $
 */

function debug_convert_memory($value) {

    $kilo=(int)(($value)/(1024));
    $byte=($value)%(1024);
    
    $tmp='';

    $tmp.=number_format($kilo, 0, ',', ' ');
    $tmp.='.';
    $tmp.=sprintf('%03d', $byte);
    $tmp.=' Ko';

    return $tmp;
}

/*
 * Fonction debug_get_mysql_version()
 * -----
 * Retourne la version de MySQL
 * -----
 * -----
 * @return  string                  la version
 * -----
 * $Author: armel $
 * $Copyright: GLOBALIS media systems $
 */

function debug_get_mysql_version() {
    global $db;
    global $session;

    if(!$session->get_var('mysql_version')) {
        $sql = 'SELECT VERSION() as mysql_version';  
        $tmp = $db->getone($sql);

        if(strpos($tmp, '-'))
            $tmp = @strstr($tmp, '-', TRUE);

        $session->register('mysql_version', $tmp);

        return $tmp;
    }
    else
        return($session->get_var('mysql_version'));  
}

/*
 * Fonction debug_convert_time($value)
 * -----
 * Retourne un temps en s ou ms
 * -----
 * @param   int         $value      la valeur a convertir
 * -----
 * @return  string                  la valeur convertie
 * -----
 * $Author: armel $
 * $Copyright: GLOBALIS media systems $
 */

function debug_convert_time($value) {

    $tmp=sprintf('%2.3f', $value);
    
    if(substr($tmp, 0, 2)=='0.')
        $tmp=(int)substr($tmp,2).' ms';
    else
        $tmp=$tmp.' s';

    return $tmp;
}

/*
 * Fonction debug_get_database($value)
 * -----
 * Exploitation du fichier de log des requetes SQL
 * -----
 * @param   int         $value      la valeur a convertir
 * @global  mixed       $session    instance de session
 * -----
 * @return  string                  la valeur convertie
 * -----
 * $Author: armel $
 * $Copyright: GLOBALIS media systems $
 */

function debug_get_database() {
    global $session;

    $database=array();

    if(defined('CFG_PATH_FILE_LOG_SQL') && is_readable(CFG_PATH_FILE_LOG_SQL)) {    
        if(isset($session->session_id)) {
        	$filesize = filesize(CFG_PATH_FILE_LOG_SQL);
        	$filesize_seek = $filesize-(8*1024);
        	if($filesize_seek < 0)
        		$filesize_seek=0;
        	$fp = fopen(CFG_PATH_FILE_LOG_SQL, 'r');
        	
        	fseek($fp, $filesize_seek, SEEK_SET);
        
        	$foo=array();
        
        	if ($fp) {
        		while (!feof($fp)) {
        			$foo[]=fgets($fp, 4096);
        		}
        		fclose($fp);
        	}
        
            $foo=array_reverse($foo);
            //$foo=array_map('htmlentities', $foo);
            for($i=0; $i<20; $i++) {
                if(isset($foo[$i]) && strpos($foo[$i], $session->session_id))
                    if(substr($foo[$i], -4)!="---\n")
                        $database[]=str_replace('@'.$session->session_id, '', $foo[$i]);
                    else
                        break;
                else
                    continue;
            }
            unset($foo);
        }
    }
    
    return array_reverse($database);
}

/*
 * Fonction debug_get_info($value)
 * -----
 * Retourne un div avec les infos
 * -----
 * @param   string      $value      les infos à affichier
 * -----
 * @return  string                  le flux
 * -----
 * $Author: armel $
 * $Copyright: GLOBALIS media systems $
 */

function debug_get_info($value) {
    global $session;
            
    $titre=$value;    
    $value=strtolower($value);

    if($value=='get')
        $foo=print_r($_GET, true);
    elseif($value=='post')
        $foo=print_r($_POST, true);
    elseif($value=='cookie')
        $foo=print_r($_COOKIE, true);
    elseif($value=='files')
        $foo=print_r($_FILES, true);
    elseif($value=='server')
        $foo=print_r($_SERVER, true);
    elseif($value=='globals')
        $foo=print_r($GLOBALS, true);
    elseif($value=='session')
        $foo=print_r($session, true);
    elseif($value=='constantes') {
        $foo=get_defined_constants(true);
        $get_defined_constants=$foo['user'];        
        ksort($get_defined_constants);
        $foo=print_r($get_defined_constants, true);
    }
    elseif($value=='fonctions') {
        $foo=get_defined_functions();
        $get_defined_functions=$foo['user'];        
        sort($get_defined_functions);
        $foo=print_r($get_defined_functions, true);
    }
    elseif($value=='classes') {
        $foo=get_declared_classes();
        $get_declared_classes=$foo;        
        sort($get_declared_classes);
        $foo=print_r($get_declared_classes, true);
    }
    elseif($value=='database') {
        $foo=debug_get_database();
        if(empty($foo))
            $foo="Oops ! Pas de requête !";
        else
            $foo=implode('',$foo);
    }
                
    if($foo=="Array\n(\n)\n")
        $foo="Oops ! Pas de donnée";
    
    if($value!='database')    
        $foo=nl2br(str_replace('  ', '&nbsp;', htmlentities($foo, ENT_COMPAT, 'UTF-8')));
    else
        $foo=nl2br(str_replace('  ', '&nbsp;', $foo));
        
    $tmp ='';

    if(isset($_COOKIE['carbone_cookie_debug_info']) && $_COOKIE['carbone_cookie_debug_info']=='debug_info_'.$value)
        $tmp.="<div id=\"debug_info_".$value."\" class=\"alert alert-info debug_info\" style=\"display: yes; width: auto;\">\n";
    else
        $tmp.="<div id=\"debug_info_".$value."\" class=\"alert alert-info debug_info\" style=\"display: none; width: auto;\">\n";

    $tmp.="<button class=\"close\" onclick=\"debug_toggle('debug_info_".$value."'); return false;\">&times;</button>\n";

    $tmp.="<h4 style=\"display: inline-block; cursor: text;\">".$titre."</h4>\n";
    $tmp.="<p style=\"font-size: x-small; line-height:10px; cursor: text;\">".$foo."</p>\n";
    $tmp.="</div>\n";

    return $tmp;
}

/*
 * Fonction debug_print_gonsole($get_defined_vars)
 * -----
 * Fonctionnalité d'aide au debug 
 * Affichage d'une barre d'outils de debug (scope, mémoire, requêtage, process, temps d'exécution)
 * -----
 * @param   string      $get_defined_vars       tableau contenant les variables definie en début de script (variables standards)
 * -----
 * @return  string                              le flux
 * -----
 * $Author: armel $
 * $Copyright: GLOBALIS media systems $
 */

function debug_print_console($get_defined_vars) {

    global $session;
    global $cfg_profil;
    global $time_start;
        
    list($usec, $sec) = explode(" ", microtime());
    $time_end=((float)$usec + (float)$sec);
    
    $page_size=strlen(ob_get_contents());
    
    $database=debug_get_database();

    if($database!='') {    
        $nb_database=sizeof($database);
        if($nb_database>1)
            $nb_database.=' queries';
        else
            $nb_database.= ' querie';

        $sql_time = 0;

        foreach ($database as $k => $v) {
            $sql_time += substr($v, 21, 6);
        }
    }
    else
        $nb_database = '';

    
    $flux = "\n\n<!-- debug -->\n";
    
    $flux.= '   
            <nav class="navbar navbar-fixed-bottom navbar-inverse">
                    <div class="container">
                        <div class="navbar-header">
                            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar-debug">
                                <span class="sr-only">Toggle navigation</span>
                                <span class="icon-bar"></span>
                                <span class="icon-bar"></span>
                                <span class="icon-bar"></span>
                            </button>
                            <a class="navbar-brand" href="#">Debug</a>
                        </div>

                        <div class="collapse navbar-collapse" id="navbar-debug">
                            <ul class="nav navbar-nav">
                                <li class="dropdown">
                                    <a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="glyphicon glyphicon-eye-open"></i> Scope <span class="caret"></span></a>
                                    <ul class="dropdown-menu">
                                        <li><a href="#" onclick="debug_toggle(\'debug_info_get\'); return false;">_GET</a></li>
                                        <li><a href="#" onclick="debug_toggle(\'debug_info_post\'); return false;">_POST</a></li>
                                        <li><a href="#" onclick="debug_toggle(\'debug_info_cookie\'); return false;">_COOKIE</a></li>
                                        <li><a href="#" onclick="debug_toggle(\'debug_info_files\'); return false;">_FILES</a></li>
                                        <li><a href="#" onclick="debug_toggle(\'debug_info_server\'); return false;">_SERVER</a></li>
                                        <li><a href="#" onclick="debug_toggle(\'debug_info_globals\'); return false;">GLOBALS</a></li>
                                        <li class="divider"></li>
                                        <li><a href="#" onclick="debug_toggle(\'debug_info_session\'); return false;">Session</a></li>
                                        <li class="divider"></li>
                                        <li><a href="#" onclick="debug_toggle(\'debug_info_constantes\'); return false;">Constantes</a></li>
                                        <li class="divider"></li>
                                        <li><a href="#" onclick="debug_toggle(\'debug_info_fonctions\'); return false;">Fonctions</a></li>
                                        <li><a href="#" onclick="debug_toggle(\'debug_info_classes\'); return false;">Classes</a></li>
                                    </ul>
                                </li>
                                <li><a href="#" title="Database" onclick="debug_toggle(\'debug_info_database\'); return false;"><i class="glyphicon glyphicon-list"></i> Db '.$nb_database.' - '.debug_convert_time($sql_time).'</a></li>
                                <li class="dropdown">
                                    <a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="glyphicon glyphicon-signal"></i> Mem '.debug_convert_memory(@memory_get_usage()).' <span class="caret"></span></a>
                                    <ul class="dropdown-menu">
                                        <li><a href="#">Code PHP '.debug_convert_memory(@memory_get_usage()).'</a></li>
                                        <li><a href="#">Code PHP (Peak) '.debug_convert_memory(@memory_get_peak_usage()).'</a></li>
                                        <li class="divider"></li>
                                        <li><a href="#">Code HTML '.debug_convert_memory($page_size).'</a></li>
                                    </ul>
                                </li>
                                <li><a href="#" title="Time"><i class="glyphicon glyphicon-time"></i> Time '.debug_convert_time($time_end - $time_start).'</a></li>
                                <li class="dropdown">
                                    <a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="glyphicon glyphicon-wrench"></i> Carbone  '.CFG_VERSION_CARBONE.' <span class="caret"></span></a>
                                    <ul class="dropdown-menu">
                                        <li><a href="#">Carbone '.CFG_VERSION_CARBONE.'</a></li>
                                        <li class="divider"></li>
                                        <li><a href="#">PHP '.PHP_VERSION.'</a></li>
                                        <li><a href="#">MySQL '.debug_get_mysql_version().' ('.CFG_TYPE.')</a></li>
                                    </ul>
                                </li>
                            </ul>
                        </div><!-- /.nav-collapse -->
        
                    </div>
            </nav><!-- /.navbar -->';
    
    $flux.="\n\n<script type=\"text/javascript\" src=\"".CFG_PATH_HTTP_WEB."/js/debug/debug.min.js\"></script>\n\n";

    if(isset($_COOKIE['carbone_cookie_debug_info_left']) && isset($_COOKIE['carbone_cookie_debug_info_top']))
        $flux.="<div id=\"debug_info\" onmouseover=\"document.getElementById('debug_info').style.cursor='pointer'; document.getElementById('debug_info').style.opacity='1.0'; document.getElementById('debug_info').style.filter='alpha(opacity:100)';\" onmouseout=\"document.getElementById('debug_info').style.opacity='0.6'; document.getElementById('debug_info').style.filter='alpha(opacity:60)';\" onmousedown=\"dragStart(event, 'debug_info')\" style=\"display:yes; position:absolute; left:".$_COOKIE['carbone_cookie_debug_info_left']."; top:".$_COOKIE['carbone_cookie_debug_info_top'].";\">";
    else
        $flux.="<div id=\"debug_info\" onmouseover=\"document.getElementById('debug_info').style.cursor='pointer'; document.getElementById('debug_info').style.opacity='1.0'; document.getElementById('debug_info').style.filter='alpha(opacity:100)';\" onmouseout=\"document.getElementById('debug_info').style.opacity='0.6'; document.getElementById('debug_info').style.filter='alpha(opacity:60)';\" onmousedown=\"dragStart(event, 'debug_info')\" style=\"display:yes; position:absolute; left:64px; top:64px;\">";

    //$flux.="<div id=\"debug_info\" onmouseover=\"document.getElementById('debug_info').style.cursor='pointer'; document.getElementById('debug_info').style.opacity='1.0'; document.getElementById('debug_info').style.filter='alpha(opacity:100)';\" onmouseout=\"document.getElementById('debug_info').style.opacity='0.6'; document.getElementById('debug_info').style.filter='alpha(opacity:60)';\" onmousedown=\"dragStart(event, 'debug_info')\" style=\"display:yes; position:absolute; left:50px; top:50px;\">\n";
    
    if(!isset($_GET))
        $_GET=array();
        
    $flux.=debug_get_info('GET');
    
    if(!isset($_POST))    
        $_POST=array();
    
    $flux.=debug_get_info('POST');
    
    $flux.=debug_get_info('COOKIE');
    $flux.=debug_get_info('FILES');
    $flux.=debug_get_info('SERVER');
    $flux.=debug_get_info('GLOBALS');
    
    $flux.=debug_get_info('Session');
    
    $flux.=debug_get_info('Constantes');
    
    $flux.=debug_get_info('Fonctions');
    $flux.=debug_get_info('Classes');
    
    $flux.=debug_get_info('Database');
    
    $flux.="</div>\n";
        
    if($cfg_profil['theme']=='simplex')
        $flux=str_replace('icon-white', 'icon-black', $flux);
    if($cfg_profil['theme']=='flatly' or $cfg_profil['theme']=='simplex')
        $flux=str_replace('navbar-inverse', '', $flux);
        
    return $flux;
}
?>