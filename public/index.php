<?php

// Check PHP version.
$minPhpVersion = '7.4'; // CI4 requires PHP 7.4 or newer
if (version_compare(PHP_VERSION, $minPhpVersion, '<')) {
    $message = sprintf(
        'Your PHP version must be %s or higher to run CodeIgniter. Current version: %s',
        $minPhpVersion,
        PHP_VERSION
    );

    exit($message);
}

// Path to the front controller (this file)
define('FCPATH', __DIR__ . DIRECTORY_SEPARATOR);

// Path to the 'app' directory
define('APPPATH', dirname(FCPATH) . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR);

// Path to the 'system' directory
define('SYSTEMPATH', dirname(FCPATH) . DIRECTORY_SEPARATOR . 'system' . DIRECTORY_SEPARATOR);

// Path to the 'writable' directory
define('WRITEPATH', dirname(FCPATH) . DIRECTORY_SEPARATOR . 'writable' . DIRECTORY_SEPARATOR);

// Location of the Composer autoloader
$composerAutoload = dirname(FCPATH) . '/vendor/autoload.php';

// Load the Composer autoloader
if (file_exists($composerAutoload)) {
    require $composerAutoload;
} else {
    exit('Failed to find Composer autoloader. Please run: composer install');
}

// Load the CodeIgniter bootstrap file
require SYSTEMPATH . 'bootstrap.php';

// Load environment settings
$env = (is_file(APPPATH . 'Config/.env')) ? 'development' : 'production';
define('ENVIRONMENT', isset($_SERVER['CI_ENV']) ? $_SERVER['CI_ENV'] : $env);

/**
 * ---------------------------------------------------------------
 * ERROR REPORTING
 * ---------------------------------------------------------------
 */
switch (ENVIRONMENT) {
    case 'development':
        error_reporting(-1);
        ini_set('display_errors', 1);
        break;

    case 'testing':
    case 'production':
        ini_set('display_errors', 0);
        error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_STRICT & ~E_USER_NOTICE & ~E_USER_DEPRECATED);
        break;

    default:
        header('HTTP/1.1 503 Service Unavailable.', true, 503);
        echo 'The application environment is not set correctly.';
        exit(1);
}

// Initialize CodeIgniter
$app = Config\Services::codeigniter();
$app->initialize();
$app->run();
