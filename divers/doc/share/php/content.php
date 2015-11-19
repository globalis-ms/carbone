<?php
function php4_scandir($dir,$listDirectories=false, $skipDots=true) {
    $dirArray = array();
    if ($handle = opendir($dir)) {
        while (false !== ($file = readdir($handle))) {
            if (($file != "." && $file != "..") || $skipDots == true) {
                if($listDirectories == false) { if(is_dir($file)) { continue; } }
                array_push($dirArray,basename($file));
            }
        }
        closedir($handle);
    }
    return $dirArray;
}

define('ROOT', realpath(dirname($_SERVER['SCRIPT_FILENAME']).'/../structure/'));

if(stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml"))
    header("Content-type: application/xhtml+xml");
else
    header("Content-type: text/xml");

echo("<?xml version=\"1.0\" encoding=\"iso-8859-1\"?>\n");
echo("<data>");

// Initialisation de la racine

if(!isset($_GET['root']))
    $root=ROOT;
else
    $root=realpath(ROOT.$_GET['root']);

if($root < ROOT) 
    $root=ROOT;

// Initialisation du chemin

if(!isset($_GET['path']))
    $path=realpath($root);
else
    $path=realpath(ROOT.$_GET['path']);

if($path < $root)
    $path=$root;

echo $path.' ---- '.$root;

$directory = php4_scandir($path, true);

if($directory!=false && count($directory)>0){

    // Ordonancement répertoire / fichiers

    $tmp_dir=array();
    $tmp_file=array();

    foreach($directory as $file) {
        if(($path==$root && ($file!='.' && $file!='..')) || ($path!=$root && ($file!='.'))) {
            $file_full_path = $path."/".$file;
            if(is_dir($file_full_path))
                $tmp_dir[]=$file;
            else
                $tmp_file[]=$file;
        }
    }

    sort($tmp_dir);
    sort($tmp_file);

    $directory=array_merge($tmp_dir, $tmp_file);

    // Boucle de construction du flux XML

    foreach($directory as $file) {
        $file_full_path = $path."/".$file;
        $mtimeArray = getdate(filemtime($file_full_path));
        //file size in bytes
        $fsize = filesize($file_full_path);
        //format date yyyy-m-d hh:mm:ss
        $fdate = sprintf("%04s-%02s-%02s %02s-%02s-%02s", $mtimeArray["year"],$mtimeArray["mon"],$mtimeArray["mday"],$mtimeArray["hours"],$mtimeArray["minutes"],$mtimeArray["seconds"]);
        //extension
        $fext = pathinfo($file,PATHINFO_EXTENSION);
        //item ID
        if(is_dir($file_full_path))
            $ftype='dir';
        else
            $ftype='file';
        //output item
        print("<item name='".$file."' type='".$ftype."' ext='".$fext."'>\n");
        print("<filesize>".$fsize."</filesize>\n");
        print("<modifdate>".$fdate."</modifdate>\n");
        print("</item>\n");
    }
}
echo("</data>");
?>