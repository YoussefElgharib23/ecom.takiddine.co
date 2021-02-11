<?php

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
namespace App\Middleware;

defined('BASEPATH') OR exit('No direct script access allowed');

class authMiddleware extends Middleware {
    
    public function __invoke($request, $response, $next)
    {
        
        $actual_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

        $route = $request->getAttribute('route')->getName();
   
        $this->container->view->getEnvironment()->addGlobal('EXIST_URL', '('.$actual_link .')');
        $this->container->view->getEnvironment()->addGlobal('EXIST_ROUTE', $route );
   
        if(!isset($_SESSION['auth-user'])) {
            
            
            return $this->container->view->render($response,'admin/admin/login.twig');
        }
        
        
        else {
        $response = $next($request, $response);
        return $response;
            
        }
    }

}