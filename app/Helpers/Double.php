<?php 

namespace App\Helpers;
use \Psr\Http\Message\ServerRequestInterface as Request;
use App\Models\{ Lists , NewOrders};
use PHPtricks\Orm\Database;
use Noodlehaus\Config;
use Illuminate\Database\Capsule\Manager as Capsule;


defined('BASEPATH') OR exit('No direct script access allowed');

class Double { 
    
  protected $number;
  protected $lists;
  protected $newOrders;
  protected $result;

  public function __construct(){
    return $this;
  }
  
  public function get(){
    return  $this->lists()->newOrders()->merge()->result;
  }

  public function merge(){
    $lists = array_merge($this->lists, $this->newOrders);
      
    foreach($lists as $key => $value){
        $new[$value['tel']][$key] = $value;
    }
    $this->result = $new;
    return $this;
  }


  public function newOrders(){
    $lists = NewOrders::whereNotNull('duplicated_at');
    $this->newOrders = $lists->get()->toArray();
    return $this;
  }




  public function lists(){
      
      
    $lists = Lists::with('deliver','employee','products','products.product','city')->orderBy('duplicated_at','desc')->whereIn('tel', function ( $query ) {
        $query->select('tel')->from('lists')->groupBy('tel')->havingRaw('count(*) > 1');
    });
    $this->lists = $lists->get()->toArray();
    return $this;
  }

}
