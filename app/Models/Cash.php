<?php

namespace App\Models;
use illuminate\database\eloquent\model;


class Cash extends model{

   protected $table = 'cache';
    
   protected $guarded = ['id', 'created_at', 'updated_at'];
    
   public function product(){
        return $this->belongsTo('\App\Models\Product','productID');
   }
   
   public function city(){
        return $this->belongsTo('\App\Models\Cities','cityID');
   }
    
   public function deliver(){
           return $this->belongsTo('\App\Models\User','userID');
   }
    










    
}
