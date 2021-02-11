<?php

namespace App\Controllers;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use \App\Helpers\{Revenue,Cash,Search,Double,Statistiques};
use \App\Models\{User,DailyStock,Api};
use Illuminate\Database\Capsule\Manager as DB;


defined('BASEPATH') OR exit('No direct script access allowed');

class PagesController extends Controller {
        
    
    
    public function getstats($from=false,$to=false){
        return [
            'delivers' => GetAllDeliversStats($from,$to),
            'employees' =>GetAllEmployeesStats($from,$to)
        ];
    }
    
    public function others($request,$response){
        $view       = 'stats/others.twig';
        $cities   = (new \App\Helpers\Stats())->cities();
        $products = (new \App\Helpers\Stats())->products();
        $cash     = (new \App\Helpers\Cash)->list();   
        $stats    = $this->getstats();
        return $this->view->render($response, $view,compact('cash','stats','earned','cities','products'));
    }      
    
    
    public function statistiques($request,$response){
        $params     = $request->getParams();
        $view       = 'admin/admin/statistiques.twig';
        $employees = (new \App\Helpers\Stats\Employee())->list();
        $delivers = (new \App\Helpers\Stats\Deliver())->list();
        return $this->view->render($response, $view , compact('employees','delivers'));
    }         
        


    public function vs(){
        $providers = [];
        foreach ( $_SESSION['employees'] as $provider) {
            $providers[$provider['id']] = $provider['username'];
        }
        return $providers;
    }

    public function statistiques_employees($request,$response){

  
        $params     = $request->getParams();
        $waiting    = Api::selecting()->duration($params)->WaitingForEmployees()->OrderCreated()->get()->toArray();
        $canceled   = Api::selecting()->duration($params)->CanceledForEmployees()->OrderCreated()->get()->toArray();
        $unanswered = Api::selecting()->duration($params)->UnanswredForEmployees()->OrderCreated()->get()->toArray();
        $recall     = Api::selecting()->duration($params)->RecallForEmployees()->OrderCreated()->get()->toArray();
        $delivred   = Api::selecting()->duration($params)->DelivredForProviders()->OrderCreated()->get()->toArray();
        $sent       = Api::selecting()->duration($params)->SentForEmployees()->OrderCreated()->get()->toArray();


        $employees  = $this->vs();

       
        $data       = [];
        $starter    = ['all'=> 0 ,'waiting'=> 0 ,'canceled'=> 0 ,'noanswer'=> 0 ,'recall'=> 0 , 'delivred' => 0 , 'sent' => 0 ];


        $totalwaiting = count($waiting);
        $totalcanceled = count($canceled);
        $totalunanswered = count($unanswered);
        $totalrecall = count($recall);
        $totaldelivred = count($delivred);
        $totalsent = count($sent);

        if(!isset($params['from']) and !isset($params['to'])){

                foreach ($sent as $list ) {
            
                    $day = explode(' ',$list['created_at'])[0];
                    $provider = $employees[$list['mowadafaID']];
                    if(!isset($data[$day][$provider])){
                            $data[$day][$provider] = $starter;
                    }

                    $data[$day][$provider]['all'] += 1;
                    $data[$day][$provider]['sent'] += 1;
                }                     


                foreach ($waiting as $list ) {
            
                    $day = explode(' ',$list['created_at'])[0];
                    $provider = $employees[$list['mowadafaID']];
                    if(!isset($data[$day][$provider])){
                            $data[$day][$provider] = $starter;
                    }

                    $data[$day][$provider]['all'] += 1;
                    $data[$day][$provider]['waiting'] += 1;
                }   

                foreach ($canceled as $list ) {
            
                    $day = explode(' ',$list['created_at'])[0];
                    $provider = $employees[$list['mowadafaID']];

                    if(!isset($data[$day][$provider])){
                        $data[$day][$provider] = $starter;
                    }

                    $data[$day][$provider]['all'] += 1;
                    $data[$day][$provider]['canceled'] += 1;
                }   



                foreach ($unanswered as $list ) {
            
                    $day = explode(' ',$list['created_at'])[0];
                    $provider = $employees[$list['mowadafaID']];

                    if(!isset($data[$day][$provider])){
                        $data[$day][$provider] = $starter;
                    }

                    $data[$day][$provider]['all'] += 1;
                    $data[$day][$provider]['noanswer'] += 1;
                }   


                foreach ($recall as $list ) {
            
                    $day = explode(' ',$list['created_at'])[0];
                    $provider = $employees[$list['mowadafaID']];

                    if(!isset($data[$day][$provider])){
                        $data[$day][$provider] = $starter;
                    }

                    $data[$day][$provider]['all'] += 1;
                    $data[$day][$provider]['recall'] += 1;
                }   


                foreach ($delivred as $list ) {
            
                    $day = explode(' ',$list['created_at'])[0];
                    $provider = $employees[$list['mowadafaID']];

                    if(!isset($data[$day][$provider])){
                         $data[$day][$provider] = $starter;
                    }

                    $data[$day][$provider]['delivred'] += 1;
                }


                $employees = [];

                foreach ($data as $key => $days) {
                   
                   $new = [];

                   foreach ($days as  $provider => $day) {

                      $y = $day['all'];
                      $waiting = $day['waiting'];
                      $canceled = $day['canceled'];
                      $noanswer = $day['noanswer'];
                      $recall = $day['recall'];
                      $delivred = $day['delivred'];
                      $sent = $day['sent'];
                    
                      $day['waitingPercent'] = $waiting == 0 ? '0%' : number_format( ($waiting / $y ) * 100, 2 ) . '%';
                      $day['canceledPercent'] = $canceled == 0 ? '0%' : number_format( ($canceled / $y ) * 100, 2 ) . '%';
                      $day['noanswerPercent'] = $noanswer == 0 ? '0%' : number_format( ($noanswer / $y ) * 100, 2 ) . '%';
                      $day['recallPercent'] = $recall == 0 ? '0%' : number_format( ($recall / $y ) * 100, 2 ) . '%';
                      $day['delivredPercent'] = $delivred == 0 ? '0%' : number_format( ($delivred / $y ) * 100, 2 ) . '%';
                      $day['sentPercent'] = $sent == 0 ? '0%' : number_format( ($sent / $y ) * 100, 2 ) . '%';

                      $new[$provider] = $day;
                   }

                   $new['date'] = $key; 
                   $employees[$key] = $new; 

                }

              
                usort($employees, array($this, "employeesSort"));


                unset($new,$day,$y,$days,$waiting,$canceled,$noanswer,$recall,$delivred,$data,$provider,$providers,$starter);
                $params     = $request->getParams();
                $view       = 'stats/dailyEmployees.twig';
                return $this->view->render($response, $view , compact('totalwaiting','totaldelivred','totalcanceled','totalunanswered','totalrecall','employees','params'));
        }



        $fromToKey = $params['from'] . ' - ' . $params['to'];


    
                foreach ($sent as $list ) {
                    $provider = $employees[$list['mowadafaID']];
                    if(!isset( $data[$fromToKey][$provider])){
                          $data[$fromToKey][$provider] = $starter;
                    }
                    $data[$fromToKey][$provider]['all'] += 1;
                    $data[$fromToKey][$provider]['sent'] += 1;
                }                     


                foreach ($waiting as $list ) {
            
                    $provider = $employees[$list['mowadafaID']];
                    if(!isset( $data[$fromToKey][$provider])){
                          $data[$fromToKey][$provider] = $starter;
                    }
                    $data[$fromToKey][$provider]['all'] += 1;
                    $data[$fromToKey][$provider]['waiting'] += 1;
                }   

                foreach ($canceled as $list ) {
                    $provider = $employees[$list['mowadafaID']];
                    if(!isset( $data[$fromToKey][$provider])){
                          $data[$fromToKey][$provider] = $starter;
                    }
                    $data[$fromToKey][$provider]['all'] += 1;
                    $data[$fromToKey][$provider]['canceled'] += 1;
                }   



                foreach ($unanswered as $list ) {
            
                    $provider = $employees[$list['mowadafaID']];
                    if(!isset( $data[$fromToKey][$provider])){
                          $data[$fromToKey][$provider] = $starter;
                    }
                    $data[$fromToKey][$provider]['all'] += 1;
                    $data[$fromToKey][$provider]['noanswer'] += 1;
                }   


                foreach ($recall as $list ) {
            
                    $provider = $employees[$list['mowadafaID']];
                    if(!isset( $data[$fromToKey][$provider])){
                          $data[$fromToKey][$provider] = $starter;
                    }
                    $data[$fromToKey][$provider]['all'] += 1;
                    $data[$fromToKey][$provider]['recall'] += 1;
                }   


                foreach ($delivred as $list ) {
                    $provider = $employees[$list['mowadafaID']];
                    if(!isset( $data[$fromToKey][$provider])){
                          $data[$fromToKey][$provider] = $starter;
                    }
                    $data[$fromToKey][$provider]['delivred'] += 1;
                }


                $employees = [];

                foreach ($data as $key => $days) {
                   
                   $new = [];

                   foreach ($days as  $provider => $day) {

                      $y = $day['all'];
                      $waiting = $day['waiting'];
                      $canceled = $day['canceled'];
                      $noanswer = $day['noanswer'];
                      $recall = $day['recall'];
                      $delivred = $day['delivred'];
                      $sent = $day['sent'];
                    
                      $day['waitingPercent'] = $waiting == 0 ? '0%' : number_format( ($waiting / $y ) * 100, 2 ) . '%';
                      $day['canceledPercent'] = $canceled == 0 ? '0%' : number_format( ($canceled / $y ) * 100, 2 ) . '%';
                      $day['noanswerPercent'] = $noanswer == 0 ? '0%' : number_format( ($noanswer / $y ) * 100, 2 ) . '%';
                      $day['recallPercent'] = $recall == 0 ? '0%' : number_format( ($recall / $y ) * 100, 2 ) . '%';
                      $day['delivredPercent'] = $delivred == 0 ? '0%' : number_format( ($delivred / $y ) * 100, 2 ) . '%';
                      $day['sentPercent'] = $sent == 0 ? '0%' : number_format( ($sent / $y ) * 100, 2 ) . '%';

                      $new[$provider] = $day;
                   }

                   $new['date'] = $key; 
                   $employees[$key] = $new; 

                }

              
                usort($employees, array($this, "employeesSort"));


                unset($new,$day,$y,$days,$waiting,$canceled,$noanswer,$recall,$delivred,$data,$provider,$providers,$starter);
                $params     = $request->getParams();
                $view       = 'stats/dailyEmployees.twig';
                return $this->view->render($response, $view , compact('totalwaiting','totaldelivred','totalcanceled','totalunanswered','totalrecall','employees','params'));





    }         
        
    


    public function employeesSort($a,$b) {
        $t1 = strtotime($a['date']);
        $t2 = strtotime($b['date']);
        return $t2 - $t1;
    }

   
        
    
    public function statistiques_delivers($request,$response){
        
        $params     = $request->getParams();
        $from = $params['from'] ?? Null;
        $to = $params['to'] ?? Null;

        $params     = $request->getParams();
        $view       = 'stats/dailyDelivers.twig';
        $delivers = (new \App\Helpers\Stats\DailyDelivers($params))->load();

        return $this->view->render($response, $view , compact('delivers','from','to'));
        
    }         
    
        
    public function statistiques_products($request,$response){


        $query = \App\Models\Lists::with('products.product');
      
        if( isset($_GET['from']) && isset($_GET['to'])){
                $date_from = $_GET['from'];
                $date_to   = $_GET['to'];
                $from = \Carbon\Carbon::parse($date_from);
                $to   = \Carbon\Carbon::parse($date_to);
              
        }else {
                $twoweeks = lastweek();
                $date_from = $twoweeks[6];
                $date_to   = $twoweeks[0];
                $from = \Carbon\Carbon::parse($date_from);
                $to   = \Carbon\Carbon::parse($date_to);
        }
   
         
        $lists = $query->whereBetween('created_at',[$from,$to])->orderBy('created_at')->get()->toArray();

        unset($query,$from,$to,$twoweeks);

        $result = [];

        foreach (  $lists as $list ) {

            $products = $list['products'];

            foreach ($products as $product) {
                
                $quantity = $product['quanity'];
                $name     = $product['product']['name'];

                if(!isset($result[$name])){
                    $result[$name] = [
                        'total_orders'      => 0,
                        'total_quantity'      => 0,
                        'waiting_orders'            => 0,
                        'delivred_orders'   => 0,
                        'waiting_quantity'          => 0,
                        'delivred_quantity' => 0,
                    ];
                }

                $result[$name]['total_orders'] += 1;
                $result[$name]['total_quantity'] += $quantity;

                if( !empty($list['delivred_at'])){
                    $result[$name]['delivred_orders'] += 1;
                    $result[$name]['delivred_quantity'] += $quantity;
                }

                if( empty($list['delivred_at'])){
                   $result[$name]['waiting_orders'] += 1; 
                   $result[$name]['waiting_quantity'] += $quantity;
                }

            }

            unset($list,$lists,$products,$product,$name,$quantity);

            $data = [];

            foreach ($result as $key => $item) {

                    $item['name'] = $key;
                
                    $y = $item['total_orders'];
                    $x = $item['delivred_orders'];
                    $percentOrders = $x == 0 ? '0%' : number_format( ($x / $y ) * 100, 2 ) . '%';

      
                    $m = $item['total_quantity'];
                    $n = $item['delivred_quantity'];
                    $percentQuantity = $n == 0 ? '0%' : number_format( ($n / $m  ) * 100, 2 ) . '%';


                    $item['percentOrders'] = $percentOrders;
                    $item['percentQuantity'] = $percentQuantity;

                    array_push($data, $item);
                }   
                
            }

            unset($x,$y,$m,$n,$percentQuantity,$percentOrders,$item,$result);

            $file = '/admin/admin/products-stats.twig';    
            return $this->view->render($response, $file , compact('data','date_from','date_to')); 
    }     

      



            
    public function receptionData($city){ 

            $data = [];

            // get retour
            $this->city_id = $city;
            $retours = \App\Models\Retour::where('cityID',$city)->select('id','productID','cityID','quantity')->get()->toArray();
            $recues  = \App\Models\StockSortieList::where('cityID',$city)->get()->toArray();
            $livred  = \App\Models\Lists::with('products','products.product')->where('cityID',$city)->whereNotNull('delivred_at')->get()->toArray();
            $encours = \App\Models\Lists::with('products','products.product')
    
                            ->whereNull('deleted_at')
                            ->whereNotNull('accepted_at')
                            ->whereNotNull('verified_at')
                            ->whereNull('duplicated_at')

                            ->whereNull('canceled_at')
                            ->whereNull('delivred_at')
                            ->whereNull('recall_at')
                            ->where('statue','!=','NoAnswer')
                            ->where('cityID',$this->city_id)

                            // اظهار الطلبات التي لا تجيب بعد 15 دقيقة 
                            ->orwhere(function($query) {
                                $query
                                ->where('cityID',$this->city_id)
                                
                            ->whereNull('deleted_at')
                            ->whereNotNull('accepted_at')
                            ->whereNotNull('verified_at')
                            ->whereNull('duplicated_at')
                                ->whereNull('deleted_at')
                                ->whereNotNull('accepted_at')
                                ->whereNotNull('verified_at')
                                ->whereNull('duplicated_at')
                                ->whereNull('deleted_at')
                                ->whereNotNull('accepted_at')
                                ->whereNotNull('verified_at')
                                ->whereNull('duplicated_at')
                                ->whereNotNull('no_answer_time')
                                ->whereNull('delivred_at')
                                ->whereNull('canceled_at')
                                ->whereNull('recall_at')
                                ->whereNotNull('no_answer_time')
                                ->where('no_answer_time', '<', \Carbon\Carbon::now()->subMinutes(60)->toDateTimeString());
                            })
                            

                            // اظهار الطلبات بعد مرور وقت إعادة الإتصال
                            ->orwhere(function($query) {
                                $query
                                 ->where('cityID',$this->city_id)
                                 
                            ->whereNull('deleted_at')
                            ->whereNotNull('accepted_at')
                            ->whereNotNull('verified_at')
                            ->whereNull('duplicated_at')
                                ->whereNull('deleted_at')
                                ->whereNotNull('accepted_at')
                                ->whereNotNull('verified_at')
                                ->whereNull('duplicated_at')
                                ->whereNull('deleted_at')
                                ->whereNotNull('accepted_at')
                                ->whereNotNull('verified_at')
                                ->whereNull('duplicated_at')
                                ->whereNull('canceled_at')
                                ->whereNull('delivred_at')
                                ->whereNull('no_answer_time')
                                ->whereNotNull('recall_at')
                                ->where('recall_at', '<', \Carbon\Carbon::now());
                            })->get()->toArray();;

            foreach ($_SESSION['products'] as $product) {


                $item = [
                    'product_id'  =>  $product['id'],
                    'product_name'=>  $product['name'],
                    'product_ref' =>  $product['reference'],    
                ];

                // calculating the retour stock
                $product_retour = 0;
                foreach ($retours as $retour) {
                    if($product['id'] == $retour['productID']){
                        $product_retour += $retour['quantity'];
                    }
                }
                $item['retour'] = $product_retour;


                // calculating recue stock
                $product_recue = 0;
                foreach ($recues as $recue) {
                    if($product['id'] == $recue['productID']){
                        $product_recue += $recue['valid'] ?? 0;
                    }
                }
                $item['recue'] = $product_recue;

                // calculating real stock
                $item['real'] = $item['recue'] - $item['retour'];

                $item['livre']     = 0;
                $item['physique']  = 0;
                $item['theorique'] = 0;
                $item['encours']   = 0;
                
                $data[$product['id']] = $item;
            }

            unset($product_recue,$product_retour,$item,$product);


            foreach($livred as $item){
                foreach($item['products'] as $product){    
                   $data[$product['productID']]['livre'] += $product['quanity'];
                }
            }

            unset($livred,$item,$product);

            foreach($encours as $item){
                foreach($item['products'] as $product){    
                   $data[$product['productID']]['encours'] += $product['quanity'];
                }
            }

            $new = [];
            foreach ($data as $item) {
                $item['physique'] = $item['real'] - $item['livre'];
                $item['theorique'] = $item['physique'] - $item['encours'];
                $new[] = $item;
            }

            unset($livred,$data,$item,$product);

            return $new;

    }    
    
        public function reception($request,$response){ 
        $city = $_GET['city'] ?? 36;
        
        
        $result = [];
        foreach ($_SESSION['cities'] as $citie) {
          if($citie['id'] == $city){

                $result[$citie['city_name']] = $this->receptionData($city);
           }
        }


     //   dd($result);
        $reception = $result;

        
        $file = '/admin/admin/reception.twig';    
        return $this->view->render($response, $file , compact('reception','delivers'));    
    }    
    









    public function pr(){
        $products = [];
        foreach ( $_SESSION['products'] as $product) {
            $products[$product['id']]['name'] = $product['name'];
            $products[$product['id']]['jmla'] = $product['prix_jmla'];
        }
        return $products;
    }

    function reception2($request,$response){ 
        $city = $_GET['city'] ?? NULL;
        $product = $_GET['product'] ?? NULL;
      //  $reception = (new \App\Helpers\Reception($city,$product))->load();
        $reception = $this->receptionData($city);
        $file = '/admin/admin/reception.twig';    
        return $this->view->render($response, $file , compact('reception','delivers'));    
    }    
    
    
    
     public function downloadExcelDeliverJour($request,$response){
         
            $route = $request->getAttribute('route');
            
            $link = $route->getArguments();
            
            $revenue    =  (new Revenue('loadHistory'))->HistoryDetails($link['jour'],$link['deliver']);
            
            $city = \App\Models\User::find($link['deliver'])->username;

            $data = [];
            foreach($revenue as $day => $products) {
                
                foreach ($products['products'] as $product ){
                     $row = [
                        $city,
                        $day,
                        $product['product'],
                        $product['clients'],
                        $product['quantity'],
                        $product['total'],
                        $product['laivraison'],
                        $product['rest'],
                    ];
                    $data[] = $row;
                }
                
            }
            
           // Add header
            $columns = [
                'ville',
                'date',
                'product',
                'clients',
                'quantity',
                'total',
                'laivraison',
                'Rest',
            ];
            $stream = fopen('php://memory', 'w+');
            fwrite($stream, chr(0xEF) . chr(0xBB) . chr(0xBF));

                  
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

            foreach ($data as  $row ) {
                 
                fputcsv($fh, $row, ';');
            }

            fclose( $fh );
            
    } 
    
    
    
    
     
    
 	public function citiesedit($request,$response,$args){
 	    
 	    $city = \App\Models\Cities::find($args['id']); 
            
        if($request->getMethod() == 'POST'){ 
            $city->city_name = $request->getParam('city_name');
            $city->reference = $request->getParam('reference');
            $city->save();
            $this->flashsuccess('تم اضافة المدينة بنجاح');
            return $response->withRedirect($this->router->pathFor('pages.cities'));
        }
		
		$view     = 'admin/admin/edit-city.twig';
        return $this->view->render($response, $view ,compact('city'));   
        
    }   
    
    
    
       
    
    
    
    
    
 	public function cities($request,$response){
        if($request->getMethod() == 'POST'){ 
            \App\Models\Cities::create($request->getParams());
            $this->flashsuccess('تم اضافة المدينة بنجاح');
            return $response->withRedirect($this->router->pathFor('pages.cities'));
        }
		$cities   =  Cities();
		$delivers =  GetDelivers();
		$view     = 'admin/admin/cities.twig';
        return $this->view->render($response, $view ,compact('cities','delivers'));    
    }   
    
    
    
    
    
    
    
    
    

    public function Delivercash($request,$response){
        $params     = $request->getParams();
        $view       = 'admin/deliver/cash.twig';
        $deliver    = Deliver();
        $cash       = (new Cash(@$deliver))->listing();
        return $this->view->render($response, $view , compact('cash'));
    } 
 	    







    public function cash($request,$response){

        $params    = $request->getParams();

        if(($params['duration'] == 'week') or !isset($params['duration'] )){
            $listing   =  Api::LastWeekDelivred()->select('id','DeliverID','delivred_at','prix_de_laivraison')->get()->toArray();    
        }
        
        if(($params['duration'] == 'month') or !isset($params['duration'] )){
            $listing   =  Api::LastMonthDelivred()->select('id','DeliverID','delivred_at','prix_de_laivraison')->get()->toArray();    
        }
        
        if( isset($params['from'])  and  isset($params['to']) ){
            $from = str_replace('/','-',$params['from']);
            $to = str_replace('/','-',$params['to']);


            $listing   =  Api::LastDelivredFromTo($from,$to)->select('id','DeliverID','delivred_at','prix_de_laivraison')->get()->toArray();    
        
        }
    
        $providers =  $this->xs();
        $data  = [];



        foreach ($listing as $list )  {
            /*
            if($list['DeliverID'] ==  '154'){
               if($list['prix_de_laivraison'] != '35'){
             dd($list);
               }
            }
            */

            $day = explode(' ',$list['delivred_at'])[0];
            $provider = $providers[$list['DeliverID']];

            if(!isset($data[$provider]['list'][$day])){

                $item = [];
                $item['deliver_id'] = $list['DeliverID'];
                $item['day']        = $day;
                $item['count']      = 0;
                $item['laivraison'] = 0;
                $item['total']      = 0;
                $item['rest']       = 0;
                $item['paied']      = 0;
                $data[$provider]['list'][$day] = $item;
            }
            
            $data[$provider]['list'][$day]['count'] += 1;
            $data[$provider]['list'][$day]['laivraison'] += $list['prix_de_laivraison'];

            foreach ($list['products'] as $product ) {
              $data[$provider]['list'][$day]['total'] += $product['price'];
            }

            $total      = $data[$provider]['list'][$day]['total'];
            $laivraison = $data[$provider]['list'][$day]['laivraison'];
            $data[$provider]['list'][$day]['rest'] = $total - $laivraison;
            $data[$provider]['list'][$day]['paied'] = isset($this->md()[$list['DeliverID']][$day]) ? 1 : 0;
        }

        $cash      = $data ;

  //      dd($cash);
        $view      = 'admin/admin/cash2.twig';
        return $this->view->render($response, $view , compact('cash'));

    }      




    public function md(){
        $days = [];
        $payments = DB::table('payments')->get()->toArray();

        $data = [];
        foreach ($payments as $payment) {
            $data[$payment->deliver_id][$payment->date] = $payment->money;
        }

        return $data;
    }




    public function xs(){
        $providers = [];
        foreach ( $_SESSION['providers'] as $provider) {
            $providers[$provider['id']] = $provider['username'];
        }
        return $providers;
    }













    public function cash2($request,$response){
        $params     = $request->getParams();
        $view       = 'admin/admin/cash.twig';
        $cash       = (new Cash(@$params))->listing();
        return $this->view->render($response, $view , compact('cash'));
    }         
        

    public function ads($request,$response){
        $data          = $request->getAttribute('route')->getArguments();
        Revenue::SpentsAds($data);
        return $response->withRedirect($this->router->pathFor('pages.revenue'));
    } 



    public function revenue($request,$response){
 

        $params    = $request->getParams();
        $view       = 'admin/admin/revenue3.twig';

        if((isset($params['duration']) and  ($params['duration'] == 'week')) or !isset($params['duration'] )){
            $date      = \Carbon\Carbon::today()->subDays(7);
            $listing   =  Api::with('products')->whereNotNull('delivred_at')->where('delivred_at','>=',$date)
            ->orderBy('delivred_at', 'desc')->select('id','delivred_at','prix_de_laivraison')->get()->toArray();   

        }
        
        if((isset($params['duration']) and  ($params['duration'] == 'month'))){
            $listing   =  Api::LastMonthDelivred()->get()->toArray();    
        }
        
        if( isset($params['from'])  and  isset($params['to']) ){
            $listing   =  Api::LastDelivredFromTo($params['from'],$params['to'])->get()->toArray();    
        }

        $products = $this->pr();

        $data  = [];

        foreach ($listing as $list )  {

            unset($day);

            $day = explode(' ',$list['delivred_at'])[0];

             
            if(!isset($data[$day])){
                    $item                = [];
                    $item['price']       = 0;
                    $item['quantity']    = 0;
                    $item['laivraison']  = 0;
                    $item['jmla']        = 0;
                    $item['rest']        = 0;
                    $data[$day]['total'] = $item;
                    $data[$day]['products'] = []; 
            }

            
            $laivraison = isset($list['prix_de_laivraison']) ? $list['prix_de_laivraison'] :  0;

            $data[$day]['total']['laivraison'] += $laivraison;
            $data[$day]['total']['quantity'] += 1;


             foreach($list['products'] as $product){

                        $id = $product['productID'];
                
                        if(!isset($data[$day]['products'][$id])){

                            $name = $products[$id]['name'];
                            $item = [];
                            $item['quantity'] = 0;
                            $item['name'] = $name;
                            $item['price'] = 0;
                            $item['prix_jmla'] = $products[$id]['jmla'];;
                            $item['prix_jmla_total'] = 0;
                            $item['rest'] = 0;
                            $item['ads'] = 0;
                            $data[$day]['products'][$id] = $item;
                            $data[$day]['products'][$id] = $item;
                        } 

                $data[$day]['products'][$id]['quantity'] += $product['quanity'];
                $data[$day]['products'][$id]['price']    += $product['price'];
                $data[$day]['products'][$id]['prix_jmla']  = $products[$id]['jmla'];
                $data[$day]['products'][$id]['prix_jmla_total']  = ($products[$id]['jmla'] * $data[$day]['products'][$id]['quantity']);

                $data[$day]['total']['price'] += $product['price'];
                $data[$day]['total']['jmla'] += $products[$id]['jmla'];

            }
        }

        $result = [];
        foreach ($data as $key => $item) {
            
            $result[$key] = $item ;

            $new = [];
            foreach ($item['products'] as $product) {

                $product['rest'] = $product['price'] - $product['prix_jmla_total'];
                $result[$key]['total']['rest'] +=  $product['rest'];
                $new[] = $product;
            }

            $result[$key]['products'] = $new;
        }


        unset($data);
        return $this->view->render($response, $view , compact('result'));
    }         
      

    public function revenue3($request,$response){

        $view       = 'admin/admin/revenue2.twig';
        $post       = clean($request->getParams());

        $products = $_SESSION['products'];

        $days = LastWeek();

        $result = [];

        foreach ($days as $day) {
            
                $lists = \App\Models\Lists::with('products.product')->whereDate('delivred_at',$day)->get()->toArray();
                $laivraison = \App\Models\Lists::with('products.product')->whereDate('delivred_at',$day)->get()->sum('prix_de_laivraison');
                $ads =  DB::table('historymoney')->where('date', $day)->first();

                if($ads ){
                    $ads = $ads->ads;
                }else {
                    $ads = 0;                          
                }

                $data = [];

                foreach ($lists as $list) {
                    foreach ($list['products'] as $product) {

                        if(!isset($data[$product['product']['id']])){

                            $jmla = $product['product']['prix_jmla'];
                            $total_jmla = $jmla *  $product['quanity'];

                            $data[$product['product']['id']] = 
                            [
                                'quantity' => $product['quanity'] , 
                                'name' => $product['product']['name'] ,  
                                'price' => $product['price'] ,  
                                'prix_jmla' => $jmla ,  
                                'prix_jmla_total' =>  $total_jmla ,  
                                'rest' =>  $product['price'] - $total_jmla ,  
                                'ads' =>  $ads ,  
                            ];

                        }else {

                           $new_quantity = $data[$product['product']['id']]['quantity'] +  $product['quanity'];
                           $data[$product['product']['id']]['quantity'] = $new_quantity;
                           $data[$product['product']['id']]['price'] = $data[$product['product']['id']]['price'] +  $product['price'];
                           $data[$product['product']['id']]['prix_jmla_total'] = $new_quantity * $data[$product['product']['id']]['prix_jmla'];

                        }

                        foreach($products as $item){
                            if($item['name'] == $data[$product['id']]['name']){
                               $data[$product['id']]['product_id'] = $item['id'];
                               break;
                            }
                        }

                    }
                }


                if(!empty($data)){
                     
                    $price = array_sum(array_column($data,'price'));
                    $jmla  = array_sum(array_column($data,'prix_jmla_total'));
                    $total = [
                        'price'      => $price,
                        'quantity'   => array_sum(array_column($data,'quantity')),
                        'laivraison' => $laivraison,
                        'jmla'       => $jmla ,
                        'rest'       => $price - $jmla - $laivraison ,
                    ];


                    $result[$day]['total'] = $total;
                    $result[$day]['products'] = $data;
                }
               
                
        }

        unset($lists);
        unset($total);
        unset($data);
        unset($day);
        unset($jmla);
        unset($price);


        return $this->view->render($response, $view , compact('result'));
    }         

          
    public function double($request,$response){
        $lists = (new Double())->get();
        $view     = 'admin/admin/double.twig';
        return $this->view->render($response, $view, compact('lists'));
    }     


    public function search($request,$response){
        $post     = clean($request->getParams());
        $route    = $request->getAttribute('route')->getName();
        $search   = new Search($post,$route);
        $view     = $search->view();
        $lists    = $search->search();
        $number   = $search->number();
        return $this->view->render($response, $view, compact('lists','number'));
    }     
      


    public function ExportRevenue($request,$response){
            $dats    = (new Revenue())->get();
            
                // Add header
            $columns = [
                'date',
                'product',
                'clients',
                'quantity',
                'total',
                'laivraison',
                'Rest',
            ];
            $stream = fopen('php://memory', 'w+');
            fwrite($stream, chr(0xEF) . chr(0xBB) . chr(0xBF));

                  
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

            foreach ($dats as $day => $value ) {
                    foreach ($value['products'] as  $product ) {
                         $data = [
                            $day,
                            $product['product'],
                            $product['clients'],
                            $product['quantity'],
                            $product['total'],
                            $product['laivraison'],
                            $product['rest'],
                        ];
                        fputcsv($fh, $data, ';');
                    }
            }

          

            
            fclose( $fh );
    }     
      





}