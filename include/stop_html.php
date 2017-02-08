<?php
//
// Ajout du js de gestion des messages en session (growl)
//

echo growl();

//
// Chargement du stop_html.php 
//

require dirname(__FILE__).'/../web/theme/'.$cfg_profil['theme'].'/stop_html.php';

//
// Affichage de la debug bar
//

if(CFG_DEBUG)
    echo debug_print_console(array());
?>