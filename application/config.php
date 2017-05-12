<?php

$config = [
    'SITE_NAME' => 'MVC каркас',

    'ROOT_DIR'       => $_SERVER['DOCUMENT_ROOT'],
    'APP_DIR'        => $_SERVER['DOCUMENT_ROOT'] . '/application',
    'CORE_DIR'       => $_SERVER['DOCUMENT_ROOT'] . '/application/core',
    'COMPONENTS_DIR' => $_SERVER['DOCUMENT_ROOT'] . '/application/core/components',
    
    'DB_HOST'        => 'localhost',
    'DB_NAME'        => 'mvc',
    'DB_USER'        => 'root',
    'DB_PASSWORD'    => '',
];

foreach ($config as $const => $value) {
    defined($const) || define($const, $value);
}
