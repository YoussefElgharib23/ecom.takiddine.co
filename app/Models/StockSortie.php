<?php

namespace App\Models;
use illuminate\database\eloquent\model;


class StockSortie extends model{
 
    protected $table = 'stocksortie';
    
    protected $guarded = ['id', 'created_at', 'updated_at'];
    
    
    public function product(){
        return $this->belongsTo('\App\Models\Product','ProductID');
    }
    
    
    
    public function ListQuantities(){
        return $this->HasMany('\App\Models\StockSortieList','sortie_list_id');
    }
        
    public static function ValidateMe($id,$valid){
        $yep = self::find($id);
        $yep->valid = $valid;
        $yep->statue = 1;
        $yep->save();
    }
    
    
    
}