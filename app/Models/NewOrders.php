<?php

namespace App\Models;
use illuminate\database\eloquent\model;


class NewOrders extends model{

    protected $table = 'new';
    
    protected $guarded = ['id', 'created_at', 'updated_at'];
    

    protected $appends = ['type','select'];


    public static function archived(){
        return Self::whereNotNull('deleted_at');
    }

    public static function sheet(){
        return Self::where('source','sheet')->whereNull('deleted_at')->whereNull('duplicated_at');
    }

    public static function stores(){
        return Self::where('source','!=','sheet')->whereNull('duplicated_at')->whereNull('deleted_at');
    }


    public static function duplicated(){
        return Self::whereNotNull('duplicated_at')->whereNull('deleted_at');
    }

    public function getSelectAttribute(){
        return "new";
    }

    public function getTypeAttribute(){
        return "طلب جديد  " . $this->genre();
    }

    
    public function realproduct(){
        return $this->belongsTo('\App\Models\Product','productID')->select('name');
    }
    

    public function genre(){
        if(!empty($this->duplicated_at)){
            return ' - في المكررة   ';
        }
        elseif(!empty($this->deleted_at)) {
            return ' -  في المحذوفة   ';
        }
        elseif(empty($this->duplicated_at) and empty($this->deleted_at)) {
            
            if($this->source != 'sheet') {
                return ' -  في المتاجر   ';    
            }
            
            if($this->source == 'sheet') {
                return ' -  في googe sheet';    
            }
            
        }
    }
    

    // Get Post User
    public function cityname(){
        return $this->belongsTo('\App\Models\Cities','cityID')->select('city_name');
    }
    
    
    public function productTitle(){
        return  mb_strimwidth($this->ProductReference, 0,30, '');
    }
    
    
   
}