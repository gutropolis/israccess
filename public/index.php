<?php

use Illuminate\Database\Capsule\Manager as Capsule;
$whitelist = array(
    '127.0.0.1',
    '::1'
);
/*
 *---------------------------------------------------------------
 * APPLICATION ENVIRONMENT
 *---------------------------------------------------------------
 *
 * You can load different configurations depending on your
 * current environment. Setting the environment also influences
 * things like logging and error reporting.
 *
 * This can be set to anything, but default usage is:
 *
 *     development
 *     production
 *
 * NOTE: If you change these, also change the error_reporting() code below
 *
 */
if(!in_array($_SERVER['REMOTE_ADDR'], $whitelist)){ 
	define('ENVIRONMENT', 'production');
	
}else{
	define('ENVIRONMENT', 'development');
}

if (PHP_SAPI == 'cli-server') {
    // To help the built-in PHP dev server, check if the request was actually for
    // something which should probably be served as a static file
    $file = __DIR__  . $_SERVER['REQUEST_URI'];
    if (is_file($file)) {
        return false;
    }
}
require __DIR__  . '/../vendor/autoload.php';
require __DIR__ . '/../app/cors.php';
 
session_start();
// Instantiate the app
$capsule = new Capsule;

if (defined('ENVIRONMENT'))
{
	switch (ENVIRONMENT)
	{
		case 'development': 
              $settings = require __DIR__  . '/../app/settingsDev.php'; 
			  $capsule->addConnection($settings['settings']['database']);
			  define("base_url",$settings['settings']['app_url']); 
		break; 
		case 'production': 
			  $settings = require __DIR__  . '/../app/settingsProd.php';   
			  $capsule->addConnection($settings['settings']['database']);
			  define("base_url",$settings['settings']['app_url']); 	
		break;
		default:
			exit('The application environment is not set correctly.');
	}
}


$capsule->bootEloquent();
$app = new \Slim\App($settings); 

// Set up dependencies

require __DIR__  . '/../app/dependencies.php';
// Register middleware
require __DIR__  . '/../app/middleware.php';
require __DIR__  . '/../app/middleware.php';
require __DIR__  . '/../app/common_functions.php';

require __DIR__ . '/../app/constants.php'; // All constatns
// Register routes
require __DIR__  . '/../app/routes/index.php';

// Run!
$app->run();