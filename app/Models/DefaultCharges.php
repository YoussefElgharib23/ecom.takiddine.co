<?php

namespace App\Models;
use illuminate\database\eloquent\model;



class DefaultCharges extends model{

    protected $table = 'defaultcharges';
    
    protected $guarded = ['id', 'created_at', 'updated_at'];
    
    public function product() {
        return $this->belongsTo('\App\Models\Product','productID');
    }
    
    public function statueEcho(){
        
        if(!is_null($this->paied)){
            echo 'تم الدفع';
        }else {
            echo 'غير مدفوع';
        }
        
    }
    
    
}