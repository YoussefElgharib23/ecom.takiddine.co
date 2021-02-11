<?php

namespace App\Controllers;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use \App\Models\User;

defined('BASEPATH') OR exit('No direct script access allowed');

class ApiController extends Controller {
    

	public $query;
	public $provider;
	public $employee;
	public $employees;
	public $product;
	public $phone;
	public $products;
	public $statue;
	public $city;
	public $cities;
    
    
    public function store($request,$response){
        $data = [
            'name'  =>  $_POST['name'] ,
            'tel'  =>  $_POST['tel'] ,
            'adress'  =>  $_POST['adress'] ,
            'city'  =>  $_POST['city'] ,
            'quantity' => $_POST['quantity'],
            'price' =>  $_POST['price'],
            'source' =>  $_POST['source'],
            'ProductReference' => $_POST['ProductReference'],
            'productID' => \App\Models\Product::where('name','LIKE',$data['ProductReference'])->first()->id ?? '',
        ];
        
      
        $newstring = substr($_POST['tel'], -8);
        $foundAlreadyInLists     =   \App\Models\Lists::all();
        $foundAlreadyInNeworders =  \App\Models\NewOrders::all();
        
        $exist = false;
        foreach($foundAlreadyInLists as $order) {
            if( substr($order->tel, -8) == $newstring ) { $exist = true; }
        }
        foreach($foundAlreadyInNeworders as $order) {
            if( substr($order->tel, -8) == $newstring ) { $exist = true; }
        }
        
        if($exist == true ) {
             $data['duplicated_at'] = \Carbon\Carbon::Now();
        }
        
        \App\Models\NewOrders::create($data);
    }
    
    
    
    
    public function __construct( $params  = false ){
        if($params){

        }
        $this->setup();
        return $this;
    }

    public function setup(){
         $this->query = \App\Models\Lists::with('deliver','employee','products','products.product','realcity');
         return $this;
    }

    public function providers($providers){
        if(!empty($providers) or !is_null($providers)){
            
            if($providers == 'current'){
                $this->query = $this->query->current('provider');
            }

            if(is_numeric($providers)){
                $this->query = $this->query->provider($providers);
            }

            if(is_array($providers)){
                $this->query = $this->query->providers($providers);
            }

        }
        return $this;
    }

    public function get($type){

        if(!isset($type)){
            return $this->query->get();
        }

        if($type == 'array'){
            return $this->query->get()->toArray();
        }
        
        if($type == 'count'){
            return $this->query->count();
        }

        if($type == 'json'){
            return $this->query->toJson();
        }

        return $this->query->get();
        
    }


}
