<?php

$git_name = exec('git config user.name');
$git_email = exec('git config user.email');

return [
    // Local Config
    'local' => [
        /*
         * IDENTITY
         */
        'DEV_NAME' => [
            'question' => 'Developer name',
            'default' => $git_name
        ],

        'DEV_MAIL' => [
            'question' => 'Developer email',
            'default' => $git_email
        ],

        /*
         * BINARY PATHS
         */
        'BIN_PHP_PATH' => [
            'question' => 'Path to PHP binary',
            'default' => '/usr/local/bin/php',
        ],
        'BIN_CLOC_PATH' => [
            'question' => 'Path to cloc binary',
            'default' => '/usr/local/bin/cloc',
        ],
        'BIN_ECHO_PATH' => [
            'question' => 'Path to echo binary',
            'default' => '/bin/echo',
        ],
        'BIN_ZIP_PATH' => [
            'question' => 'Path to zip binary',
            'default' => '/usr/bin/zip',
        ],
        'BIN_GIT_PATH' => [
            'question' => 'Path to git',
            'default' => '/usr/local/bin/git'
        ],
    ],

    // Project constant
    'settings' => [
    ],

    // Project configuration
    'config' => [
        /**
         * DATABASE
         */
        'DB_ABSTRACTION_CLASS' => [
            'question' => 'Database abstraction_class',
            'default' => 'adodb_carbone',
        ],
        'DB_TYPE' => [
            'question' => 'Database type',
            'default' => 'mysqli',
        ],
        'DB_HOST' => [
            'question' => 'Database host',
            'default' => 'localhost',
        ],
        'DB_USER' => [
            'question' => 'Database username',
            'default' => 'username',
        ],
        'DB_PASSWORD' => [
            'question' => 'Database password',
            'default' => 'password',
        ],
        'DB_NAME' => [
            'question' => 'Database name',
            'default' => 'carbone_v53',
        ],
        'DB_TABLE_PREFIX' => [
            'question' => 'Database table prefix',
            'default' => '\'\'',
        ],

        /*
         * OTHER CONF
         */
        'ENV' => [
            'question' => 'Environment',
            'choices' => ['dev', 'rec', 'prod'],
            'default' => 'dev'
        ],
        'EMAIL_INTERCEPTION' => [
            'question' => 'Interception email (not used on prod environment)',
            'default' => $git_email
        ],
        'CFG_OPTIMISATION_LEVEL' => [
            'question' => 'Optimisation level (0: nothing, 1: fewer HTTP request, 2: JS at the bottom, 3: both)',
            'choices' => [0, 1, 2, 3],
            'default' => 0
        ],
        'INCLUDE_PATH' => [
            'question' => 'Include file path',
            'default' => '/usr/local/apache/htdocs/carbone_v54/include'
        ],
    ]
];
