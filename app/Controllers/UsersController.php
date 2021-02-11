<?php

namespace App\Controllers;
use \App\Classes as classes;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use \App\Models\User;
use forxer\Gravatar\Gravatar;
use \App\Classes\files;
use Dompdf\Dompdf;


defined('BASEPATH') OR exit('No direct script access allowed');

class UsersController extends Controller{
  
    
    // index Page, Get all users
    public function index($request,$response) {
        $query = User::orderby('created_at','DESC');
        $users = $query->get();
        $count = $query->count();
        return $this->view->render($response, 'admin/users/index.twig', compact('users','count'));    
    }
    
    
    // Delete all users at once 
    public function blukdelete($request,$response){
        
        // get all the users exept the supper admin
        User::where('statue', '!=', 'supper')->delete();
        
        // flash succes and redirect
        $this->flashsuccess('تم حذف كل الأعضاء بنجاح');
        return $response->withHeader('Location', $this->router->pathFor('users'));
    }
    
    
    // Delete the user
    public function delete($request,$response,$args) {
        
        // get the id & the post
        $id = rtrim($args['id'], '/');
        $user = User::find($id);
        if($user) {
            
         if($user->statue == 'supper') {
                    $this->flasherror('لا يمكن حذف هذا العضو ');
                    return $response->withHeader('Location', $this->router->pathFor('users'));
                }
            else{
                                      $user->delete();
                    $this->flashsuccess('تم حذف العضو بنجاح');
                    return $response->withHeader('Location', $this->router->pathFor('users'));
            }       
        }
        
        // redirect to users Home
        return $response->withRedirect($this->router->pathFor('users'));  
    }
    
    
   
           
    
    
    // Create user
    public function create($request,$response) {
                if($request->getMethod() == 'POST'){  

                // Get the parameters Sent by the Form & initialize the helper 
                $post = clean($request->getParams());
                          
                $user = new User();
                foreach($post as $input  => $value){ 
                      $user->$input = $value; 
                }
                
                $user->save();

                $this->flashsuccess('تم اضافة المستخدم بنجاح');

                $route = $response->withRedirect($this->router->pathFor('users'));
                
                return $route;
        }else {
                    return $this->container->view->render($response,'admin/users/create.twig');
         }
    }
    
    
    
    
    public function edit($request,$response,$args) {

        $slug = clean($args['username']);
        
        // Get the user
        $user = User::where('username','=',$slug)->first();    
        
        
        if($request->getMethod() == 'GET'){
            if($user){
                return $this->container->view->render($response,'admin/users/edit.twig',compact('user'));
            }
            return $response->withHeader('Location', $this->router->pathFor('users'));
        }
        
        if($request->getMethod() == 'POST'){
            
            // Get the parameters Sent by the Form & initialize the helper & the fileupldader
            $post = clean($request->getParams());
            
            $route = $response->withRedirect($this->router->pathFor('users.edit', ['username'=> $user->username , 'user'=>$user]));
            
            
            
      


     
            // edit user info
            if($request->getParam('validate') == 'update-general-user-info'){
              
                
                // update the user info
                $user->password = !empty($post['password']) ? $post['password'] : $user->password;
                $user->username     = strtolower($post['username']);;
                $user->email        = strtolower($post['email']);
                $user->phone        = strtolower($post['phone']);
                $user->save();
                
                // update the session info
                
                
                $this->flashsuccess('تم تعديل المعلومات بنجاح');
                return $route;
                
            }
            
          
            
        
        
        }
        

    }    

  
   
   

  
    
}
