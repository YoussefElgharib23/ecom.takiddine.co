<?php 

namespace App\Helpers;
use \Psr\Http\Message\ServerRequestInterface as Request;
use App\Models\{ Lists , NewOrders};

defined('BASEPATH') OR exit('No direct script access allowed');

class Search { 
    
  protected $number;
  protected $lists;
  protected $newOrders;
  protected $result;
  protected $view;

  public function __construct($number = false,$route){

    if(isset($number['q']) and !empty($number['q']) and !is_null($number['q']) and is_numeric($number['q'])) {
        $this->number = clean($number['q']);
    }
    $this->setView();
    return $this;
  }

  public function setView(){
    if(isset($_SESSION['auth-admin'])){
        $this->view = 'admin/admin/search.twig';
    }
    if(isset($_SESSION['auth-employee'])){
        $this->view = 'admin/employee/search.twig';
    }
    if(isset($_SESSION['auth-data'])){
        $this->view = 'admin/data/search.twig';
    }
  }


  public function view(){
      return $this->view ;
  }

  public function number(){
      return $this->number ;
  }
  
  public function search(){
    return  $this->lists()->newOrders()->merge()->result;
  }

  public function merge(){
    
      if(empty($this->lists) and empty($this->newOrders)){
        return $this;
      }
      $this->result = array_merge($this->lists, $this->newOrders);
      return $this;
  }


  public function newOrders(){
    if(empty($this->number)){
      return $this;
    }
    $lists = NewOrders::where('tel', 'LIKE', '%' . $this->number . '%');
    $this->newOrders =  $lists->get()->toArray();
    return $this;
  }


  public function lists(){

    if(empty($this->number)){
      return $this;
    }

    $lists = Lists::with('deliver','employee','products','products.product','realcity');
    $lists   = $lists->where('tel', 'LIKE', '%' . $this->number . '%');
    $this->lists =  $lists->get()->toArray();
    return $this;
  }

}




