<?php

define ('CFG_ENV', 'dev');  // Environnement de dev

switch (CFG_ENV) {

    //
    // Environnement de dev
    //

    case 'dev':

        // Level d'erreur

        error_reporting(E_ALL);

        // Database

        define ('CFG_CLASS', 'adodb_carbone');  // Class d'abstraction utilisée
        define ('CFG_TYPE',  'mysqli');         // Type de sgbd ciblé (mysql, postgres, ...)

        define ('CFG_HOST', 'localhost');       // Host
        define ('CFG_USER', 'username');        // User
        define ('CFG_PASS', 'password');        // Pass
        define ('CFG_BASE', 'carbone_v54');     // Base

        // Option

        define ('CFG_EMAIL_INTERCEPTION', '');
        define ('CFG_OPTIMISATION_LEVEL', '0'); // Mode Optimisation (champ de bit) : 1 = fewer HTTP request, 2 = JS at the bottom)
        define ('CFG_DEBUG', TRUE);             // Mode Debug

        break;

    //
    // Environnement de prod
    //

    case 'prod':

        // Level d'erreur

        error_reporting(0);

        // Database

        define ('CFG_CLASS', 'adodb_carbone');  // Class d'abstraction utilisée
        define ('CFG_TYPE',  'mysqli');         // Type de sgbd ciblé (mysql, postgres, ...)

        define ('CFG_HOST', 'localhost');       // Host
        define ('CFG_USER', 'username');        // User
        define ('CFG_PASS', 'password');        // Pass
        define ('CFG_BASE', 'carbone_v54');     // Base

        // Option

        define ('CFG_OPTIMISATION_LEVEL', '3'); // Mode Optimisation (champ de bit) : 1 = fewer HTTP request, 2 = JS at the bottom)
        define ('CFG_DEBUG', FALSE);            // Mode Debug

        break;
}
