<?php

namespace App\Models;
use illuminate\database\eloquent\model;


class MultiSale extends model{

    protected $table = 'multisale';
    
    protected $guarded = ['id', 'created_at', 'updated_at'];
    
    public function product(){
        return $this->belongsTo('\App\Models\Product','productID');
    }
    
    public function list(){
        return $this->belongsTo('\App\Models\Lists','listID');
    }
    
}