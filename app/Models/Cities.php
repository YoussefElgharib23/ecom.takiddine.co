<?php

namespace App\Models;
use illuminate\database\eloquent\model;




class Cities extends model{

    protected $table = 'cities';
    
    protected $guarded = ['id', 'created_at', 'updated_at'];
    
    
     public function log(){
        
        return unserialize($this->log);
        
    }
            // Get Post User
    public function product(){
        return $this->belongsTo('\App\Models\Product','productID');
    }
    
    public function deliver(){
           return $this->belongsTo('\App\Models\User','user_id');
    }
    
    
    public function stock(){
           return $this->hasMany('\App\Models\Stock','CityID');
    }

    public function reception(){
            return $this->hasMany('\App\Models\DailyStock','stockcity');
    }
    
    
    public function dailystock()
    {
        return $this->hasManyThrough(
            '\App\Models\DailyStock', '\App\Models\User',
            'stockcity', 'user_id', 'id'
        );
    }  
    
    
    
    
//    public function users() {
//        return $this->belongsToMany(User::class, 'role_user');
//    }
    
    
}