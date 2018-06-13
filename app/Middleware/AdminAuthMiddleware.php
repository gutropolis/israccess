<?php
namespace App\Middleware;
use App\Models\User;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use \Psr\Http\Message\RequestInterface;
use \Psr\Http\Message\ResponseInterface;

class AdminAuthMiddleware
{
    
    public function __invoke(Request $request, Response $response, $next)
    {
        if (!$this->checkAdminLogin()) {
			return $response->withRedirect(base_url.'/admin/login');	
			//return $response->withStatus(200)->withHeader('Location', base_url.'/admin/login');
        }
        $response = $next($request, $response);
        return $response;
    }
	
	public function checkAdminLogin(){
       return (isset($_SESSION['isAdmin']) && $_SESSION['isAdmin'] == 'Okay') ?  true : false;
   }
}