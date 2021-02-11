<?php 

namespace App\Helpers;
use \App\Models\Lists;
use \Carbon\Carbon;

defined('BASEPATH') OR exit('No direct script access allowed');

class Noanswer {
    
    public $daba ;
    
    public function __construct(){
         $this->daba = Carbon::now(new \DateTimeZone('Africa/Casablanca'));
    }
    
    public function start($id){       
        if(is_numeric($id)){
            if(isset($_SESSION['auth-suivi'])){
                return  $this->suivi_set($id);
            }else {
                return  $this->check_and_set($id);
            }
        }
        return  false;
    }
  
     public function getusername()
    {
        
        if(!isset($_SESSION['username'])){
            $_SESSION['username'] = (\App\Models\User::find($_SESSION['auth-user']))->username;
                return $_SESSION['username'];
        }
        return $_SESSION['username'];
    }


    public function saveHistory($list,$message){
        $load_history = $list->history;
    
        if($load_history){
                $history = json_decode($load_history);
                array_push($history,$message);
                $history = json_encode($history, JSON_UNESCAPED_UNICODE);
                $list->history  = $history;
                $list->save();
                return '1';
        }else {
                $history = [];
                array_push($history,$message);
                $history = json_encode($history, JSON_UNESCAPED_UNICODE);
                $list->history  = $history;
                $list->save();
                return '0';
        }
    }
    
  
     
    public function No_answer_1($list){
        $list->statue           = 'NoAnswer';
        $list->no_answer        = 'no_answer_1';
        $list->no_answer_time   = $this->daba;
        $mesage = 'تم  تعيين ك لا يجيب 1 '.  ' - '. $this->daba ;
        $mesage .= '  من طرف ';
        $mesage .= $this->getusername();
        $this->saveHistory($list,$mesage);   
    }
    
    public function No_answer_2($list){
        $list->statue           = 'NoAnswer';
        $list->no_answer        = 'no_answer_2';
        $list->no_answer_time   = $this->daba;
        $mesage = 'تم  تعيين ك لا يجيب 2 '.  ' - '.$this->daba;
        $mesage .= '  من طرف ';
        $mesage .= $this->getusername();
        $this->saveHistory($list,$mesage);   
    }    
    
    public function No_answer_3($list){
        $list->statue           = 'NoAnswer';
        $list->no_answer        = 'no_answer_3';
        $list->no_answer_time   =  $this->daba;
        $mesage = 'تم  تعيين ك لا يجيب 3 '.  ' - '.$this->daba;
        $mesage .= '  من طرف ';
        $mesage .= $this->getusername();
        $this->saveHistory($list,$mesage);   
    }  
    
    public function No_answer_4($list){

        if(isset($_SESSION['auth-deliver'])){
            return $this->No_Answer_Cancel_deliver($list);
        }

        else {
                $list->statue           = 'NoAnswer';
                $list->no_answer        = 'no_answer_4';
                $list->no_answer_time   = $this->daba;
                $mesage = 'تم  تعيين ك لا يجيب 4 '.  ' - '.$this->daba;
                $mesage .= '  من طرف ';
                $mesage .= $this->getusername();
                $this->saveHistory($list,$mesage);   
                if(isset($_SESSION['auth-employee'])) {
                    return "send_sms";
                }
        }
    }  
    
    public function No_answer_5($list){
        $list->statue           = 'NoAnswer';
        $list->no_answer        = 'no_answer_5';
        $list->no_answer_time   = $this->daba;                        
        $list->save();
        $mesage = 'تم  تعيين ك لا يجيب 5 '.  ' - '. $this->daba;
        $mesage .= '  من طرف ';
        $mesage .= $this->getusername();
        $this->saveHistory($list,$mesage);
    }  
    
    
    public function No_answer_6($list){
        $list->statue           = 'NoAnswer';
        $list->no_answer        = 'no_answer_6';
        $list->no_answer_time   = $this->daba;
        $list->save();
        $mesage = 'تم  تعيين ك لا يجيب 6 '.  ' - '. $this->daba;
        $mesage .= '  من طرف ';
        $mesage .= $this->getusername();
        $this->saveHistory($list,$mesage);
    } 
    
        
    public function No_answer_7($list){
        $list->statue           = 'NoAnswer';
        $list->no_answer        = 'no_answer_7';
        $list->no_answer_time   =  $this->daba;
        $list->save();
        $mesage = 'تم  تعيين ك لا يجيب 7 '.  ' - '. $this->daba;
        $mesage .= '  من طرف ';
        $mesage .= $this->getusername();
        $this->saveHistory($list,$mesage);
    } 
    
    
        
    public function No_answer_8($list) {
        $list->statue        = '';
        $list->no_answer     = '';
        $list->cancel_reason = "ملغى بسبب لا يجيب";
        $list->canceled_at   =  $this->daba;
        $list->save();
        $mesage = ' تم  الإلغاء بسبب الرقم لا يجيب ل 8 مرات '.  ' - '  . $this->daba;
        $this->saveHistory($list,$mesage);
    } 
        
    public function No_Answer_Cancel_deliver($list) {
        $list->statue        = '';
        $list->no_answer     = '';
        $list->cancel_reason = "ملغى بسبب لا يجيب";
        $list->canceled_at   =  $this->daba;
        $list->save();
        $mesage = ' تم  الإلغاء بسبب الرقم لا يجيب ل4  مرات '.  ' - '  . $this->daba;
        $this->saveHistory($list,$mesage);
    } 


    public function suivi_1($list){
        $list->suivi_tentative   = '1';
        $mesage = 'تم  تعيين ك لا يجيب 1 '.  ' - '. $this->daba ;
        $mesage .= '  من طرف ';
        $mesage .= $this->getusername();
        $this->saveHistory($list,$mesage);   
    }
    
    public function cancel_suivi($list){
        $list->suivi_tentative   = '2';
        $list->deleted_at        = $this->daba;
        $mesage = ' تم  الإلغاء بسبب الرقم لا يجيب ل2  مرات '.  ' - '  . $this->daba;
        $mesage .= '  من طرف ';
        $mesage .= $this->getusername();
        $this->saveHistory($list,$mesage);   
    }



    public function suivi_set($id){
        $list =  Lists::find($id);
        $no_answer = $list->suivi_tentative;
        if($no_answer == '') {
            return  $this->suivi_1($list);
        }
        if($no_answer == '1') {
            return  $this->cancel_suivi($list);
        }
    }
    
    public function check_and_set($id){
         $list =  Lists::find($id);
         $no_answer = $list->no_answer;
        
         if($no_answer == '') {
                return  $this->No_answer_1($list);
         }
        
         if($no_answer == 'no_answer_1') {
                return $this->No_answer_2($list);
         }
        
         if($no_answer == 'no_answer_2') {
                return $this->No_answer_3($list);
         }          
        
         if($no_answer == 'no_answer_3') {
                return $this->No_answer_4($list);
         } 
        
         if($no_answer == 'no_answer_4') {
                return $this->No_answer_5($list);
         }  

         if($no_answer == 'no_answer_5') {
                return $this->No_answer_6($list);
         }  
        
         if($no_answer == 'no_answer_6') {
                return $this->No_answer_7($list);
         } 
         
         if($no_answer == 'no_answer_7') {
                return $this->No_answer_8($list);
         } 
    }    
    
    


    
    
}