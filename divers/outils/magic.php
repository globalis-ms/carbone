<?php
//
// On n'exécute la boucle que si nécessaire
//

if(get_magic_quotes_gpc()) {
    //
    // Définition de la fonction récursive
    //
    function remove_magic_quotes(&$array) {
        foreach($array as $key => $val) {

            //
            // Si c'est un tableau, appel récursif de la fonction
            // Sinon, suppression des slashes
            //

            if(is_array($val))
                remove_magic_quotes($array[$key]);
            else if(is_string($val))
                $array[$key] = stripslashes($val);
        }
    }

    //
    // Appel de la fonction pour chaque variables.
    // Notes, vous pouvez enlevez celle d'on vous ne vous servez pas.
    // Personnellement, j'enlève $_REQUEST et $_FILES
    //

    remove_magic_quotes($_POST);
    remove_magic_quotes($_GET);

    // remove_magic_quotes($_REQUEST);
    // remove_magic_quotes($_FILES);
    // remove_magic_quotes($_SERVER);
    // remove_magic_quotes($_COOKIE);
}
?>