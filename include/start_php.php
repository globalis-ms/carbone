<?php
// Passage en mode Output Buffering

ob_start();

// Require optionnels

if(file_exists('local_config.php'))
    require 'local_config.php';
if(file_exists('local_lib.php'))
    require 'local_lib.php';

// Require obligatoires
require 'config/config.php';            // Fichier de configuration de l'environnement
require 'config/setting.php';           // Fichier de configuration de l'application
require 'lib/lib_carbone.php';          // Librairie Carbone
require 'lib/class_db.php';             // Class Abstraction SGBD
require 'lib/class_session.php';        // Class Session
require 'open.php';                     // Ouverture SGBD et Session
?>
