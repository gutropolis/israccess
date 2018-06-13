<?php
namespace App\Controllers;

use App\Models;
use App\Middleware\Auth;
use App\Middleware\RouteMiddleware; 
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Respect\Validation\Validator as v;

/**
   Admin Dashboard Controller
   CRUDs for Dashboard

*/
class AdminDashboardController extends Base 
{
	protected $container;
	protected $lang;
	public function __construct($container)
	{
		$this->container = $container;
		$this->lang =  $this->container->view['adminLang'];
	}
	
	 public function dashboard($request, $response) {
		 $is_for_maintenance = Models\Setting::where('id', '=', 1)->first()->is_for_maintenance;
		 
		
		 $params = array('base_url' => base_url, 
						 'title' => $this->lang['dashboard_txt'],
						 'is_for_maintenance' => $is_for_maintenance,
						 'current_url' => $request->getUri()->getPath());
        return $this->render($response, ADMIN_VIEW.'/Dashboard/dashboard.twig', $params);		
    }
	
	
	
	
	
}
