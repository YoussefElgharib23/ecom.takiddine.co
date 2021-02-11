<?php

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
namespace App\Middleware;

defined('BASEPATH') OR exit('No direct script access allowed');

class logoutMiddleware extends Middleware {
    
    public function __invoke($request, $response, $next)
    {
        
        
    

          session_destroy();
         
        
    
        $response = $next($request, $response);
        return $response;
            
        
    }

}