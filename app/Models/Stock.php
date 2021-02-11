<?php

namespace App\Models;
use illuminate\database\eloquent\model;


class Stock extends model{


    
    protected $table = 'stock';
    
    protected $guarded = ['id', 'created_at', 'updated_at'];
    
   public $theorique;
    
   public function product(){
        return $this->belongsTo('\App\Models\Product','ProduitID');
   }
   
   public function city(){
        return $this->belongsTo('\App\Models\Cities','CityID');
   }
    
   public function deliver(){
           return $this->belongsTo('\App\Models\User','User_id');
   }
    
    public function stockBozi(){
       
  
         echo      $this->stockVirtuel + $this->stockTheorique() ;
        
        

    }
    
  public function stockTheorique(){
      
      if(isset($this->StockPhisique) and isset($this->stockEnCours)){
          if($this->stockEnCours != '0'){ 
              $this->theorique = $this->StockPhisique - $this->stockEnCours;
            return $this->StockPhisique - $this->stockEnCours;
          }else {
              $this->theorique = $this->StockPhisique ;
            return  $this->StockPhisique ;
          }
      }else{
           $this->theorique = $this->StockPhisique ;
          return  $this->StockPhisique ;
          
      }
      
      
      
  }
}