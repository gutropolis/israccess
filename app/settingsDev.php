<?php
return [
    'settings' => [
        // comment this line when deploy to production environment
        'displayErrorDetails' => true,
        // View settings
    	
        // monolog settings
        'logger' => [
            'name' => 'app',
            'path' => __DIR__ . '/../log/app.log',
        ],
		//database
        'database' => [
			'driver'    => 'mysql',
			'host'      => 'localhost',
			'database'  => 'cultureaccess',
			'username'  => 'root',
            'password'  => '',
            'charset'   => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix'    => '',
        ],
		
		'defaultLang' => 'en_US',
		// app url
		'app_url' => 'http://'.$_SERVER['HTTP_HOST'].'/event_site_v1/public' ,
    ],
	 
	
];