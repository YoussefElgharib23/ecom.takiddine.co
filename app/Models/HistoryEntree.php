<?php

namespace App\Models;
use illuminate\database\eloquent\model;


class HistoryEntree extends model{

   protected $table = 'historyentree';
    
   protected $guarded = ['id', 'created_at', 'updated_at'];
    
   public function product(){
        return $this->belongsTo('\App\Models\Product','productID');
   }
   
   public function city(){
        return $this->belongsTo('\App\Models\Cities','stockcity');
   }
    
    
  
    
}