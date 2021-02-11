<?php

namespace App\Controllers;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use \App\Models\Product;
use \App\Helpers\Paginator;

defined('BASEPATH') OR exit('No direct script access allowed');

class ProductsController extends Controller{
    
    

    // index Page
    public function index($request,$response) {
        $model          = new Product();
        $count          = $model->count();         
        $page           = ($request->getParam('page', 0) > 0) ? $request->getParam('page') : 1;
        $limit          = 200; 
        $lastpage       = (ceil($count / $limit) == 0 ? 1 : ceil($count / $limit));   
        $skip           = ($page - 1) * $limit;
        $products         = $model->skip($skip)->take($limit)->orderBy('created_at', 'desc')->get();
        $urlPattern     = "?page=(:num)";
        $paginator = new Paginator($count, $limit, $page, $urlPattern);
        return $this->view->render($response, 'admin/products/index.twig', ['products'=> $products,'p'=>$paginator]);    
    }
    
    
    
    public function checkexist($name){
        return \App\Models\Product::where('name',$name)->count();
    }
    
    public function create($request,$response){
        if($request->getMethod() == 'GET'){ 
          return $this->view->render($response,'admin/products/create.twig');
        }
        if($request->getMethod() == 'POST'){ 
            $post   = clean($request->getParams());
            
            $name = $post['name'];
            if( $this->checkexist($name) > 0  ){
                
                // error & redirect
                $this->flasherror('المنتوج موجود من قبل');
                return $response->withRedirect($this->router->pathFor('products.create'));
            }
            
            Product::create($post);
            unset($_SESSION['products']);
            $this->flashsuccess('تم اضافة المنتوج بنجاح');
            return $response->withRedirect($this->router->pathFor('products'));
        }
    }
    

    public function edit($request,$response,$args){
        
        
            // get the product id
            $id = rtrim($args['id'], '/');
            $product = Product::find($id);

        
            
            // show the edit page 
            if($request->getMethod() == 'GET'){ 
               return $this->view->render($response,'admin/products/edit.twig',compact('product'));
            }
        
            if($request->getMethod() == 'POST'){

                $post  = clean($request->getParams());            
                foreach($post as $field => $value ){
                    $product->$field = $value;
                }
            
                $product->save();

                // success & redirect
                $this->flashsuccess('تم تحديث المنتوج بنجاح');
                return $response->withRedirect($this->router->pathFor('products'));
            
        }
        
        
        
    }

    public function delete($request,$response,$args) {
        
        // get the product id
        $id = rtrim($args['id'], '/');
        
        $path = $this->container->conf['dir.products'];
        
        // Get the Product
        $product = Product::find($id);
        
        
        if($product){
            // Delete the Product
            $product->delete();
            $this->flashsuccess('تم حذف المنتوج بنجاح');
            
        }
        
        return $response->withRedirect($this->router->pathFor('products'));
        
    }
    
    
    
    
}
 