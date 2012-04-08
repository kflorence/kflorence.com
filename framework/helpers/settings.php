<?php

// Build framework libraries into include_path
ini_set('include_path',
    ini_get('include_path') . PATH_SEPARATOR . DOC_ROOT . BASE_DIR . LIBRARIES);

// Enable error reporting in development mode
if (ENVIRONMENT == 'development') {
    error_reporting(E_ALL);
    ini_set('display_errors', true);
}

// Disable error reporting in production mode
else {
    error_reporting(0);
    ini_set('display_errors', false);
}