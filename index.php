<?php
// @Author: Kyle Florence
// @Update: 08/14/2009

// Server host, document root and other locations
define('BASE_PATH', './');
define('ENVIRONMENT', 'development');
define('HOST', $_SERVER['HTTP_HOST']);
define('DOC_ROOT', $_SERVER['DOCUMENT_ROOT']);
define('PROTOCOL', 'http' . (empty($_SERVER['HTTPS']) ? '' : 's') . '://');
define('BASE_DIR', preg_replace('/[^\/]+$/i', '', $_SERVER['SCRIPT_NAME']));
define('BASE_URL', PROTOCOL . HOST . BASE_DIR);
define('REQUEST_URI', substr($_SERVER['REQUEST_URI'], strlen(BASE_DIR)));
define('PAGE_URL', BASE_URL . REQUEST_URI);

// Directory locations
define('FRAMEWORK', 'framework/');
define('FRAMEWORK_CONFIG', FRAMEWORK . 'framework.config.php');
define('LIBRARIES', FRAMEWORK . 'libraries/');
define('MODULES', FRAMEWORK . 'modules/');
define('HELPERS', FRAMEWORK . 'helpers/');
define('CORE', FRAMEWORK . 'core/');

// Site information
define('SITES', 'sites/');
define('SITE', SITES . 'kflorence.com/');
define('SITE_CONFIG', SITE . 'site.config.php');
define('REQUESTS', SITE . 'requests/');
define('TEMPLATE', SITE . 'template/');
define('CHUNKS', TEMPLATE . 'chunks/');
define('PAGES', TEMPLATE . 'pages/');
define('MEDIA', SITE . 'media/');

// File extensions
define('CHUNK_EXT', '.chunk');
define('PAGE_EXT', '.page');
define('PHP_EXT', '.php');

// Start up the framework
require_once(FRAMEWORK . 'start.php');