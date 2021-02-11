<?php 

namespace App\Helpers;
use App\Models\MultiSale;
use PHPtricks\Orm\Database;
use Illuminate\Database\Capsule\Manager as Capsule;



defined('BASEPATH') OR exit('No direct script access allowed');




class StockSortieHelper { 
    
      protected $query;
      protected $deliver;
      protected $from;
      protected $to;
      
      
      /*
      *     
      *     groupe by date
      */
      
      function groupByDay($array_in){


            $hash = array();
            $array_out = array();
            foreach($array_in as $item) {
                  $hash_key = $item['created_at'];
                  if(!array_key_exists($hash_key, $hash)) {
                      $hash[$hash_key] = sizeof($array_out);
                      array_push($array_out, array(
                          'day' => $item['dayDelivred'],
                          'count' => 0,
                      ));
                  }
                  $array_out[$hash[$hash_key]]['count'] += 1;
            }
            return $array_out;
            
            
      }
    
     
     
      public function getDates(){
          StockSortieList::groupeByDate("");
      }
      
       
       
      public function getDelivery(){
          
      }
      
      
      
       
      public function getStock(){
          
          
      }
      
      
}

