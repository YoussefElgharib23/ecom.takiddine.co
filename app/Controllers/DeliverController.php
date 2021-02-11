<?php

namespace App\Controllers;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

use \App\Helpers\DeliverHelper;


defined('BASEPATH') OR exit('No direct script access allowed');

class DeliverController extends Controller {
    
   
   
    public function index($request,$response) {
        $params         = $request->getParams();
        $params['limit'] = 200;
        $listing        = (new DeliverHelper($params))->new();
        $lists          = $listing->load();
        $pagination     = $listing->pagination();
        $type           = $listing->viewType();
        $view           = 'deliver/index.twig'; 
        return $this->view->render($response,$view,compact('type','lists','params','pagination'));
    }


    public function canceled($request,$response) {
        $params         = $request->getParams();
        $params['limit'] = 200;
        $listing        = (new DeliverHelper($params))->canceled();
        $lists          = $listing->load();
        $pagination     = $listing->pagination();
        $type           = $listing->viewType();
        $view           = 'deliver/index.twig'; 
        return $this->view->render($response,$view,compact('type','lists','params','pagination'));
    }


    public function NoResponse($request,$response) {
        $params         = $request->getParams();
        $params['limit'] = 200000;
        $listing        = (new DeliverHelper($params))->NoResponse();
        $lists          = $listing->load();
        $pagination     = $listing->pagination();
        $type           = $listing->viewType();
        $view           = 'deliver/index.twig'; 
        return $this->view->render($response,$view,compact('type','lists','params','pagination'));
    }


    public function recall($request,$response) {
        $params         = $request->getParams();
        $listing        = (new DeliverHelper($params))->recall();
        $lists          = $listing->load();
        $pagination     = $listing->pagination();
        $type           = $listing->viewType();
        $view           = 'deliver/index.twig'; 
        return $this->view->render($response,$view,compact('type','lists','params','pagination'));
    }


    public function delivered($request,$response) {
        $params         = $request->getParams();
        $params['limit'] = 200;
        $listing        = (new DeliverHelper($params))->delivered();
        $lists          = $listing->load();
        $pagination     = $listing->pagination();
        $type           = $listing->viewType();
        $view           = 'deliver/index.twig'; 
        return $this->view->render($response,$view,compact('type','lists','params','pagination'));
    }



}
