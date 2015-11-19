<?php
//
// Chargement initiaux
//

require 'local_config.php';
require 'local_lib.php';

//
// VŽrification des usages
//

$arg=argument($argv);

$help=FALSE;
$length=128;

$arg_valide=array();
foreach($cfg_arg['lorem'] as $list => $label) {
    $tmp=explode(',', $list);
    foreach($tmp as $option) {
        $option=str_replace(' ', '', $option);
        $option=str_replace('-', '', $option);
        $option=str_replace('=<length>', '', $option);
        $arg_valide[]=$option;
    }
}

if(isset($arg['argument'])) {
    foreach($arg['argument'] as $key => $value) {
        if(!in_array($key, $arg_valide)) {
            $arg=FALSE;
            echo "ici";
            break;
        }
        else {
            if($key=='h' || $key=='help')
                $help=TRUE;
            elseif($key=='l')
                $length=(int)($value);
        }
    }
}

if($length==1)
    unset($arg);

if(!$arg || $help==TRUE) {
    $flux = '';
    $flux.= "usage : php lorem [OPTION]... \n";
    $flux.= "Lorem Ipsum string generator\n\n";

    foreach($cfg_arg['lorem'] as $list => $label) {
        $flux.=$list."\t".$label."\n";
    }

    die($flux);
}

//
// DŽbut de traitement
//

$generator = new LoremIpsumGenerator;

while($foo=trim($generator->getContent($length, 'txt', false))) {
    $end=$foo[$length-1];
    if($end==' ' || $end==',' || $end=='.') 
        break;
}

$foo=substr($foo, 0, ($length-1));
$foo[$length-1]='.';

$foo=ucfirst($foo);

for($i=0; $i<($length-1); $i++) {
    if($foo[$i]=='.')
        $foo[$i+2]=ucfirst($foo[$i+2]);
}

echo $foo."\n";
?>