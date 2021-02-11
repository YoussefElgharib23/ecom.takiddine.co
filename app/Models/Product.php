<?php




namespace App\Models;
use illuminate\database\eloquent\model;

class Product extends model{

    protected $table = 'products';    
    protected $guarded = ['id', 'created_at', 'updated_at'];   
}