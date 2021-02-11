<?php

namespace App\Controllers;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;



defined('BASEPATH') OR exit('No direct script access allowed');

class Controller {
    
    protected $container;
    protected $lang;
    protected $user;


    

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
         

    public function init( $model = false ) {
        if($model){
              return '\\App\\Models\\' . ucfirst($model);
        }
        if($this->model) {
            return '\\App\\Models\\' . ucfirst($this->model);
        }
        return false;
    }
    
    
    public function __construct($container){
        
       $this->container = $container; 
       $this->lang      = $_SESSION['l'] ?? [];
       
       
       if(!isset($_SESSION['user_account'])){

            $_SESSION['user_account'] = \App\Models\User::find($_SESSION['auth-user']);

            $this->user      = isset($_SESSION['auth-user']) ? \App\Models\User::find($_SESSION['auth-user']) : []; 
       } else {

            $this->user   = $_SESSION['user_account'];
       }
       

    } 
    
    public function dir($dir){
       return $this->container->conf['dir.'.$dir];
    }
    public function url($url){
        return $this->container->conf['url.'.$url];
    }
    
    public function flash($message, $type = 'success'){
        return $_SESSION['flash'][$type] = $message;
    }
    
    public function flasherror($message){
        return $this->flash->addMessage('error',$message);
    } 
    
    public function flashsuccess($message){
        return $this->flash->addMessage('success',$message);
    } 
    
    
    public function __get($name){
        return $this->container->$name;
    }
    
    
    
}