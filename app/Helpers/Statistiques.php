<?php 

namespace App\Helpers;
use \Psr\Http\Message\ServerRequestInterface as Request;
use App\Models\{MultiSale,Lists,Product};
use PHPtricks\Orm\Database;
use Illuminate\Database\Capsule\Manager as Capsule;


defined('BASEPATH') OR exit('No direct script access allowed');

class Statistiques { 
    
    
    
    public $from;
    public $to;
    public $date;
    public $employee;
       
       
       

    public function buildquery(){
         $query = Lists::where('elok')    ;
    }
        
        
    
    public function employee(){
    
    
    
    }
    
        public function employees(){
        
        
        // get employees
       
        
        foreach($employees as $employee ){
            
            // get where delivered 
            
            
        }
        
        
        // get for all girls
        
        }
        
        
        public function day($day,$deliver){
        
        }
        
        public function SpentsAds($data){
        
        }
          
        public function __construct($post = false){
            
            $employees = (new \App\Helpers\Stats\Employee())->list();
            $delivers = (new \App\Helpers\Stats\Deliver())->list();
            sv($delivers);
        }
        
        
        public function setDates($post) {
        
        }


        public static function dateSort($a, $b) {
          
        }

        public function orderDates($days){
      
        }

        public function get(){
         
        }

        public function gain($revenue,$ads){
   
        }




        public function manipulate(){
   
        }

        public function whereBetween(){

        }
        public function days(){

        }
        
        public function groupByDay($array_in){


        }

        public function getProductResult($product){
         
        }



}


