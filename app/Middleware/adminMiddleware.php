<?php

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
namespace App\Middleware;

defined('BASEPATH') OR exit('No direct script access allowed');

class adminMiddleware extends Middleware {
    

    public $container;

    public function __construct($container){

      $this->container = $container; 
    }
    public function __invoke($request, $response, $next)
    {


        if(!isset($_SESSION['auth-admin'])) {
            
          if(isset($_SESSION['auth-deliver'])){
          	return $response->withRedirect($this->container->conf['url.'.'base'].'/deliver/listing/new'); 
          }


          if(isset($_SESSION['auth-employee'])){

            return $response->withRedirect($this->container->conf['url.'.'base'].'/employee/listing/new');
          }


          if(isset($_SESSION['auth-suivi'])){
            return $response->withRedirect($this->container->conf['url.'.'base'].'/suivi?type=suivi');
          }

          if(isset($_SESSION['auth-data'])){
          	return $response->withRedirect('/new-orders');
          }


          if(isset($_SESSION['auth-stock'])){
            return $response->withRedirect('/embalage');
          }
        
        }else {
            $response = $next($request, $response);
            return $response;
        }
    }

}