<?php

namespace App\Models;
use illuminate\database\eloquent\model;


class DailyStock extends model{

   protected $table = 'dailystock';
    
   protected $guarded = ['id', 'created_at', 'updated_at'];
    
   public function product(){
        return $this->belongsTo('\App\Models\Product','productID');
   }
   
   public function city(){
        return $this->belongsTo('\App\Models\Cities','stockcity');
   }
    
    
  
    
}