<?php

namespace App\Helpers;
use \App\Models\Lists;

defined('BASEPATH') OR exit('No direct script access allowed');

class Api {
    

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


    public function __construct( $params  = false ){
        if($params){

        }
        $this->setup();
        return $this;
    }
    

    public function setup(){
        $this->query = Lists::with('deliver','employee','products','products.product','realcity');
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


    public function employees($employees){
        if(!empty($employees) or !is_null($employees)){
            
            if($providers == 'current'){
                $this->query = $this->query->current('employee');
            }

            if(is_numeric($employees)){
                $this->query = $this->query->employee($employees);
            }

            if(is_array($employees)){
                $this->query = $this->query->employees($employees);
            }
        }
        return $this;
    }


    public function products($products){
        if(!empty($products) or !is_null($products)){

            if(is_numeric($products)){
                $this->query = $this->query->product($products);
            }

            if(is_array($products)){
                $this->query = $this->query->products($products);
            }
        }
        return $this;
    }



    public function cities($cities){
        if(!empty($cities) or !is_null($cities)){

            if(is_numeric($cities)){
                $this->query = $this->query->city($cities);
            }

            if(is_array($cities)){
                $this->query = $this->query->cities($cities);
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
