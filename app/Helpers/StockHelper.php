<?php 

namespace App\Helpers;
use App\Models\MultiSale;
use PHPtricks\Orm\Database;
use Illuminate\Database\Capsule\Manager as Capsule;
use \App\Models\{ Product ,  HistoryEntree , StockSortieList  };



defined('BASEPATH') OR exit('No direct script access allowed');



class StockHelper {
    
    
      protected $query;
      protected $deliver;
      protected $from;
      protected $to;
      protected $products;
      protected $data;
      
      
      public function __construct(){
        $this->products = Product::all()->toArray();
      }
      
      
      public function get(){
          $data = [];
          // select all products where not null
          foreach( $this->products as $product ){
              $item = [
                    'product_id'   =>  $product['id'],
                    'sortie'       =>  $this->getStockEntreeProdut($product['id']) ,
                    'entree'       =>  $this->getStockSortieProdut($product['id']) ,
                    'rest'         =>  $this->getStockSortieProdut($product['id']) - $this->getStockSortieProdut($product['id']) 
              ];
          }
          return $data;
      }
      
      
      
      public function getStockEntreeProdut($id){
        $HistoryEntree = HistoryEntree::where('productID',$id)     
        ->selectRaw('*, sum(quantity) as sum_quantity ,  sum(valid) as sum_valid ')
        ->get()->toArray();
        return $HistoryEntree[0]['sum_valid'];
      }



      public function getStockSortieProdut($id){
        $validSortie = StockSortieList::where('productID',$id)
            ->selectRaw('*, sum(quantity) as sum_quantity ,  sum(valid) as sum_valid ')
            ->get()->toArray();
        return $validSortie[0]['sum_valid'];
      }
      
      
      
      
}
