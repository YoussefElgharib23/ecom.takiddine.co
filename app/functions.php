<?php

use \App\Helpers\Listing;



function getDays(){
    
    $days = \App\Models\Lists::select('created_at')->get()->toArray();
    $days = array_column($days, 'created_at');
    $list = [];
    foreach($days as $day ){
        $list[] = explode(' ',$day)[0];
    }
    return array_reverse(array_unique(groupdays($list)));
}
function date_sort($a, $b) {
    return strtotime($a) - strtotime($b);
}
function groupdays($arr){
    usort($arr, "date_sort");
    return $arr;
}





function Suivi(){
    return  $_SESSION['auth-suivi'] ?? false;
}



function Admin(){
    return  $_SESSION['auth-admin'] ?? false;
}



function Cities(){
  return \App\Models\Cities::all();
}

function GetDelivers(){
    return \App\Models\User::where('role','deliver')->get();
}

function Emplooyee(){
   return  $_SESSION['auth-employee'] ?? false; 
}

function GetEmployees(){
     return \App\Models\User::where('role','employee')->get();
}

function GetCities($id){
    // get the cities
    $cities = \App\Models\Cities::where('user_id',$id)->get();
    return ($cities->count() > 0 )  ? $cities : false ;
}
function GetAllLists(){
   return \App\Models\Lists::all(); 
}

function getDeliverCities($id = false){
     if(!$id){ $id = Deliver(); }
     return \App\Models\Cities::where('user_id',$id)->get();
}

function groupDeliverListsByCitie($id,$CityID){
  return  \App\Models\Lists::where('DeliverID',$id)->where('cityID',$CityID)->where('delivred_at','!=',\Carbon\Carbon::today())->get();
}



function GetEmployeeStats($id,$from=null,$to=null){
     return [
      'TotalCanceled'  => (new Listing(['employee' => $id  ,  'type' => 'canceled']))->count,
      'TotalWaiting'   => (new Listing(['employee' => $id  ,  'type' => 'waiting']))->count,
      'TotalDelivered' => (new Listing(['employee' => $id  ,  'type' => 'delivered']))->count,
      'TotalRecall'    => (new Listing(['employee' => $id  ,  'type' => 'recall']))->count,
    ];
}


function GetDeliverStats($id,$from=null,$to=null){
    return [
      'TotalDeliverd'  => (new Listing(['deliver' => $id  ,  'type' => 'driver_delivred']))->count ,
      'TotalCanceled'  => (new Listing(['deliver' => $id  ,  'type' => 'driver_canceled']))->count ,
      'TotalWaiting'   => (new Listing(['deliver' => $id  ,  'type' => 'driver_waiting']))->count,
      'TotalRecall'    => (new Listing(['deliver' => $id  ,  'type' => 'driver_recall']))->count,
      'TotalNoAnswer'  => (new Listing(['deliver' => $id  ,  'type' => 'driver_NoAnswer']))->count,
    ];
}

function GetAllDeliversStats($from=null,$to=null){
    $DeliversStats = [];
    // Get all the delivers
    $delivers =  GetDelivers();

    if(!is_null($from) and !is_null($to)){
        foreach($delivers as $deliver){
           $DeliversStats[] = array_merge(['name' => $deliver->username ],GetDeliverStats($deliver->id,$from,$to));
        }

    }else {

        foreach($delivers as $deliver){
           $DeliversStats[] = array_merge(['name' => $deliver->username ],GetDeliverStats($deliver->id));
        }
    }
    return $DeliversStats;
}


function GetAllEmployeesStats($from=null,$to=null){

    $EmployesStats = [];
    // Get all the Employes
    $Employes =  GetEmployees();

    
    if(!is_null($from) and !is_null($to)){
        $from = \Carbon\Carbon::parse($from);
        $to = \Carbon\Carbon::parse($to);
        foreach($Employes as $Employe){
               $EmployesStats[] = array_merge(['name' => $Employe->username ],GetEmployeeStats($Employe->id));
        }
    }else {
        foreach($Employes as $Employe){
               $EmployesStats[] = array_merge(['name' => $Employe->username ],GetEmployeeStats($Employe->id,$from,$to));
        }
    }

    return $EmployesStats; 
}


function RestoreNewOrdersFromDuplicates($ids){
    $orders =  \App\Models\NewOrders::whereIn('id', $ids)->get();
          if($orders->count() > 0  ) {
                foreach($orders as $order){
                    $order->duplicated_at = NULL;
                    $order->save();
                }
                return true;
    }
}


function AssignNewOrdersToDeleted($ids){
    $orders =  \App\Models\NewOrders::whereIn('id', $ids)->get();
    if($orders->count() > 0  ) {
          foreach($orders as $order){
              $order->duplicated_at = NULL;
              $order->deleted_at = \Carbon\Carbon::Now();
              $order->save();
          }
          return true;
    }
}

function ConfirmOrders($ids){
    $orders =  \App\Models\Lists::whereIn('id', $ids)->get();
    if($orders->count() > 0  ) {
          foreach($orders as $order){
            if(!empty($order->cityID) and !empty($order->name) and !empty($order->adress)) {
              $order->verified_at = \Carbon\Carbon::Now();
              $order->save();
            }
          }
          return true;
    }
}






function AssignNewOrdersToProduct($ids,$productID){
    
    $orders =  \App\Models\NewOrders::whereIn('id', $ids)->get();
          if($orders->count() > 0  ) {
                foreach($orders as $order){
                    $order->productID = $productID;
                    $order->save();
                }
                return true;
    }
}



function RestoreNewOrdersFromDelete($ids){
       $orders =  \App\Models\NewOrders::whereIn('id', $ids)->get();

      if($orders->count() > 0  ) {
            foreach($orders as $order){
                $order->deleted_at = Null;
                $order->save();
            }
            return true;
      }
    return false;
}




function AssignNewOrdersToAcity($ids,$CityID){
    if($CityID){
     $orders =  \App\Models\NewOrders::whereIn('id', $ids)->get();
          if($orders->count() > 0  ) {
                    foreach($orders as $order){
                        $order->cityID = $CityID;
                        $order->save();
                    }
                    return true;
          }
    }
    return false;
}


function AssignNewOrdersToEmployee($girl,$ids){
    
  
    // get the orders
    $orders =  \App\Models\NewOrders::whereIn('id', $ids)->get();
    
    if($orders->count() > 0  ) {
                    foreach($orders as $order){
                      
                     
                         
                            $list =  new \App\Models\Lists();
                            $list->price      = $order->price;
                            $list->product    = $order->ProductReference;
                            $list->quantity   = $order->quantity;
                            $list->city       = $order->city;
                            $list->name       = $order->name;
                            $list->tel        = $order->tel;
                            $list->adress     = $order->adress;
                            $list->source     = $order->source;
                            $list->mowadafaID = $girl;
                            $list->source     = $order->source;
                            
                            if(is_numeric($order->cityID)){
                                $DeliversID = \App\Models\Cities::find($order->cityID)->user_id;
                                $list->DeliverID = $DeliversID;
                                $list->prix_de_laivraison = \App\Models\User::find($DeliversID)->deliver_price;
                            }
                            
                            if($order->cityID == 'Horzone') {
                                 $list->horszoned_at =  \Carbon\Carbon::Now();
                            }
                            elseif($order->cityID == 'NotFound') {
                               
                            }
                            else {
                               $list->cityID = $order->cityID;
                            }
                                
                            
                            $list->save();
                            
                   
                        
                           if(is_numeric($order->productID)){
                                $pro = new \App\Models\MultiSale();
                                $pro->listID = $list->id;
                                $pro->productID = $order->productID;
                                $pro->price = $order->price;
                                $pro->quanity = $order->quantity ?? 0;
                                $pro->save();
                           }
                                
                           
                              $order->delete();
                    }
                    return true;
             }
    
    return false;
}






















function todaysEarnedMoney(){
    $lists = \App\Models\Lists::whereNotNull('delivred_at')->orderBy('delivred_at', 'ASC')->get();
    
    $cash = [];
    
    foreach ($lists as $list ) {
        
                $total = 0;
                $products = \App\Models\MultiSale::where('listID',$list->id)->get();
                foreach($products as $product) :
                $total += $product->price;
                endforeach;
                    
                $cash[$date = date('Y-m-d', strtotime($list->delivred_at))][] = $total;
 
    }
    
    
    $he = [];
    foreach ($cash as $item => $value ) {
        $he[$item]['sum'] = array_sum($cash[$item]);
        $he[$item]['date'] = $item;
        $he[$item]['paied'] =  \App\Models\Verified::whereDate('date', \Carbon\Carbon::parse($item)->toDateString())->count();
    }
    return $he;
    
}




function DeliverDailyView(){
    $lists = \App\Models\Lists::where('DeliverID',Deliver())->whereNotNull('delivred_at')->orderBy('delivred_at', 'ASC')->get();
    
    $cash = [];
    
    foreach ($lists as $list ) {
                $total = 0;
                $products = \App\Models\MultiSale::where('listID',$list->id)->get();
                foreach($products as $product) :
                $total += $product->price;
                endforeach;
                $cash[$date = date('Y-m-d', strtotime($list->delivred_at))][] = $total;
 
    }
    
    
    $he = [];
    foreach ($cash as $item => $value ) {
        $he[$item]['sum'] = array_sum($cash[$item]);
        $he[$item]['date'] = $item;
    }
    return $he;
    
}














function getReceptionStockForDeliver($id = false ){

        if(!$id){
            $id = Deliver();
        }
        $user_cities = \App\Models\User::where('id',$id)->first()->cities->toArray();
        $reception = [];
        foreach($user_cities as $city ){
            $resutl = \App\Models\DailyStock::where('stockcity',$city['id'])->get();
            if(($resutl->count() > 0 )) {
                foreach($resutl as $daily ){
                    $reception[] = [
                        'reference' => $daily->product->reference,
                        'product_name' => $daily->product->name,
                        'product_name' => $daily->product->name,
                        'quantity' => $daily->quantity,
                        'Ville' => $city['city_name'],
                        'date' => $daily->created_at->toDateString(),
                    ];
                }     
            }
        }
        return $reception;
}
function getDeliveredByDeliver($id = false ){
     if(!$id){
            $id = Deliver();
     }
     return \App\Models\Lists::where('delivred_at','!=',null)->where('DeliverID',$id)->get();
}
function getDeliveredByDeliverAndDate($date){
     if(isset($_SESSION['auth-admin'])){
       return \App\Models\Lists::where('delivred_at','!=',null)->whereDate('delivred_at', '=', $date)->get();  
     }else  {
     $id = Deliver();
       return \App\Models\Lists::where('delivred_at','!=',null)->where('DeliverID',$id)->whereDate('delivred_at', '=', $date)->get();  
     }
}














function getTotalDeliveryCashForAdeliverToday($id){
   return \App\Models\Lists::where('DeliverID',$id)
       ->where('delivred_at', '>=', \Carbon\Carbon::today())->get()->sum('prix_de_laivraison');  
}
function GetLivredTodayForDeliver($DeliversID){
    return \App\Models\Lists::where('delivred_at','=',\Carbon\Carbon::today())
        ->where('DeliverID',$DeliversID)->get();
}
function GetTodayTotalCash(){
    return \App\Models\Lists::where('delivred_at', '>=', \Carbon\Carbon::today())->get()->sum('prix_de_laivraison');
}
function GetTotalDeliveryCashForAllDelivers(){
    $stat = [];
    foreach(GetDelivers() as $deliver ){
       $stat[] =  ['username' => $deliver->username , 'Montant' => getTotalDeliveryCashForAdeliverToday($deliver->id) ];
    }
    return $stat;
}
function getTotalRevenueForListings($listing){
    $money = [];
    foreach($listing as $list){
      $money[] = $list->productsList->sum('price');
    }
   return array_sum($money);
}
function GetTotalRevenueForDeliverToday($id){
    
    $money = [];
    foreach(GetDelivers() as $deliver ){
     $money[] = getTotalRevenueForListings(GetLivredTodayForDeliver($deliver->id));
    }
    return array_sum($money);
}











function calculateTodaysRevenueByDeliver($id){
    
    // cash Table
    $cash = [];
    
    // get deliver cities
    $cities = getDeliverCities($id);
        
    // foreach city from cities get CMDS where cityID is CityID
    foreach($cities as $city):
    $listsOFcity = groupDeliverListsByCitie($id,$city->id);
    
    // set the city array
    $cash[$city->city_name] = [];
    
            foreach($listsOFcity as $list):
        
            // getProductOfList
            $products = getProductOfList($list->id);
                
            $laivraison = $list->prix_de_laivraison;
                
                $i = 0;
                foreach($products as $product) :
                
                    $l = $i == 0  ? $laivraison : 0 ;
                    
    
                        $cash[$city->city_name][$product->product->id][] = [
                           'laivraison' => $l,
                           'reference' => $product->product->reference,
                           'product' => $product->product->name ,
                           'price' => $product->price,
                           'quanity' => $product->quanity,
                           'clients' => 1 ,
                           'cityID' => $city->id ,
                        ];

                $i++;
                endforeach;


            endforeach;
    
    endforeach;

    $lmajmo3 = [];
    
    foreach($cash as $cities => $city  ){
        foreach($city as $products){   
            
            $lmajmo3[$cities]
                [] = [
              'laivraison' =>array_sum(array_column($products,'laivraison')),
              'price' =>array_sum(array_column($products,'price')),
              'quanity' =>array_sum(array_column($products,'quanity')),
              'clients' => array_sum(array_column($products,'clients')),
              'reference' => $products[0]['reference'],
              'product' => $products[0]['product'],
              'cityID' => $products[0]['cityID'],
              'date' => Carbon\Carbon::today()->toDateString() ,

            ];
        }
    }
    
    return $lmajmo3;
    
}

function ListCashToday(){
    $list = [];
    foreach(GetDelivers() as $deliver){
    $l = calculateTodaysRevenueByDeliver($deliver->id);
        if(!empty($l)) {
            $list[] =  $l; 
        }
    }
    return $list;
}
    
 
function validerJour($date,$livredmoney,$CityID){
    $data = [
        'Date'          => $date,
        'cityID'        => $CityID,  
        'cashLivred'    => $livredmoney,   
    ];
    \App\Models\ValidatedCash::create($data);
}





function CheckDuplicateNumber($tel){
        $lists = \App\Models\Lists::Where('tel', 'LIKE', '%' . $tel . '%')->get();
        return ($lists->count() > 0) ?  true : false;
}



