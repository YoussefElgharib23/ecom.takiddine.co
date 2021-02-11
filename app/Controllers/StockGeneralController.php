<?php

namespace App\Controllers;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

use \App\Models\{Options , Lists,Product,DailyStock , SentLists , Cities , User , Stock , StockEntree , StockSortie,StockSortieList ,StockGeneral, HistoryEntree , Retour};

use \App\Classes\files;
use \App\Classes\Noanswser as ff;
use \App\Classes\SystemLog;
use Carbon\Carbon;
use Dompdf\Dompdf;


use \App\Controllers\MyController;


defined('BASEPATH') OR exit('No direct script access allowed');

class StockGeneralController extends Controller {
     

    public $folder = 'stockGeneral/';
    

        public function listretour($request, $response) {
          $retour = Retour::with('product','city','city.deliver')->get()->toArray();
          print_r($retour);
      //    exit;
          $file = 'admin/admin/retour.twig';
          return $this->view->render($response,$file,compact('retour'));    
        }





        public function rest($request, $response){
            
            $id = $_POST['id'];

            $HistoryEntree = HistoryEntree::where('productID',$id)     
            ->selectRaw('*, sum(quantity) as sum_quantity ,  sum(valid) as sum_valid ')
            ->get()->toArray();
            $entree =  $HistoryEntree[0]['sum_valid'];
            
           // dd( $HistoryEntree );


            $validSortie = StockSortieList::where('productID',$id)
                ->selectRaw('*, sum(quantity) as sum_quantity ,  sum(valid) as sum_valid ')
                ->get()->toArray();
            $sortie  =  $validSortie[0]['sum_valid'];

            $rest = $entree - $sortie;

            echo $rest;

        }


        public function waitingAction($request, $response){
            $file = $this->folder.'stock.twig';
            return $this->view->render($response, 'admin/'.$file);    
        }
          public function waitingGet($request, $response){
            $file = $this->folder.'acceptSortie.twig';
            return $this->view->render($response, 'admin/'.$file);    
        }
        
    
    
        public function saveRetour($request, $response){
            
            $post = $request->getParams();
            
            for($x=0; $x < count($post['quantities']);$x++){
                            
                $data  = [
                          'productID' => $post['productID'] ,
                          'quantity'  => $post['quantities'][$x],
                          'cityID' => $post['cities'][$x],
                ];
                \App\Models\Retour::create($data);
            }
            
            $entree = [
                'productID'  => $post['productID'] ,
                'quantity'  => array_sum($post['quantities']),
                'valid'  => array_sum($post['quantities']),
                'note'  => 'had stock dyal retour',
            ];

            HistoryEntree::create($entree);

            unset($entree);
            unset($post);
            
            $this->flashsuccess('تم الإضافة بنجاح');
            return $response->withRedirect($this->router->pathFor('stockGeneral'));
            
            
        }
    
    
    
        public function index($request, $response){


            $nots = $this->getStockGeneralNotification();
            
            $table = [];

            $HistoryEntree = HistoryEntree::groupBy('productID')      
            ->selectRaw('*, sum(quantity) as sum_quantity ,  sum(valid) as sum_valid ')
            ->get();

            foreach($HistoryEntree as $item ){
                if($item->product){
                    
                        $validSortie = StockSortieList::where('productID',$item->product->id)
                        ->selectRaw('*, sum(quantity) as sum_quantity ,  sum(valid) as sum_valid ')
                        ->get()->toArray();
        
                        $table[] = [
                            'product_id'   => $item->product->id,
                            'product_name' => $item->product->name,
                            'total_sortie' => $validSortie[0]['sum_valid'],
                            'total_entree' => $item->sum_valid ,
                            'rest' =>         $item->sum_valid - $validSortie[0]['sum_valid'] ,
                        ];
                            
                    
                }
               
                

            }

            $file = $this->folder.'stock.twig';
            return $this->view->render($response, 'admin/'.$file, [ 'nots'  => $nots , 'stockList'=> $table ] );    
        }
    
    

        public function create_entree($request,$response){
           
            $products = Product::all();
            $stocks = StockEntree::all();

            $HistoryEntree = HistoryEntree::groupBy('productID')
            
                        ->selectRaw('*, 
            sum(quantity) as sum_quantity , 
            sum(valid) as sum_valid
                       ')->get();
            
            $nots = $this->getStockGeneralNotification();
            
            $file = $this->folder.'entree.twig';
            return $this->view->render($response, 'admin/'.$file,compact('products','stocks','HistoryEntree','nots'));    
        }


        public function getStockGeneralNotification(){
            return [
               'entree'  => StockEntree::count(),
              'sortie'  => StockSortie::where('statue','=','')->count() 
            ];
        }
    
    
    
        public function store_entree($request,$response){
            $post = $request->getParams();
            StockEntree::create($post);
            $this->flashsuccess('تم الإضافة بنجاح');
            return $response->withRedirect($this->router->pathFor('stockGeneral.create.entree'));
        }
    
    
        public function activateStock($request,$response){
          $list = StockSortieList::where('sortie_list_id',$_POST['id']);
        }

        public function loadSortieList($request,$response){
          $list = StockSortieList::where('sortie_list_id',$_POST['id'])->get();
          $view     = 'admin/elements/sortieList.twig';
          return $this->view->render($response, $view , compact('list'));
        }
    

       public function loadHistorySortie($request,$response){
          $list = StockSortieList::with('city','product')->where('productID',$_POST['productID'])->get()->toArray();
          $view     = 'admin/elements/History-sortie.twig';
          return $this->view->render($response, $view , compact('list'));
        }
    

    
     public function validateSortieList ($request,$response){

         $post = $request->getParams();
          
        $sortieRow = StockSortie::find($post['SortieListID']);
        // delete Old Sortie Stock list
        StockSortieList::where('sortie_list_id',$post['SortieListID'])->delete();
        
        // Create new Sortie List 
        for($x=0; $x < count($post['cities']);$x++){
                        
            $data  = [
                      'productID' => $sortieRow->ProductID ,
                      'sortie_list_id' => $post['SortieListID'],
                      'quantity'  => $post['quantities'][$x],
                      'valid'  => $post['valid'][$x],
                      'cityID' => $post['cities'][$x],
                      'statue' => 1,
            ];
            StockSortieList::create($data);
        }
         
         
       // set the sortie list to valid
       $edit = StockSortie::find($post['SortieListID']);
       $edit->statue = 1;
       $edit->save();
             
         
        // add the stock sortie to stockgeneral
         
      
        // add the stock to delivers stock
        for($x=0;$x< count($post['cities']);$x++){
            validateSortieItem($post['ProductID'],$post['cities'][$x],$post['valid'][$x]);
        } 
         
         $this->flashsuccess('تم التعديل بنجاح');
            return $response->withRedirect($this->router->pathFor('stockGeneral.create.sortie'));  
    
     }
    
    
    
    public function addStockToStockPrincipale($CityID,$productID,$quantity){
             $Stock = Stock::where('CityID',$CityID)->where('ProduitID',$productID)->first();
             $Stock->Recue =  $Stock->Recue + $quantity;
             $Stock->StockPhisique =  $Stock->StockPhisique + $quantity;
             $Stock->save();
    }
    
     // change the Livred Products Stock
    public function addLivredStock($cityID,$productID,$quantity){
        $stockFound = Stock::where('CityID',$cityID)->where('ProduitID',$productID)->first();
        if($stockFound){
            $stockFound->Livre  =  is_numeric($stockFound->Livre) ? $stockFound->Livre + $quantity : $quantity ; 
            $stockFound->StockPhisique =  is_numeric($stockFound->StockPhisique) ? $stockFound->StockPhisique - $quantity : $quantity ;
            $stockFound->stockEnCours  =  is_numeric($stockFound->stockEnCours) ? $stockFound->stockEnCours - $quantity  : $quantity ;
            $stockFound->save();
        }
    }
    
    
    public function loadEntreeHistory($request,$response){
         
         $data = HistoryEntree::where('productID',$_POST['productID'])->get();
         
         $html =  '<table class="table table-striped datatable entreetable" >
                        <thead>
                            <tr>
                                <th><b> التاريخ </b></th>
                                <th><b> المنتوج </b></th>
                                <th><b> ملاحظة </b></th>
                                <th><b> الكمية </b></th>
                                <th><b> تأكيد  </b></th>
                            </tr>
                        </thead>
                        <tbody> ';
        
         foreach($data as $item):
         
             $html .= "<tr>
                            <td >{$item->created_at}</td>
                             <td>".$item->product->name."</td>
                             <td>$item->note</td>
                             <td> $item->quantity </td>
                             <td>$item->valid </td>
                         </tr>";
              
              
         endforeach;
                 
                             
            $html .= '</tbody></table>';
              
         echo $html;
         
         
     }
    
    
    
    
    
    
        public function validateEntree($request,$response){
            
            // get the stock entree
            $entree = StockEntree::find($_POST['id']);
            
            // add the stock entree to history
            $new = new HistoryEntree();
            $new->productID = $entree->productID;
            $new->quantity = $entree->quantity;
            $new->note = $entree->note;
            $new->valid = $_POST['valid'];
            $new->save();
         
           
            // search and check if the stock general has already this product
            $stock = StockGeneral::where('ProductID',$entree->productID)->first();
            
            
            if($stock) {
                
             // if the stock general has already this product this add the new quantity to the existing quantity
             if(!empty($stock->Entree) and is_numeric($stock->Entree)){
                        $stock->Entree = $stock->Entree + $_POST['valid'];
              }else {
                        $stock->Entree = $_POST['valid'];
              }
              
                $stock->save();
                
             }else {
                    // if it dosnt exist add it
                   StockGeneral::create(['Entree'=>$_POST['valid'] ,'ProductID'=>$entree->productID]); 
              }
             
           $entree->delete();
        }

    
       
        
        // Ajout de Stock Sortie
        public function store_sortie($request,$response){
           
           $post = $request->getParams();
         
           $sortie = StockSortie::create(['ProductID'=>$post['productID']]);

            for($x=0; $x < count($post['quantities']);$x++){
                $data  = [
                    'sortie_list_id' => $sortie->id,
                    'quantity'  => $post['quantities'][$x],
                    'cityID' => $post['cities'][$x],
                         ];
                st($post['quantities'][$x]);
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
            return $response->withRedirect($this->router->pathFor('stockGeneral.create.sortie'));
           
        }

        public function create_sortie($request,$response){
            $sortie = StockSortie::where('statue','=','')->get();
           
            
              $HistorySortie = StockSortieList::groupBy('productID')
            
                        ->selectRaw('*, 
            sum(quantity) as sum_quantity , 
            sum(valid) as sum_valid
                       ')->get();
            
            $nots = $this->getStockGeneralNotification();

            $file = $this->folder.'sortie.twig';
            return $this->view->render($response, 'admin/'.$file,compact('sortie','products','HistorySortie','nots'));    
        
        }    

        
    
    
    
}

