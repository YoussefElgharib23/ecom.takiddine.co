<?php

namespace App\Models;
use illuminate\database\eloquent\model;


class StockSortieList extends model{
 
    protected $table = 'sortielistproducts';
    
    protected $guarded = ['id', 'created_at', 'updated_at'];
    
    
    // Get Post User
    public function cityID(){
        return $this->belongsTo('\App\Models\Product','ProductID');
    }
    
    
    public static function ValidateMe($id,$valid){
        $yep = self::find($id);
        $yep->valid = $valid;
        $yep->statue = 1;
        $yep->save();
    }
    
        
   public function product(){
        return $this->belongsTo('\App\Models\Product','productID');
   }

        
        
   public function city(){
        return $this->belongsTo('\App\Models\Cities','cityID');
   }        
        
   public function ScopeStockRecue(){
        return $this->belongsTo('\App\Models\Product','productID');
   }



        
   public function Scopevalid($query,$product){
        return $valid;
   }





    
    
}