<?php

namespace App\Controllers;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use \App\Models\User;

defined('BASEPATH') OR exit('No direct script access allowed');

class AuthController extends Controller {
    
    public function getLogin($request,$response) {
        if(isset($_SESSION['auth-user'])){
            return $response->withRedirect($this->container->router->pathFor('website.home'));
        }
        return $this->container->view->render($response,'admin/admin/login.twig');
    } 
    
     
    public function attempt($email,$password) {

        $user = User::where('username',$email)->orwhere('email',$email)->orwhere('phone',$email)->first();
     
        if($user->password  === $password){
             $type = $user->role;
            if($type == 'data'){                    
                
                $_SESSION['auth-data'] = $user->id;
                $_SESSION['auth-user'] = $user->id;
                $_SESSION['auth-logged']  = $user;
                return $user;
            }
            if($type == 'admin'){
                $_SESSION['auth-admin'] = $user->id;
                $_SESSION['auth-user'] = $user->id;
                $_SESSION['auth-logged']  = $user;
                return $user;
            }
           if($type == 'deliver'){
                
                $_SESSION['auth-deliver'] = $user->id;
                $_SESSION['auth-user'] = $user->id;
                $_SESSION['auth-logged']  = $user;
                return $user;
            }
            if($type == 'employee'){
                
                $_SESSION['auth-employee'] = $user->id;
                $_SESSION['auth-user'] = $user->id;
                $_SESSION['auth-logged']  = $user;
                return $user;
            }
            if($type == 'stock'){
                $_SESSION['auth-stock'] = $user->id;
                $_SESSION['auth-user'] = $user->id;
                $_SESSION['auth-logged']  = $user;
                return $user;
            } 
            if($type == 'suivi'){
                $_SESSION['auth-suivi'] = $user->id;
                $_SESSION['auth-user'] = $user->id;
                $_SESSION['auth-logged']  = $user;
                return $user;
            } 

                           
        }
       
         return false; 
    }
    


    public function login($request,$response) {
        
        $post = clean($request->getParams());
        
        // get the login credentials
        $user = $post['user_login'];
        $pass = $post['pass_login'];
          
        // admin login
        $auth = $this->attempt($user,$pass);
        
        if($auth) {
            $type = $auth->role;
            if($type == 'employee'){
                return $response->withRedirect($this->url('base').'/employee/listing/new');
            }
            if($type == 'deliver'){
                return $response->withRedirect($this->url('base').'/deliver/listing/new');
            }
            if($type == 'admin'){
                return $response->withRedirect($this->container->router->pathFor('admin.index'));
            }
            if($type == 'stock'){
                return $response->withRedirect($this->container->router->pathFor('embalage'));
            }

            if($type == 'data'){
                return $response->withRedirect($this->container->router->pathFor('NewOrders'));
            }

            if($type == 'suivi'){
                return $response->withRedirect($this->container->router->pathFor('suivi.listing').'?type=suivi');
            }
                           
        }else {
            $this->flasherror('المعلومات غير صحيحة');
            return $response->withRedirect($this->container->router->pathFor('admin.index'));
        }
    }
    
    public function logout($request,$response) {


        session_start();


        unset($_SESSION['auth-user']);
        unset($_SESSION['auth-data']);
        unset($_SESSION['auth-admin']);
        unset($_SESSION['auth-deliver']);
        unset($_SESSION['auth-employee']);
        unset($_SESSION['auth-stock']);
        
        //clear session from globals
        $_SESSION = [];
        
        //clear session from disk
        session_destroy();
            
        return $response->withRedirect($this->container->router->pathFor('admin.index'));
    }
 
 
    
    
  
   
  
    

    
}

