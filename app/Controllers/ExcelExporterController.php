<?php

namespace App\Controllers;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use \App\Models\{Lists , Product , Cities , User , MultiSale } ;
use Carbon\Carbon;
use \App\Helpers\{Noanswer , Listing,Revenue , Statue};


defined('BASEPATH') OR exit('No direct script access allowed');

class ExcelExporterController extends Controller {
    
    
// get the lists for employees
    public function export($request,$response){
        
        
            $post = clean($request->getParams());

           
            $stream = fopen('php://memory', 'w+');
            fwrite($stream, chr(0xEF) . chr(0xBB) . chr(0xBF));
            
            // Add header
            $columns = [
                'date',
                'nom et prenom',
                'Telephone',
                'Ville',
                'Adresse',
                'Ref',
                'store',
                'quantity',
                'prix (DH)'
            ];

            $listing        = new \App\Helpers\Listing( $post);
            $users          = $listing->list();
                              
            $filename = date('Y-m-d') . '_cmds.csv';
            $fh = @fopen( 'php://output', 'w' );

            header( 'Cache-Control: must-revalidate, post-check=0, pre-check=0' );
            header( 'Content-Description: File Transfer' );
            header('Content-type: text/csv; charset=UTF-8');
            header('Content-Encoding: UTF-8');
            header('Content-Transfer-Encoding: binary');
            header( "Content-Disposition: attachment; filename={$filename}" );
            header( 'Expires: 0' );
            header( 'Pragma: public' );
            header('Content-Encoding: UTF-8');
            header('Content-type: text/csv; charset=UTF-8');

            echo "\xEF\xBB\xBF";
        
            fputcsv( $fh, $columns ,';');
            $city = $user['city']['city_name'] ?? '';
            $product = $user['products'][0]['product']['name'] ?? '';
            $quantity = $user['products'][0]['quanity'] ?? '';
            $price   = $user['products'][0]['price'] ?? '';
            foreach ($users as $user) {
                  $data = [
                    $user['created_at'],
                    $user['name'],
                    $user['tel'],
                    $city,
                    $user['adress'],
                    $product,
                    $user['source'],
                    $quantity,
                    $price,
                ];
                
                fputcsv($fh, $data, ';');
            }
            
            fclose( $fh );
    }
    
   
    // get the lists for employees
    public function exportDeliverSelected($request,$response){
        
        
             
       // dd('dlkdkld');
    
    
    
            $ids = explode(',',$request->getParam('selectedToExport')) ?? [];
            $lists =  \App\Models\Lists::with('deliver','employee','products','products.product','city')->whereIn('id', $ids);
            
            if( !is_null($request->getParam('type')) and $request->getParam('type') == 'driver_delivred'){
                $lists = $lists->orderBy('delivred_at','DESC');
            }
            
            
            
            
            $lists = $lists->get()->toArray();

            $stream = fopen('php://memory', 'w+');
            fwrite($stream, chr(0xEF) . chr(0xBB) . chr(0xBF));
            
            // Add header
            $columns = [
                'order N',
                'nom et prenom',
                'Telephone',
                'Ville',
                'Adresse',
                'Ref',
                'store',
                'prix (DH)',
                'statue',
            ];
            
            if( !is_null($request->getParam('type')) and $request->getParam('type') == 'driver_delivred'){
               $columns[] = 'date de laivraison' ;
            }
            
            
         //dd($columns);
            
            $filename = date('Y-m-d') . '_cmds.csv';
            $fh = @fopen( 'php://output', 'w' );

            header('Cache-Control: must-revalidate, post-check=0, pre-check=0' );
            header('Content-Description: File Transfer' );
            header('Content-type: text/csv; charset=UTF-8');
            header('Content-Encoding: UTF-8');
            header('Content-Transfer-Encoding: binary');
            header("Content-Disposition: attachment; filename={$filename}" );
            header('Expires: 0' );
            header('Pragma: public' );
            header('Content-Encoding: UTF-8');
            header('Content-type: text/csv; charset=UTF-8');

            echo "\xEF\xBB\xBF";
        
            fputcsv( $fh, $columns ,';');
            
            
            foreach ($lists as $list) {
                
                  $products = '';
                  foreach($list['products'] as $product){
                      if(count($list['products']) == 1 ) {
                          $products .= $product['product']['name']. $product['quanity'] . ' = ' . $product['price'] ;
                      }else{
                        $products .= $product['product']['name'] . $product['quanity'] . ' = ' . $product['price'] . '|';    
                      }
                      
                  }
                   $city = $list['city']['city_name'] ?? '';

                  $data = [
                    $list['id'],
                    $list['name'],
                    $list['tel'],
                    $city,
                    $list['adress'],
                    $products,
                    $list['source'],
                    $list['total'],
                    $list['type'],
                ];
                
                if( !is_null($request->getParam('type')) and $request->getParam('type') == 'driver_delivred'){
                   $data[] = $list['delivred_at'];
                }
                
                
                fputcsv($fh, $data, ';');
            }
            

            fclose( $fh );
    }
    
   
    // get the lists for employees
    public function exportConfirmation($request,$response){
         
            $post = clean($request->getParams());

            $stream = fopen('php://memory', 'w+');
            fwrite($stream, chr(0xEF) . chr(0xBB) . chr(0xBF));
            
            // Add header
            $columns = [
                'date',
                'nom et prenom',
                'Telephone',
                'Ville',
                'Adresse',
                'Ref',
                'store',
                'quantity',
                'prix (DH)'
            ];

            $listing        = new \App\Helpers\Listing( $post);
            $users          = $listing->list();
                              
            $filename = date('Y-m-d') . '_cmds.csv';
            $fh = @fopen( 'php://output', 'w' );

            header( 'Cache-Control: must-revalidate, post-check=0, pre-check=0' );
            header( 'Content-Description: File Transfer' );
            header('Content-type: text/csv; charset=UTF-8');
            header('Content-Encoding: UTF-8');
            header('Content-Transfer-Encoding: binary');
            header( "Content-Disposition: attachment; filename={$filename}" );
            header( 'Expires: 0' );
            header( 'Pragma: public' );
            header('Content-Encoding: UTF-8');
            header('Content-type: text/csv; charset=UTF-8');

            echo "\xEF\xBB\xBF";
        
            fputcsv( $fh, $columns ,';');
            
            foreach ($users as $user) {
            $city = $user['city']['city_name'] ?? '';
            $product = $user['products'][0]['product']['name'] ?? '';
            $quantity = $user['products'][0]['quanity'] ?? '';
            $price   = $user['products'][0]['price'] ?? '';

                  $data = [
                    $user['created_at'],
                    $user['name'],
                    $user['tel'],
                    $city,
                    $user['adress'],
                    $product,
                    $user['source'],
                    $quantity,
                    $price,
                ];
                
                fputcsv($fh, $data, ';');
            }
            
            fclose( $fh );
    }
  


    public function exportDeliver($request,$response){
         $deliver = Deliver();

         if(isset($deliver) and is_numeric($deliver)){
            $stream = fopen('php://memory', 'w+');
            fwrite($stream, chr(0xEF) . chr(0xBB) . chr(0xBF));
            
            // Add header
            $columns = [
                'date',
                'nom et prenom',
                'Telephone',
                'Ville',
                'Adresse',
                'Ref',
                'store',
                'quantity',
                'prix (DH)'
            ];

            $listing        = new \App\Helpers\Listing(['deliver'=> $deliver]);
            $users          = $listing->list();
                              
            $filename = date('Y-m-d') . '_cmds.csv';
            $fh = @fopen( 'php://output', 'w' );

            header( 'Cache-Control: must-revalidate, post-check=0, pre-check=0' );
            header( 'Content-Description: File Transfer' );
            header('Content-type: text/csv; charset=UTF-8');
            header('Content-Encoding: UTF-8');
            header('Content-Transfer-Encoding: binary');
            header( "Content-Disposition: attachment; filename={$filename}" );
            header( 'Expires: 0' );
            header( 'Pragma: public' );
            header('Content-Encoding: UTF-8');
            header('Content-type: text/csv; charset=UTF-8');

            echo "\xEF\xBB\xBF";
        
            fputcsv( $fh, $columns ,';');
            
            foreach ($users as $user) {
            $city = $user['city']['city_name'] ?? '';
            $product = $user['products'][0]['product']['name'] ?? '';
            $quantity = $user['products'][0]['quanity'] ?? '';
            $price   = $user['products'][0]['price'] ?? '';

                  $data = [
                    $user['created_at'],
                    $user['name'],
                    $user['tel'],
                    $city,
                    $user['adress'],
                    $product,
                    $user['source'],
                    $quantity,
                    $price,
                ];
                
                fputcsv($fh, $data, ';');
            }
            
            fclose( $fh );

         }
      


    }

    

    public function exportData($request,$response){
        
         
            $ids = explode(',',$request->getParam('selectedToExportData')) ?? [];
            $lists =  \App\Models\NewOrders::whereIn('id', $ids)->get()->toArray();

            $stream = fopen('php://memory', 'w+');
            fwrite($stream, chr(0xEF) . chr(0xBB) . chr(0xBF));
            
            // Add header
            $columns = [
                'order N',
                'nom et prenom',
                'Telephone',
                'Ville',
                'Adresse',
                'Ref',
                'store',
                'prix (DH)',
            ];
            
            
            $filename = date('Y-m-d') . '_cmds.csv';
            $fh = @fopen( 'php://output', 'w' );

            header('Cache-Control: must-revalidate, post-check=0, pre-check=0' );
            header('Content-Description: File Transfer' );
            header('Content-type: text/csv; charset=UTF-8');
            header('Content-Encoding: UTF-8');
            header('Content-Transfer-Encoding: binary');
            header("Content-Disposition: attachment; filename={$filename}" );
            header('Expires: 0' );
            header('Pragma: public' );
            header('Content-Encoding: UTF-8');
            header('Content-type: text/csv; charset=UTF-8');

            echo "\xEF\xBB\xBF";
        
            fputcsv( $fh, $columns ,';');
            
            
            foreach ($lists as $list) {
                
                  $data = [
                    $list['id'],
                    $list['name'],
                    $list['tel'],
                    $list['city'],
                    $list['adress'],
                    $list['ProductReference'],
                    $list['source'],
                    $list['price'],
                ];
                
                fputcsv($fh, $data, ';');
            }
            

            fclose( $fh );


    }

}

