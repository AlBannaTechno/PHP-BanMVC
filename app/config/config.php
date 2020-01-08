<?php

// App Root
define('APP_ROOT', dirname(__FILE__, 2));

// URL ROOT

define('URL_ROOT', 'http://localhost/BanMVC');

// SITE NAME

define('SITE_NAME', 'BanMvcImp');

// define The Main Layout Name , from ../app/views/

define('__LAYOUT__', '_layout.php');
define('__AREA_DEFAULTS__', '_defaults.php');
define('__AREA_BASE_DEFAULTS__', '_defaults.php');

// declare specific globals => globals return string to other globals

define('__GLOB__BODY__', '__BODY__');
define('__GLOB__CONTROLLER_TITLE__', '__CONTROLLER_TITLE__');
define('__GLOB__CONTROLLER_ACTION_TITLE__', '__CONTROLLER_ACTION_TITLE__');
define('__GLOB__MODEL__', '__MODEL__');
define('__GLOB__AREA__DEFAULT_CONTROLLER', '__AREA_DEFAULT_CONTROLLER__');
define('__GLOB__AREA_BASE__DEFAULT_AREA__', '__AREA_DEFAULT_CONTROLLER__');

// Config CORE FEATURES

define('__CORE_FEATURES_SUPPORT_AREA__', TRUE);
