<?php 

namespace App\Helpers;
use App\Models\MultiSale;
use PHPtricks\Orm\Database;
use Illuminate\Database\Capsule\Manager as Capsule;
use \App\Models\{ HistoryEntree , StockSortieList , Lists , Retour };

defined('BASEPATH') OR exit('No direct script access allowed');



                
class Reception { 
        
      protected $query;
      protected $products;
      protected $product;
      protected $livre;
      protected $recue;
      protected $physique;
      protected $encours;
      protected $retour;
      protected $real;
      protected $cities;
      protected $city;
      protected $user_id;
      protected $city_id;
      protected $default;
    
      public function __construct( $city = false , $product = false){

          if($city && is_numeric($city)){
              $this->cities = \App\Models\Cities::all('id','city_name  as name','user_id','reference')->whereIn('id',[$city])->toArray();
          }else {
              $this->cities = \App\Models\Cities::all('id','city_name  as name','user_id','reference')->whereIn('id',[36])->toArray();
          }

          if($product && is_numeric($product)){
              $this->products = \App\Models\Product::all('id','name','reference','prix_jmla','price')->whereIn('id',[$product])->toArray();
          }else {
              $this->products = \App\Models\Product::all('id','name','reference','prix_jmla','price')->toArray();
          }
      }
      
      public function load(){
          return $this->get();         
      }
      
      public function get(){
        $result = [];
        foreach ($this->cities as $city) {
          $this->city = $city['name'];
          $this->user_id = $city['user_id'];
          $this->city_id = $city['id'];
          $result[$this->city] = $this->getByProduct();
        }
        return $result;
      }
    
      public function getByProduct(){

              $data = [];

              foreach( $this->products as $product ){
                  
                  $this->product = $product['id'];
                  $this->recue   = 0;
                  $this->livre   = 0;
                  $this->physique   = 0;
                  $this->encours   = 0;
                  $this->retour   = 0;
                  $this->real   = 0;
    

                
                  $item = [
                        'product_id'  =>  $product['id'],
                        'product_name'=>  $product['name'],
                        'product_ref' =>  $product['reference'],
                        'retour'      =>  $this->getStockRetour() ,
                        'recue'       =>  $this->getStockRecue() ,
                        'real'        =>  $this->real,
                        'livre'       =>  $this->getStockDelivred() ,
                        'physique'    =>  $this->getStockPhysique() ,
                        'theorique'   =>  $this->getStockPhysique() - $this->getStockEncours(),
                        'encours'     =>  $this->getStockEncours() ,
                        
                  ];
                  
                  array_push($data,$item);
              }
              return $data;
      }
      
      public function getStockRetour(){
            $validSortie =  Retour::where('productID',$this->product)->where('cityID',$this->city_id)
                ->selectRaw('*, sum(quantity) as sum_quantity')
                ->get()->toArray();
            $this->retour = $validSortie[0]['sum_quantity'] ?? 0;
            return  $this->retour ;
      }

      public function getStockRecue(){
            $validSortie =  StockSortieList::where('productID',$this->product)->where('cityID',$this->city_id)
                ->selectRaw('*, sum(quantity) as sum_quantity ,  sum(valid) as sum_valid ')
                ->get()->toArray();
            $this->recue = $validSortie[0]['sum_valid'] ?? 0;
            
            $this->real = $this->recue - $this->retour;
            
            return  $this->recue;
      }
 
      public function getStockDelivred() {
            $list = \App\Models\Lists::with('products','products.product','realcity')->where('cityID',$this->city_id)
            ->whereNotNull('delivred_at')->whereHas('products.product', function ($query) {
                    return $query->where('id', '=', $this->product);
                })->get()->toArray();
            $this->livre =  $this->getQuantityFromList($list);
            return $this->livre;
      }
    
      public function getStockPhysique(){
            $this->physique =   $this->real - $this->livre ;
            return $this->physique ;
      }
      
      public function getStockTheorique() {
           return $this->physique  - $this->encours ;
      }
    
      public function getStockEncours(){

            $list =   \App\Models\Lists::with('deliver','employee','products','products.product','realcity')
    
                            ->whereNull('deleted_at')
                            ->whereNotNull('accepted_at')
                            ->whereNotNull('verified_at')
                            ->whereNull('duplicated_at')

                            ->whereNull('canceled_at')
                            ->whereNull('delivred_at')
                            ->whereNull('recall_at')
                            ->where('statue','!=','NoAnswer')
                            ->where('cityID',$this->city_id)

                            // اظهار الطلبات التي لا تجيب بعد 15 دقيقة 
                            ->orwhere(function($query) {
                                $query
                                ->where('cityID',$this->city_id)
                                
                            ->whereNull('deleted_at')
                            ->whereNotNull('accepted_at')
                            ->whereNotNull('verified_at')
                            ->whereNull('duplicated_at')
                                ->whereNull('deleted_at')
                                ->whereNotNull('accepted_at')
                                ->whereNotNull('verified_at')
                                ->whereNull('duplicated_at')
                                ->whereNull('deleted_at')
                                ->whereNotNull('accepted_at')
                                ->whereNotNull('verified_at')
                                ->whereNull('duplicated_at')
                                ->whereNotNull('no_answer_time')
                                ->whereNull('delivred_at')
                                ->whereNull('canceled_at')
                                ->whereNull('recall_at')
                                ->whereNotNull('no_answer_time')
                                ->where('no_answer_time', '<', \Carbon\Carbon::now()->subMinutes(60)->toDateTimeString());
                            })
                            

                            // اظهار الطلبات بعد مرور وقت إعادة الإتصال
                            ->orwhere(function($query) {
                                $query
                                 ->where('cityID',$this->city_id)
                                 
                            ->whereNull('deleted_at')
                            ->whereNotNull('accepted_at')
                            ->whereNotNull('verified_at')
                            ->whereNull('duplicated_at')
                                ->whereNull('deleted_at')
                                ->whereNotNull('accepted_at')
                                ->whereNotNull('verified_at')
                                ->whereNull('duplicated_at')
                                ->whereNull('deleted_at')
                                ->whereNotNull('accepted_at')
                                ->whereNotNull('verified_at')
                                ->whereNull('duplicated_at')
                                ->whereNull('canceled_at')
                                ->whereNull('delivred_at')
                                ->whereNull('no_answer_time')
                                ->whereNotNull('recall_at')
                                ->where('recall_at', '<', \Carbon\Carbon::now());
                            })
                            
                            
                            ->whereHas('products', function($q){
                               $q->where('productID', $this->product);
                            });


                            $list = $list->get()->toArray();


            $this->encours =  $this->getQuantityFromList($list) ?? 0 ;
            return $this->encours;
      }
  
      public function getQuantityFromList($list){
          $ok = [];
          foreach($list as $item){
                foreach($item['products'] as $product){    
                    if($product['productID'] == $this->product){
                        $ok[] = $product['quanity'];
                    }
                }
          }
          return array_sum($ok);
      }
  

  
}

