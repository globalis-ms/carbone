<?php

// Database configuration

define('CFG_CLASS', '$$DB_ABSTRACTION_CLASS$$'); // Classe d'abstraction utilisée
define('CFG_TYPE', '$$DB_TYPE$$'); // Type de sgbd ciblé (mysql, postgres, ...)

define('CFG_HOST', '$$DB_HOST$$'); // Host
define('CFG_USER', '$$DB_USER$$'); // User
define('CFG_PASS', '$$DB_PASSWORD$$'); // Pass
define('CFG_BASE', '$$DB_NAME$$'); // Base

define('CFG_TABLE_PREFIX', $$DB_TABLE_PREFIX$$); // Prefix sur les tables (public. pour postgres)

// Environment configuration

define('CFG_ENV', '$$ENV$$');

switch (CFG_ENV) {
    case 'dev':
        error_reporting(E_ALL);
        define('CFG_DEBUG', true);
        define('CFG_EMAIL_INTERCEPTION', '$$EMAIL_INTERCEPTION$$');
        break;

    case 'rec':
        error_reporting(0);
        define('CFG_DEBUG', false);
        define('CFG_EMAIL_INTERCEPTION', '$$EMAIL_INTERCEPTION$$');
        break;

    case 'prod':
        error_reporting(0);
        define('CFG_DEBUG', false);
        break;

    default:
        error_reporting(E_ALL);
        define('CFG_DEBUG', true);
        define('CFG_EMAIL_INTERCEPTION', '$$EMAIL_INTERCEPTION$$');
}

// Misc configuration

define('CFG_OPTIMISATION_LEVEL', '$$CFG_OPTIMISATION_LEVEL$$'); // Mode Optimisation (champ de bit) : 1 = fewer HTTP request, 2 = JS at the bottom), 3 = both