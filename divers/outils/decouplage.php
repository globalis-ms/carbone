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
$fo='fo';
$bo='bo';

if(!isset($arg['file']) || sizeof($arg['file'])!=1)
    $arg=FALSE;
else {
    if(!file_exists($arg['file'][0]))
        die("Undefined target directory\n");
    elseif(!is_writable($arg['file'][0]))
        die("Target directory is not writable\n");
    else {
        if(substr($arg['file'][0], -1)=='/')
            $destination=substr($arg['file'][0], 0, -1);
        else
            $destination=$arg['file'][0];
    }
}

$arg_valide=array();
foreach($cfg_arg['decouplage'] as $list => $label) {
    $tmp=explode(',', $list);
    foreach($tmp as $option) {
        $option=str_replace(' ', '', $option);
        $option=str_replace('-', '', $option);
        $option=str_replace('=<name>', '', $option);
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
            elseif($key=='fo')
                $fo=basename($value);
            elseif($key=='bo')
                $bo=basename($value);
        }
    }
}

if(!$arg || $help==TRUE) {
    $flux = '';
    $flux.= "usage : php engine [OPTION]... DIRECTORY\n";
    $flux.= "Install CARBONE in the target DIRECTORY\n\n";

    foreach($cfg_arg['decouplage'] as $list => $label) {
        $flux.=$list."\t".$label."\n";
    }

    die($flux);
}

//
// Début de traitement
//

// Copie des fichiers

$destination_bo=$destination.'/'.$fo;
$destination_fo=$destination.'/'.$bo;

$source=realpath(dirname(__FILE__).'/../../');

// Mise en place du bo

recursive_copy($source, $destination_bo);
delete_directory($destination_bo.'/divers');
delete_directory($destination_bo.'/include');
delete_directory($destination_bo.'/web');

if($debug)
    echo "\nClone $bo: OK\n";

// Mise en place du fo

recursive_copy($source, $destination_fo);
delete_directory($destination_fo.'/divers');
delete_directory($destination_fo.'/include');
delete_directory($destination_fo.'/web');

if($debug)
    echo "\nClone $fo: OK\n";

//
// Patch des .htaccess
//

$bug=0;

// Tentative de calcul du 404

$tmp=substr($_SERVER['PWD'], 0, strpos($_SERVER['PWD'], '/divers/outils'));
$root=dirname($tmp);
$avant='/'.basename($tmp);
$apres=substr($destination, strlen($root));

foreach(array($fo, $bo) as $var) {
    $file=$destination.'/'.$var.'/.htaccess';
    $old=file_get_contents($file);
    $new=$old;
    $check=2;
    $total=0;
    $new=str_replace($avant, $apres, $new, $count);
    $total+=$count;
    $new=str_replace($source, $destination, $new, $count);
    $total+=$count;
    if($total!==$check) {
        $bug++;
        echo "Patch ".$file.": FAIL ($check/$total)\n";
    }
    //else
    //   echo "Patch ".$file.": OK ($check/$total)\n";

    file_put_contents($file, $new);

    if($debug && $bug==0)
        echo "Patch .htaccess in $var: OK\n";
}

// Mise en place du kernel

//recursive_copy($source.'/divers', $destination.'/divers');
recursive_copy($source.'/include', $destination.'/include');
recursive_copy($source.'/web', $destination.'/web');

if($debug)
    echo "\nClone CARBONE: OK\n";

//
// Duplication et patch des scripts
//
// include/open.php
// include/start_php.php
// config/config.php
// config/navigation.php
//

$bug=0;

foreach($decouplage as $file => $replace) {
    foreach(array($fo, $bo) as $var) {
        $old=file_get_contents($destination.str_replace('%s', '', $file));
        $new=$old;
        $check=sizeof($replace);
        $total=0;
        foreach($replace as $key => $value) {
            if($key!='CARBONE')
                $new=str_replace($key, str_replace('%s', $var, $value), $new, $count);
            else
                $new=str_replace($key, str_replace('%s', strtoupper($var), $value), $new, $count);
            $total+=$count;
        }
        if($total!==$check) {
            $bug++;
            echo "Patch ".str_replace('%s', '_'.$var, $file).": FAIL ($check/$total)\n";
        }
        //else
        //    echo "Patch ".str_replace('%s', '_'.$var, $file).": OK ($check/$total)\n";

        file_put_contents($destination.str_replace('%s', '_'.$var, $file), $new);

        if($debug && $bug==0)
            echo "Patch scripts ".str_replace('%s', '_'.$var, $file).": OK\n";
    }
    unlink($destination.str_replace('%s', '', $file));
}

if($debug && $bug==0)
    echo "Patch scripts in include: OK\n";

//
// Patch sur le require('start_php') des fichiers fo/ et bo/
// On ne traite pas les fichier local_config.php, local_lib.php et prenom.php
//

$bug=0;

foreach(array($fo, $bo) as $var) {
    $cmd=CFG_FIND.' '.$destination.'/'.$var.' -type f -name \'*.php\' ! -name \'local_*\' ! -name \'prenom.php\'';
    $result=array();
    exec($cmd, $result);
    //echo $cmd."\n";
    foreach($result as $file) {
        $old=file_get_contents($file);
        $new=$old;
        $check=1;
        $total=0;
        $new=str_replace('require \'start_php.php\';', 'require \'start_php_'.$var.'.php\';', $new, $count);
        $total+=$count;
        if($total!==$check) {
            $bug++;
            echo "Patch ".str_replace($destination, '', $file).": FAIL ($check/$total)\n";
        }
        //else
        //    echo "Patch ".str_replace($destination, '', $file).": OK ($check/$total)\n";

        file_put_contents($file, $new);
    }

    if($debug && $bug==0)
        echo "Patch scripts in $var: OK\n";
}
?>