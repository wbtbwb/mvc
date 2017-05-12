<?php

session_start();

ini_set('display_errors', 1);
error_reporting(E_ALL);

header('Content-Type: text/html; charset=utf-8');

require_once COMPONENTS_DIR . '/Autoloader.php';
(new Autoloader)->register();

Registry::set('user', (new User));
Registry::set('request', (new Request));

require_once CORE_DIR . '/Model.php';
require_once CORE_DIR . '/View.php';
require_once CORE_DIR . '/Controller.php';

require_once CORE_DIR . '/Router.php';
Router::start();
