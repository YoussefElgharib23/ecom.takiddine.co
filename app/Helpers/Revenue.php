<?php 

namespace App\Helpers;
use \Psr\Http\Message\ServerRequestInterface as Request;
use App\Models\{MultiSale,Lists,Product};
use PHPtricks\Orm\Database;
use Illuminate\Database\Capsule\Manager as Capsule;


defined('BASEPATH') OR exit('No direct script access allowed');

class Revenue { 




      public function HistoryDetails($day,$deliver){

          // getting the days
          $this->day($day,$deliver);

          // getting the products
          $this->products();

          // Calculate the revenue
          $this->manipulate();

          return $this->data;

      }


      public function day($day,$deliver){
          $lists = Lists::whereDate('delivred_at',$day)->where('DeliverID',$deliver);
          $lists = $lists->select('delivred_at')->get()->toArray();
          $this->days = $this->groupByDay($lists);

      }

      protected $productID;
      protected $query;
      protected $date;
      protected $days;
      protected $data;
      protected $from;
      protected $to;


      public function SpentsAds($data){
          $query = Capsule::table('historymoney')->where('date', '=', $data['date']);
          $find = $query->count();
          if($find == 1 ){
              $query->update(['ads' => $data['ads']]);
          }else {
              return Capsule::table('historymoney')->insert($data);
          }
      }
          
      public function __construct($post = false){

          if(is_array($post)){

            // set the dates from to
            $this->setDates($post);

            // getting the days
            $this->days();
            
            // getting the products
            $this->products();

            // Calculate the revenue
            $this->manipulate();

          }
            
          return $this;
      }


        public function setDates($post) {
          if(empty($post['from']) and empty($post['to']) ){
              return $this;
          }
          $this->from  = \Carbon\Carbon::parse($post['from']);
          $this->to    = \Carbon\Carbon::parse($post['to']);
          return $this;
        }


        public static function dateSort($a, $b) {
            return strtotime($a) - strtotime($b);
        }

        public function orderDates($days){
            usort($days, array("\App\Helpers\Revenue", "dateSort")); 
            return $days;
        }

        public function get(){
            return $this->data;
        }

        public function gain($revenue,$ads){
          if(isset($revenue) and is_numeric($revenue) and is_numeric($ads) and isset($ads)){

            return $revenue - $ads;
          }
          return "";
        }




        public function manipulate(){
            foreach ($this->days as $day) {
              $this->date = $day;
              $products = $this->getDayResult($day);
              $revenue  = array_sum(array_column($products,'revenue'));
              $ads      = $this->ads($day);
              
              
              
              
              $query = Lists::whereNotNull('delivred_at')->whereDate('delivred_at', $this->date );
              $revenue = $query->get()->sum('total');
              $laivraison =  $query->get()->sum('prix_de_laivraison');
              $rest = $revenue  - $laivraison;
              
              $this->data[$day]['ads']        = $ads;
              $this->data[$day]['total']      = array_sum(array_column($products,'total'));
              $this->data[$day]['clients']    = array_sum(array_column($products,'clients'));
              $this->data[$day]['quantity']   = array_sum(array_column($products,'quantity'));
              $this->data[$day]['prixJmla']   = array_sum(array_column($products,'prixJmla'));
              $this->data[$day]['laivraison'] = $laivraison;
              $this->data[$day]['revenue']    = $revenue;
              $this->data[$day]['rest']       = $rest;
              $this->data[$day]['products']   = $products;
              $this->data[$day]['gain']       = $this->gain($rest,$ads);
            }
        }

        public function whereBetween(){

        }
        public function days(){

            /*
            $lists = Lists::whereNotNull('delivred_at');

            if(!empty($this->from) and !empty($this->to)){
                $lists =  $lists->whereBetween('delivred_at', [$this->from, $this->to]);
            }

            $lists = $lists->select('delivred_at')->get()->toArray();
              
            $this->days = $this->groupByDay($lists);
            */
            if(!isset($_GET['day'])){
              $day = date("Y-m-d");

            }else {
              $day = $_GET['day'];
            }

            $this->days = [$day];
        }
        
        public function groupByDay($array_in){
              $hash = array();
              $array_out = array();
              foreach($array_in as $item) {
                  $hash_key = $item['dayDelivred'];
                  if(!array_key_exists($hash_key, $hash)) {
                      $hash[$hash_key] = sizeof($array_out);
                      array_push($array_out, $item['dayDelivred']);
                  }
              }
              return $this->orderDates($array_out);
        }

        public function getProductResult($product){
            $this->productID = $product['id'];
            $this->initQuery();
            $clients    =  $this->clients();
            if(!$clients > 0 ) {
                return false;
            }
            $quantity   =  $this->quantity();
            $prixJmla   =  $quantity  * $product['prix_jmla'];
            $laivraison =  $this->laivraison();
            $total      =  $this->total();
            $revenue    =  $total - $prixJmla - $laivraison;
            $rest       =  $total - $laivraison;
            $fetched = [
              'product'    =>  $product['name'],
              'productID'  =>  $product['id'],
              'clients'    =>  $clients,
              'quantity'   =>  $quantity,
              'total'      =>  $total,
              'prixJmla'   =>  $prixJmla,
              'rest'       =>  $rest,
              'laivraison' =>  $laivraison,
              'revenue'    =>  $revenue,
            ];
            return $fetched;
        }

        public function getDayResult($day){
              $data = [];

              foreach ($this->products as $product) {
                  $fetched = $this->getProductResult($product);
                  if($fetched) {
                      array_push($data,$fetched);
                  }
              }

              return $data;
        }

        public function initQuery(){
            $this->query  = Lists::whereHas('products', function ($query) {
              $query->where('productID', '=', $this->productID);
            })->whereNotNull('delivred_at')->whereDate('delivred_at', $this->date );
        }


        public function laivraison(){
          return $this->query->get()->sum('prix_de_laivraison');
        }  

        public function total2(){
            return $this->query->get()->sum('total');
        }
        
        public function total(){




              $list =  $this->query->get()->toArray();  
        
          
          return $this->calculateTotal($list);
        }
        

        public function clients(){
            return $this->query->count();
        }


        public function ads($day){
              $ads =  Capsule::table('historymoney')
                            ->where('date', $day)
                            ->first();

                if($ads ){
                 return $ads->ads;
                }else {
                 return '';                          
               }
        }

        public function quantity(){
          $list =  $this->query->get()->toArray();
          return $this->countQuantites($list);
        }
        
        public function products(){
            $this->products = $_SESSION['products'];
        }

        public function countQuantites($array_in){
            $total = [];
            foreach($array_in as $item) {
               foreach ($item['products'] as $product) {
                if($product['productID'] == $this->productID )
                 $total[] = $product['quanity'];
               }
            }
            return array_sum($total);
        }
        
        public function calculateTotal($array_in){
            $total = [];
            foreach($array_in as $item) {
               foreach ($item['products'] as $product) {
                if($product['productID'] == $this->productID )
                 $total[] = $product['price'];
               }
            }
            return array_sum($total);
        }
        


}




