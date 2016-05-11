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

if(!isset($arg['file']) || sizeof($arg['file'])!=1)
    $arg=FALSE;
else {
    if(!file_exists($arg['file'][0]))
        die("Undefined source file\n");
    else {
        if(substr($arg['file'][0], -1)=='/')
            $source=substr($arg['file'][0], 0, -1);
        else
            $source=$arg['file'][0];
    }
}

$arg_valide=array();
foreach($cfg_arg['jsmin'] as $list => $label) {
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
            elseif($key=='d' || $key=='debug')
                $debug=TRUE;
        }
    }
}

if(!$arg || $help==TRUE) {
    $flux = '';
    $flux.= "usage : php jsmin.php [OPTION] SOURCE\n";
    $flux.= "Minify SOURCE file and create a new file with _min.js extension\n\n";

    foreach($cfg_arg['jsmin'] as $list => $label) {
        $flux.=$list."\t".$label."\n";
    }

    die($flux);
}

//
// Début de traitement
//

$js=file_get_contents($source);

if($debug) {
    $tmp=mb_strlen($js,'UTF-8');

    $kilo=(int)(($tmp)/(1024));
    $byte=($tmp)%(1024);
    $size='';
    $size.=number_format($kilo, 0, ',', ' ');
    $size.='.';
    $size.=sprintf('%03d', $byte);
    $size.=' Ko';

    echo basename($source).' : '.$size."\n";
}

$jsmin=JSMin::minify($js);

if($debug) {
    $tmp=mb_strlen($jsmin,'UTF-8');

    $kilo=(int)(($tmp)/(1024));
    $byte=($tmp)%(1024);
    $size='';
    $size.=number_format($kilo, 0, ',', ' ');
    $size.='.';
    $size.=sprintf('%03d', $byte);
    $size.=' Ko';

    echo basename($source).' minify : '.$size."\n";
}

file_put_contents(substr($source, 0, -2).'min.js', $jsmin);
?>