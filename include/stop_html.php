<?php
//
// Ajout du js de gestion des messages en session (growl)
//

growl();

//
// Chargement du stop_html.php 
//

require dirname(__FILE__).'/../web/theme/'.$cfg_profil['theme'].'/stop_html.php';

//
// Expérimental : capture des JS et déplacement en fin de page
//

if((CFG_OPTIMISATION_LEVEL&2)==2) {
    $flux=ob_get_contents();
    ob_clean();

    preg_match_all("/<script.*javascript[^>]*>(.*)<\/script>/Uims", $flux, $tmp);
    
    $js='';
    foreach($tmp[0] as $value) {
        $flux=str_replace(trim($value), '', $flux);
        $js.="\n\t".$value;
    }

    $flux=preg_replace("/\t<!--start js-->(.*)\t<!--stop js-->\n/Uims", '', $flux);
    $flux=str_replace('</html>', "    <!--start js-->".$js."\n    <!--stop js-->\n</html>", $flux);
    echo $flux;
}

//
// Affichage de la debug bar
//

if(CFG_DEBUG)
    echo debug_print_console(array());
?>