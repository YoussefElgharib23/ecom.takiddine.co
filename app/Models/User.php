<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Pagination\Paginator;
use Illuminate\Pagination;


class User extends model{
    
    

    

    public function __construct(){
       
    }

    protected $admin_role = 2;
    protected $table = 'users';
    
    protected $guarded = ['id', 'created_at', 'updated_at'];
    
    /* *************
     * Display User Role 
     * *************
    */
    public function role(){
        
        $role = User::where('username',$this->username)->first();
        
        if($role){
            if($role->role == 'admin' ) {
                echo '<span class="label bg-pink-800">مدير</span>';
            }
            if($role->role == 'employee' ) {
                echo '<span class="label label label-success">موظفة</span>';
            }
            if($role->role == 'data') {
                echo '<span class="label label-danger">مضيف بيانات</span>';
            } 
            if($role->role == 'deliver') {
                echo '<span class="label label-primary">  موزع </span>';
            }  
            if($role->role == 'stock') {
                echo '<span class="label bg-violet-800">متتبع المخزون</span>';
            }

            if($role->role == 'suivi') {
                echo '<span class="label bg-violet-800">  لجنة المتابعة   </span>';
            }  
        }
        echo  " ";

    }
    
    /* *************
     * Display User Statue 
     * *************
    */  
    public function statue(){
        $statue = self::where('username',$this->username)->first();
        
        if($statue){
            if($statue->statue == 1 ) {
                echo '<span class="label border-left-success label-striped">'.$this->lang['panelang']['4'].'</span>';
            }
            if($statue->statue == 2 ) {
                echo '<span class="label border-left-primary label-striped">'.$this->lang['panelang']['5'].'</span>';
            }
            if($statue->statue == 3 ) {
                echo '<span class="label border-left-danger label-striped">'.$this->lang['panelang']['6'].'</span>';
            }   
        }
         echo  " ";
    }
    
    
    public function gender(){
        if($this->gender == 'male') {
            return $this->lang['panelang']['7'];
        }else{
            return $this->lang['panelang']['8'];
        }
    }
    
    









    
//    public function cities_list(){
//        
//        if(!empty($this->city)){
//           $cities = explode(',',$this->city);
//            foreach($cities as $city) {
//            echo '<div class="his_cities">
//                <input type="text"  class="form-control" name="city[]" value="'.$city.'" >
//                <a><i class="icon-close2"></i></a>
//            </div>';
//        }
//        }
//        
//        
//         
//    }
//    
    public function cities(){
            return $this->hasMany('\App\Models\Cities','user_id');
    }
    
    
//    public function reception()
//    {
//        return $this->hasManyThrough('\App\Models\Cities', '');
//    }
    
    
    public function is_admin(){
        
        if($this->role == $this->admin_role){
            return true;
        }
        
        return false;
    }
}