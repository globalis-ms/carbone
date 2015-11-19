<?php
//
// Définition de constantes
//

define ('CFG_AWK',  '/usr/bin/awk');    // Chemin vers le binaire de awk
define ('CFG_FIND', '/usr/bin/find');   // Chemin vers le binaire de find
define ('CFG_GREP', '/usr/bin/grep');   // Chemin vers le binaire de grep
define ('CFG_WC',   '/usr/bin/wc');     // Chemin vers le binaire de wc
define ('CFG_CP',   '/bin/cp');         // Chemin vers le binaire de cp
define ('CFG_MV',   '/bin/mv');         // Chemin vers le binaire de mv

//
// Liste des arguments autorisés
//

$cfg_arg['langue'] = array (
    '-h, --help'    => 'print this help.',
    '-s, --simple'  => 'check only unused constants.',
    '-f, --full'    => 'check all (unused and used) constants.',
);

$cfg_arg['jsmin'] = array (
    '-h, --help'    => 'print this help.',
    '-d, --debug'   => 'print debugging information.',
);

$cfg_arg['lorem'] = array (
    '-h, --help'    => 'print this help.',
    '-l=<length>'   => 'string length (must be > 1).',
);

$cfg_arg['decouplage'] = array (
    '-h,  --help'           => 'print this help.',
    '-d,  --debug'          => 'print debugging information.',
    '-fo=<name>'            => 'frontoffice directory name (default is \'fo\').',
    '-bo=<name>'            => 'backoffice directory name (default is \'bo\').',
);

$cfg_arg['database'] = array (
    '-h,  --help'       => 'print this help.',
    '-u,  --user'       => 'purge user table',
    '-d,  --data'       => 'purge data table',
    '-s,  --session'    => 'purge session table',
);

//
// Tableau des recherches / remplaces pour le découplage
//

$decouplage=array(
    // Fichier /include/open.php

    '/include/open%s.php' => array(
        'config/navigation.php'                         => 'config/navigation_%s.php',
    ),

    // Fichier /include/start_php.php

    '/include/start_php%s.php' => array(
        'config/config.php'                             => 'config/config_%s.php',
        'open.php'                                      => 'open_%s.php',
    ),

    // Fichier /include/config/navigation.php

    '/include/config/navigation%s.php' => array(),

    // Fichier /include/config/config.php

    '/include/config/config%s.php' => array(
        '.\'/\'));  // Exemple'                         => '.\'/%s/\'));  // Exemple',
        '/../..\'));             // Chemin fichier '    => '/../../%s\'));          // Chemin fichier ',
        'CFG_PATH_FILE.\'/web'                          => 'CFG_PATH_FILE.\'/../web',
        'CFG_PATH_HTTP.\'/web'                          => 'CFG_PATH_HTTP.\'/../web',
        'Carbone'                                       => 'Carbone_%s',
    ),
);
?>