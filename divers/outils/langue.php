<?php
//
// Chargement initiaux
//

require 'local_config.php';
require 'local_lib.php';

//
// Vérification des usages
//

$arg=argument($argv);

$help=FALSE;
$debug=FALSE;

$param=array(
    'racine'=>'../..',
    'source'=>'../../include/langue/langue_fr.php'
);

$arg_valide=array();
foreach($cfg_arg['langue'] as $list => $label) {
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
            elseif($key=='s' || $key=='simple')
                $simple=TRUE;
            elseif($key=='f' || $key=='full')
                $full=TRUE;
        }
    }
}

if(!$arg || $help==TRUE) {
    $flux = '';
    $flux.= "usage : php langue.php [OPTION]\n";
    $flux.= "Check unused constants in lang file\n\n";

    foreach($cfg_arg['langue'] as $list => $label) {
        $flux.=$list."\t".$label."\n";
    }

    die($flux);
}

//
// Début de traitement
//

function parse($param) {
    extract($param);

    // Chargement du fichier à analyser
    $file=file_get_contents($source);
    // Boucle principale
    $search="/define\(\'(.*?)\',(.*?)\)/msi";
    preg_match_all($search, $file, $result);
    $full=array();
    $empty=array();
    reset($result);

    // On filtre les constantes à problème : à compléter si besoin !

    $white_list = array (
        'STR_BACKOFFICE_EXPORT_FORMAT_PDF',
        'STR_BACKOFFICE_EXPORT_FORMAT_EXCEL',
        'MY_ASS',
    );

    foreach($white_list as $value) {
        $key=array_search($value, $result[1]);
        if($key!=FALSE)
            unset($result[1][$key]);
    }

    // Boucle principale

    foreach($result[1] as $constant) {
        echo ".";
        $find=array();
        $exec=sprintf(CFG_FIND.' %s -name "*.php" -type f ! -size 0 -exec '.CFG_GREP.' -l \'%s\' {} \;', $racine, $constant);
        exec($exec, $find);
        // On filtre ici certains fichiers dans lequel la chaine peut-être trouvée par erreur
        while(list($k, $v)=each($find)) {
            if(strstr(basename($v), 'langue_') || strstr(basename($v), '.doc') || strstr($v, '/outil/'))
                unset($find[$k]);
        }
        reset($find);
        if(sizeof($find)==0) {
            $empty[$constant]='&nbsp;';
        }
        else {
            while(list($k, $v)=each($find)) {
                @$full[$constant].=$v.", ";
            }
            @$full[$constant]=substr(@$full[$constant], 0, -2);
        }
    }
    return array(
        'empty' => $empty,
        'full' => $full
    );
}

$ret = parse($param);
echo "\nUnused constants in lang file: ";
echo sizeof($ret['empty'])."\n";
foreach(array_keys($ret['empty']) as $key) {echo $key."\n";}
if (isset($full)) {
    echo "\n\n";
    print_r($ret['full']);
}
?>