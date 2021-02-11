<?php

namespace App\Controllers;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use \App\Helpers\EmployeeHelper;


defined('BASEPATH') OR exit('No direct script access allowed');

class EmployeeController extends Controller {
    
   
   
    public function index($request,$response) {
        $params         = $request->getParams();
       // $params['limit'] = 20;
        $listing        = (new EmployeeHelper($params))->new();
        $lists          = $listing->load();
        $pagination     = $listing->pagination();
        $type           = $listing->viewType();
        $view           = 'employee/index.twig'; 
        return $this->view->render($response,$view,compact('type','lists','params','pagination'));
    }


    public function canceled($request,$response) {
        $params         = $request->getParams();
        $params['limit'] = 100;
        $listing        = (new EmployeeHelper($params))->canceled();
        $lists          = $listing->load();
        $pagination     = $listing->pagination();
        $type           = $listing->viewType();
        $view           = 'employee/index.twig'; 
        return $this->view->render($response,$view,compact('type','lists','params','pagination'));
    }

    public function NoResponse($request,$response) {
        $params         = $request->getParams();
        $params['limit'] = 100;
        $listing        = (new EmployeeHelper($params))->NoResponse();
        $lists          = $listing->load();
        $pagination     = $listing->pagination();
        $type           = $listing->viewType();
        $view           = 'employee/index.twig'; 
        return $this->view->render($response,$view,compact('type','lists','params','pagination'));
    }


    public function recall($request,$response) {
        $params         = $request->getParams();
        $params['limit'] = 100;
        $listing        = (new EmployeeHelper($params))->recall();
        $lists          = $listing->load();
        $pagination     = $listing->pagination();
        $type           = $listing->viewType();
        $view           = 'employee/index.twig'; 
        return $this->view->render($response,$view,compact('type','lists','params','pagination'));
    }


    public function delivered($request,$response) {
        $params         = $request->getParams();
        $params['limit'] = 100;
        $listing        = (new EmployeeHelper($params))->delivered();
        $lists          = $listing->load();
        $pagination     = $listing->pagination();
        $type           = $listing->viewType();
        $view           = 'employee/index.twig'; 
        return $this->view->render($response,$view,compact('type','lists','params','pagination'));
    }



}
