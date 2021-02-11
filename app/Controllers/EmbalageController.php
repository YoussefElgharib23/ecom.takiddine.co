<?php

namespace App\Controllers;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

// Models
use \App\Models\{ Options , Lists , Product , DailyStock , SentLists , Cities , StockGeneral  };
use \App\Models\{ User , Stock , MultiSale, Sources , StockEntree  , StockSortie  , StockSortieList , HistoryEntree };

// Classes And Libraries
use \App\Classes\{files , SystemLog};
use \App\Classes\Noanswser as ff;

use Carbon\Carbon;
use Dompdf\Dompdf;

defined('BASEPATH') OR exit('No direct script access allowed');

class EmbalageController extends Controller {
     
     
     
     
    public function getSotieTotal($request,$response){
        
        
        $id = $_POST['id'];
        
         $HistoryEntree = HistoryEntree::where('productID',$id)     
        ->selectRaw('*, sum(quantity) as sum_quantity ,  sum(valid) as sum_valid ')
        ->get()->toArray();
        
        
        $validSortie = StockSortieList::where('productID',$id)
            ->selectRaw('*, sum(quantity) as sum_quantity ,  sum(valid) as sum_valid ')
            ->get()->toArray();
        
        // حساب المتبقي لذلك المنتوج
        $rest = $HistoryEntree[0]['sum_valid'] - $validSortie[0]['sum_valid'];
        
        $entreeNoValidee = StockEntree::where('productID',$id)
        ->selectRaw('*, sum(quantity) as sum_quantity')
        ->get()->toArray();
        
        $entred  = $entreeNoValidee[0]['sum_quantity'];
        echo $rest + $entred;
        
 
    }
     
    
    public function checkSumEntree($request,$response){
        
        
        $id = $_POST['id'];
        $sum = $_POST['sum'];
        $list = StockSortieList::where('productID',$sum)->get();
        return '1';
        
    }
    
    public function index($request,$response) {
        $table = [];
$viewPage = 'rest';
        $HistoryEntree = HistoryEntree::groupBy('productID')      
        ->selectRaw('*, sum(quantity) as sum_quantity ,  sum(valid) as sum_valid ')
        ->get();
        
        foreach($HistoryEntree as $item ){
            
            $validSortie = StockSortieList::where('productID',$item->product->id)
            ->selectRaw('*, sum(quantity) as sum_quantity ,  sum(valid) as sum_valid ')
            ->get()->toArray();
            
            $table[] = [
                'product_id' => $item->product->id,
                'product_name' => $item->product->name,
                'total_sortie' => $validSortie[0]['sum_valid'],
                'total_entree' =>  $item->sum_valid ,
            ];
        }
        
        return $this->view->render($response, 'admin/embalage/stock.twig', [ 'stockList'=> $table , 'view' => $viewPage]);
    }    
    
    
    public function sortie($request,$response) {   
        
        $viewPage = 'sortie';
        
        
        $HistorySortie = StockSortieList::groupBy('productID')

                    ->selectRaw('*, 
        sum(quantity) as sum_quantity , 
        sum(valid) as sum_valid
                   ')->get();

        
        return $this->view->render($response, 'admin/embalage/sortie.twig',[ 'HistorySortie' => $HistorySortie , 'view' => $viewPage ]);
    }  
    
    
    public function entree($request,$response) {
          $viewPage = 'entree';
        $HistoryEntree = HistoryEntree::groupBy('productID')->selectRaw('*, 
        sum(quantity) as sum_quantity ,
                sum(valid) as sum_valid')->get();
        return $this->view->render($response, 'admin/embalage/entree.twig',compact('HistoryEntree','viewPage'));
    }  





    public function createproduct($request,$response) {
        if($request->getMethod() == 'GET'){ 
          return $this->view->render($response, 'admin/embalage/createProduct.twig');
        }
          
        if($request->getMethod() == 'POST'){ 

            $post   = $request->getParams();
            Product::create($post);
            $this->flashsuccess('تم اضافة المنتوج بنجاح');
            return $response->withRedirect($this->router->pathFor('embalage'));
        } 
    }  




    public function storeStock($request,$response) {
        $post = $request->getParams();
        
        $post['created_at'] = \Carbon\Carbon::parse($post['dateofentree']);
        unset($post['dateofentree']);
        
        StockEntree::create($post);
        
        $this->flashsuccess('تم الإضافة بنجاح');

        return $response->withRedirect($this->router->pathFor('embalage'));

    } 





    public function storeStockSortie($request,$response) {
           $post = $request->getParams();
             
            $date = \Carbon\Carbon::parse($post['dateofsortie']);
             
            $sortie = StockSortie::create(['ProductID'=>$post['productID'],'saved_at' => $date ]);

            for($x=0; $x < count($post['quantities']);$x++){
                $data  = [
                    'sortie_list_id' => $sortie->id,
                    'quantity'  => $post['quantities'][$x],
                    'cityID' => $post['cities'][$x],
                    'productID' => $sortie->ProductID,
                         ];
                StockSortieList::create($data);
                $Stock = Stock::where('CityID',$post['cities'][$x])->where('ProduitID',$post['productID'])->first();
                if($Stock){
                   $Stock->stockVirtuel = $post['quantities'][$x];
                   $Stock->save(); 
                }else {
                    Stock::create(['CityID' => $post['cities'][$x] , 
                                  'ProduitID' => $post['productID'] ,
                                  'stockVirtuel' => $post['quantities'][$x] ,
                    ]);
                }
                
                
            }
            
            
            $this->flashsuccess('تم الإضافة بنجاح');         
        return $response->withRedirect($this->router->pathFor('embalage'));
    } 




}
