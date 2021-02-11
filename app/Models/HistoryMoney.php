<?php

namespace App\Models;
use illuminate\database\eloquent\model;

use Illuminate\Pagination\Paginator;
use Illuminate\Pagination;


class HistoryMoney extends model{
        
    protected $table = 'historymoney';
    
    protected $guarded = ['id', 'created_at', 'updated_at'];

    
    
    

}