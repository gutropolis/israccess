<?php

// Use the ridiculously long Symfony namespaces
use Symfony\Bridge\Twig\Extension\TranslationExtension;  
use Symfony\Component\Translation\Translator; 
use Symfony\Component\Translation\Loader\ArrayLoader; 

$container = $app->getContainer();

$container['view'] = function ($c){
    $view = new \Slim\Views\Twig('../app/Views', [
        'cache' => false,
        'debug' => true,
        'auto_reload' => true
    ]);
    $basePath = rtrim(str_ireplace('index.php', '', $c['request']->getUri()->getBasePath()), '/');
    $view->addExtension(new Slim\Views\TwigExtension($c['router'], $basePath));
      

		 
		 /*
	 
				$translator = new Translator('en_US');  
				$translator->setFallbackLocales(['en_US']); 
				$translator->addLoader('php', new ArrayLoader()); 
				$languageArray = require '../app/lang/en_US.php'; 		 
			   $translator->addResource('php', $languageArray, 'en_US'); // French	  
				$view->addExtension(new Symfony\Bridge\Twig\Extension\TranslationExtension($translator));				
				var_dump($translator->trans('Symfony is great!')); 

		  */
      
    return $view;
};

//Override the default Not Found Handler
$container['notFoundHandler'] = function ($c) {
    return function ($request, $response) use ($c) {
        return $c['view']->render($response->withStatus(404), '404/404.twig', [
            "myMagic" => "Ooops! Something went wrong."
        ]);
    };
};

$container['view']['admin'] = $_SESSION;
$container['view']['cart'] = $_SESSION;$container['view']['userinfo'] = $_SESSION;

if(isset($_SESSION['defaultLang'])){
    $_SESSION['default'] = $_SESSION['defaultLang'];
}else{
	$_SESSION['default'] =$settings['settings']['defaultLang'];
}

$container['view']['adminLang'] =  include_once __DIR__ . '/lang/admin/'.$_SESSION['default'].'.php';

$container['App\Controller\UserController'] = function ($c){
    return new \App\Controllers\UserController($c['view']);
};
$container['App\Controller\HomeController'] = function ($c){
    return new \App\Controllers\HomeController($c['view']);
};
$container['App\Controller\CategoryController'] = function ($c){
    return new \App\Controllers\CategoryController($c['view']);
};
$container['App\Controller\EventgroupController'] = function ($c){
    return new \App\Controllers\EventgroupController($c['view']);
};

$container['App\Controller\BookingController'] = function ($c){
    return new \App\Controllers\BookingController($c['view']);
};


$container['App\Controller\AdminController'] = function ($c){
    return new \App\Controllers\AdminController($c['view']);
};

// $container['App\Controller\Auth\AuthController'] = function ($c){
    // return new \App\Auth\Auth;
// };