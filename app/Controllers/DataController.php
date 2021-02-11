<?php

namespace App\Controllers;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use Illuminate\Database\Capsule\Manager as DB;

// Models
use \App\Models\{ Options , Lists , Product , DailyStock , Cities , StockGeneral , NewOrders  };
use \App\Models\{ User , Stock , MultiSale, Sources , StockEntree  , StockSortie  , StockSortieList , HistoryEntree };


// Classes And Libraries
use \App\Classes\{files , SystemLog};
use \App\Classes\Noanswser as ff;


defined('BASEPATH') OR exit('No direct script access allowed');

class DataController extends Controller {
    
   
    public function index($request,$response) {

   
        $orders  = NewOrders::query();
        $type    = $request->getParam('view')    ?? 'all';
        $city    = $request->getParam('city')    ?? NULL;
        $product = $request->getParam('product') ?? NULL;
        $order   = $request->getParam('order')   ?? 'DESC';

        if(in_array($type, ['deleted','duplicated','all','stores'])){

            if($type == 'deleted'){
                $orders = $orders->whereNotNull('deleted_at');
            }

            if($type == 'duplicated'){
                $orders = $orders->whereNotNull('duplicated_at')->whereNull('deleted_at');
            }

            if($type == 'stores'){
                $orders = $orders->where('source','store');
            }

            if($type == 'all'){
                $orders = $orders->whereNull('deleted_at')->whereNull('duplicated_at');
            }
        }

        if( isset($city) and !is_null($city) and is_numeric($city)){
            $orders = $orders->where('cityID',$city);
        }

        if(!is_null($product) and is_numeric($product)){
            $orders = $orders->where('productID',$product);
        }

        
        
        
        
        
        // sv('dkjdkdkkd');
        
        
        
        
        
        

        // Pagination settings
        $count    = $orders->count();         
        $page     = ($request->getParam('page', 0) > 0) ? $request->getParam('page') : 1;
        $limit    = ($request->getParam('limit', 0) > 0) ? $request->getParam('limit') : 200;; 
        $lastpage = (ceil($count / $limit) == 0 ? 1 : ceil($count / $limit));   
        $skip     = ($page - 1) * $limit;
        $orders   = $orders->skip($skip)->take($limit);
        //$lists    = $orders->orderBy('tel',$order)->get();
          
  
                
           
        if($type == 'duplicated'){
            $lists    = $orders->orderBy('id',$order)->get();
        }else {
            $lists    = $orders->orderBy('id',$order)->get();
        }
        
              
                
                
                
        // get the url and clean it for pagination
        $url        = $request->getParams();
        unset($url['page']);
        $URLparams  = http_build_query($url);
        $urlPattern =  !empty($URLparams) ? '?'.$URLparams. "&page=(:num)" : "?page=(:num)";
        
        // get the pagination 
        $pagination = new \App\Helpers\Paginator($count, $limit, $page, $urlPattern);

        // get the employees and cities
        $employees = GetEmployees();    
        $cities    = Cities::All();   
        $twig      = 'admin/admin/data.twig'; 
        $view      = $request->getParam('view');
        
        return $this->view->render($response, $twig,  compact('view','lists','pagination','count','employees','cities') );  
    }

    public function edit($request,$response,$args) {
        $id     = rtrim($args['id'], '/');
        $list   = Lists::find($id);
        $double = Lists::where('tel', 'LIKE', '%' . $list->tel . '%')->where('id','!=',$list->id)->get();
        $double2 = SentLists::where('tel', 'LIKE', '%' . $list->tel . '%')->where('list_id','!=',$list->id)->get();
        return $this->view->render($response, 'admin/double/edit.twig', compact('double','double2'));  
    }    
          
    public function activate($request,$response,$args) {
        $id  = rtrim($args['id'], '/');
        $list  = Lists::find($id);
        $list->is_double = '';
        $list->save();
        $this->flashsuccess('تم التعديل بنجاح');
        return $response->withRedirect($this->router->pathFor('double'));   
    }    
    

    public function assignToCity($request,$response,$args){
        $ids = explode(',',$request->getParam('AssignToCityIDS')) ?? [];
        $cityID = $request->getParam('cityID');
        AssignNewOrdersToAcity($ids,$cityID);
        $this->flashsuccess('شكرا لك ، تم تعيين المدينة الى الطلبات بنجاح ');
        return $response->withRedirect($this->router->pathFor('data'));   
    }


    public function remove($request,$response,$args){
        $ids = explode(',',$request->getParam('AssignToRemove')) ?? [];
          $orders =  \App\Models\NewOrders::whereIn('id', $ids)->get();
          if($orders->count() > 0  ) {
                    foreach($orders as $order){
                        $order->delete();
                    }
          }
        $this->flashsuccess('شكرا لك ، تم حذف الطلبات بشكل كلي بنجاح ');
        return $response->withRedirect($this->router->pathFor('data').'?view=deleted');   
    }







    
    /**
     * جلب اسم الموظفة في البحث 
     * @author TakiDDine
     */
    public function getUernameByID($id){
        $user = \App\Models\User::find($id);       
        if($user){
          return  $user->username;
        } 
    }
    
    
  
    
    
    /**
     * الاحصائيات
     * @author TakiDDine
     */
    public function statiques(){
        return [
            'count_deleted' => NewOrders::whereNotNull('deleted_at')->count(),
            'count_sheet' =>  NewOrders::where('source','sheet')->whereNull('deleted_at')->whereNull('duplicated_at')->count(),
            'count_store' => NewOrders::where('source','!=','sheet')->whereNull('duplicated_at')->whereNull('deleted_at')->count(), 
            'count_duplicated' => NewOrders::whereNotNull('duplicated_at')->whereNull('deleted_at')->count() ,
        ];
    }
    
    
    
        
     /**
     * استرجاع الطلبات المحذوفة 
     * @author TakiDDine
     */
    public function restoreOrders($request,$response){
         $ids = explode(',',$request->getParam('AssignToRestore')) ?? [];
         RestoreNewOrdersFromDelete($ids);
         $this->flashsuccess('شكرا لك ، تم استرجاع الطلبات من المحذوفة');
         return $response->withRedirect($this->router->pathFor('data'));      
    }
    
    
     /**
     * حذف الطلبات
     * @author TakiDDine
     */
    public function delete($request,$response,$args){
         $ids = explode(',',$request->getParam('AssignToDelete')) ?? [];
         AssignNewOrdersToDeleted($ids);
         $this->flashsuccess('شكرا لك ، تم تعيين الطلبات كمحذوفة');
         return $response->withRedirect($this->router->pathFor('data'));        
    }
    

     /**
     * استرجاع من المكررة
     * @author TakiDDine
     */
    public function RestoreFromDuplicates($request,$response,$args){
         $ids = explode(',',$request->getParam('RestoreFromDuplicates')) ?? [];
         RestoreNewOrdersFromDuplicates($ids);
         $this->flashsuccess('تم ازالة الطلبات من المكررة');
         return $response->withRedirect($this->router->pathFor('data'));      
    }
    

        
    /**
     * تعيين الطلبات الى المنتوج
     * @author TakiDDine
     */
    public function assignToProduct($request,$response,$args){
            $ids = explode(',',$request->getParam('AssignToProductIDS')) ?? [];
            $productID = $request->getParam('productID');
            AssignNewOrdersToProduct($ids,$productID);
            $this->flashsuccess('شكرا لك ، تم تعيين الطلبات الى المنتوج بنجاح ');
            return $response->withRedirect($this->router->pathFor('data'));      
    }
    
    
    
    
    /*     
    *   
    */     
    public function set_the_orders_to_employee_with_count($employee,$count){
       $i = 0 ;
       while($i < $count):
           if(isset($_SESSION['idsToSend'][$i])){
                $_SESSION['sendToMe'][$employee][] = $_SESSION['idsToSend'][$i];   
                $i++;
           } else {
               break;
           }
       endwhile;  
       $_SESSION['idsToSend'] = array_splice($_SESSION['idsToSend'], $i);
    }
   

    /**
     * تعيين الطلبات الى الموظفة
     * @author TakiDDine
     */
    public function assignToEmployee($request,$response,$args){
        
        unset($_SESSION['idsToSend']);
        unset($_SESSION['sendToMe']);
        
        // get the ids & girls 
        $ids = explode(',',$request->getParam('AssignToEmployeeIDS')) ?? [];

        
        $list = $request->getParams();
        unset($list['AssignToEmployeeIDS']);
        $_SESSION['idsToSend'] = $ids;
       
        // Remove the empty fields
        foreach($list as $girl => $count ) {
            if(empty($count)) {
                unset($list[$girl]);
            }
        }
        
        // set the ids to girls
        foreach($list as $girl => $count ) {
            $this->set_the_orders_to_employee_with_count($girl,$count);
        }
                
        foreach($_SESSION['sendToMe'] as $girl => $ids ) {
            AssignNewOrdersToEmployee($girl,$ids);
        }
        
        $this->flashsuccess('شكرا لك ، تم تحويل الطلبات بنجاح ');
        return $response->withRedirect($this->router->pathFor('data'));
        
    }
    
    
    public function loadcount($request,$response,$args) {
        return json_encode(AutoGetEmployeesStats());
    }
    
         /**
      * To do : fix the probleme of checking if the file exist
      * رفع ملف من جوجل شيت الى قاعدة البيانات
      * @author TakiDDine
      */
     public function uploadTheSheet2($request,$response,$args){
         

//        dd('kkdkdkd');

         if(!isset($_FILES['SheetFile'])){
              $this->flasherror('المرجوا اضافة الملف');
              return $response->withRedirect($this->router->pathFor('data'));
             exit;
         }else {
             
             $uploader = new \App\Helpers\Uploader("upload");
         
             $file = $_FILES['SheetFile'];
             $uploader->file = $file;
             $uploader->path = $this->dir('csv');
             $name = $uploader->save();
             $ext = strtolower(last(explode('.', $file['name'])));
             $csv = $this->dir('csv').$name;
             
             if(file_exists($csv)){
             
                 if($ext == 'csv') {
                    $orders = \App\Helpers\SimpleCSV::import($csv);
                 }

                 if($ext == 'xlsx') {
                    $xlsx = \App\Helpers\SimpleXLSX::parse($csv);
                    $orders = $xlsx->rows();
                 }
                    $termine = 0;
                    $i = 0;
                    foreach ($orders as $order) {
                        if($i != 0  and array_filter($order)):


					        $productname = '';
					        if($order[5]){
					             $found = \App\Models\Product::where('name',trim($order[5]))->first();
					             if($found){
					                 $productname = $found->id;
					             }
					        }



                             $data = [
                                      'source'=>'sheet',
                                      'name'=>$order[1],
                                      'tel'=>$order[2],
                                      'city'=>$order[3],
                                      'adress'=>$order[4],
                                      'ProductReference'=> $order[5],
                                      'price'=>$order[8] ?? '', 
                                      'quantity'=>$order[7], 
                                      'productID' => $productname
                             ];
                             
                             if($this->checkDuplicatedNumber($order[2])) {
                                $data['duplicated_at'] = \Carbon\Carbon::Now();
                             }
                             NewOrders::create($data);
                             $termine++;
                        endif;
                        $i++;
                    }   
                $this->flashsuccess(' لقد تم اضافة الطلبات الجديدة بعدد -  '. $termine);
             }
             
             return $response->withRedirect($this->router->pathFor('data'));
             }
    
    
    }

    public function uploadTheSheet3($request,$response,$args){
         


         if(!isset($_FILES['SheetFile'])){
              $this->flasherror('المرجوا اضافة الملف');
              return $response->withRedirect($this->router->pathFor('data'));
             exit;
         }else {
             
             $uploader = new \App\Helpers\Uploader("upload");
         
             $file = $_FILES['SheetFile'];
             $uploader->file = $file;
             $uploader->path = $this->dir('csv');
             $name = $uploader->save();
             $ext = strtolower(last(explode('.', $file['name'])));
             $csv = $this->dir('csv').$name;
             
             if(file_exists($csv)){
             
                 if($ext == 'csv') {
                    $orders = \App\Helpers\SimpleCSV::import($csv);
                 }

                 if($ext == 'xlsx') {
                    $xlsx = \App\Helpers\SimpleXLSX::parse($csv);
                    $orders = $xlsx->rows();
                 }
                    $termine = 0;
                    $i = 0;
                    
                    $alldata = [];


                 

                    foreach ($orders as $order) {
                        if($i != 0  and array_filter($order)):


					        $productname = '';
					        if($order[5]){

					             $found = \App\Models\Product::where('name',trim($order[5]))->first();
					             if($found){
					                 $productname = $found->id;
					             }
					        }

                            $data = [
                                'source'=>'sheet',
                                'name'=>@$order[1],
                                'tel'=>@$order[2],
                                'city'=>@$order[3],
                                'adress'=>@$order[4],
                                'ProductReference'=> @$order[5],
                                'price'=>@$order[8] ?? '', 
                                'quantity'=>@$order[7], 
                                'productID' => @$productname,
                                'duplicated_at' => null
                            ];

                            array_push($alldata,$data);
                           
                             $termine++;
                        endif;
                        $i++;
                    }

                  //  dd($alldata);

                DB::table('new')->insert($alldata);
                $this->flashsuccess(' لقد تم اضافة الطلبات الجديدة بعدد -  '. $termine);
             }
             
             return $response->withRedirect($this->router->pathFor('data'));
             }
    }

    public function uploadTheSheet($request,$response,$args){
         

         if(!isset($_FILES['SheetFile'])){
              $this->flasherror('المرجوا اضافة الملف');
              return $response->withRedirect($this->router->pathFor('data'));
             exit;
         }else {
             
             $uploader = new \App\Helpers\Uploader("upload");
         
             $file = $_FILES['SheetFile'];
             $uploader->file = $file;
             $uploader->path = $this->dir('csv');
             $name = $uploader->save();
             $ext = strtolower(last(explode('.', $file['name'])));
             $csv = $this->dir('csv').$name;
             
             if(file_exists($csv)){
             
                 if($ext == 'csv') {
                    $orders = \App\Helpers\SimpleCSV::import($csv);
                 }

                 if($ext == 'xlsx') {
                    $xlsx = \App\Helpers\SimpleXLSX::parse($csv);
                    $orders = $xlsx->rows();
                 }
                    $termine = 0;
                    $i = 0;
                    
                    $alldata = [];


                    //get all products
                    $products = [];
                    foreach ($_SESSION['products'] as $product) {
                        $products[$product['name']] = $product;
                    }

                    foreach ($orders as $order) {
                        if($i != 0  and array_filter($order)):
                            if(isset($order[2]) && $order[2] != ''){
                                
                            
					        $productname = '';
					        if($order[5]){
                                if(isset($products[$order[5]])){
                                    $productname = $products[$order[5]]['id'];
                                }
					        }

                            $data = [
                                'source'=>'sheet',
                                'name'=>@$order[1],
                                'tel'=>@$order[2],
                                'city'=>@$order[3],
                                'adress'=>@$order[4],
                                'ProductReference'=> @$order[5],
                                'price'=>@$order[8] ?? '', 
                                'quantity'=>@$order[7], 
                                'productID' => @$productname,
                                'duplicated_at' => null
                            ];
                            
                            $alldata[@$order[2]] = $data;
                           
                             $termine++;
                            }
                        endif;
                        $i++;
                    }

                    // get all the numbers of orders                    
                    $numbers  = array_keys($alldata);

                    $duplicated = [];
                    $lists = array_keys(\App\Models\Lists::select('id','tel')->whereIn('tel',$numbers)->distinct('tel')->get()->keyBy('tel')->toArray());
                    $new = array_keys(\App\Models\NewOrders::select('id','tel')->whereIn('tel',$numbers)->distinct('tel')->get()->keyBy('tel')->toArray());
                    $duplicated = array_merge($new,$lists);
                    
                    $now = \Carbon\Carbon::Now()->toDateTimeString();

                    foreach ($duplicated as $number ) {
                        if(isset($alldata[$number])){
                            $alldata[$number]['duplicated_at'] = $now;
                        }                        
                    }

                DB::table('new')->insert($alldata);
                $this->flashsuccess(' لقد تم اضافة الطلبات الجديدة بعدد -  '. $termine);
             }
             
             return $response->withRedirect($this->router->pathFor('data'));
             }
    }

    
    
     /**
     * التحقق من أن الرقم غير مكرر في الطلبات القديمة والجديدة
     * @author TakiDDine
     */
    public function checkDuplicatedNumber($number){
        
        $newstring = substr($number, -8);
        $foundAlreadyInLists     =   \App\Models\Lists::all();
        $foundAlreadyInNeworders =  \App\Models\NewOrders::all();
        
        
        $exist = false;
        foreach($foundAlreadyInLists as $order) {
            if( substr($order->tel, -8) == $newstring ) { $exist = true; }
        }
        foreach($foundAlreadyInNeworders as $order) {
            if( substr($order->tel, -8) == $newstring ) { $exist = true; }
        }
        
        return $exist;
    }
    
    
    public function SearchForNumber($number){
       
       $results = [];
       
        $newstring = substr($number, -8);
        $foundAlreadyInLists     =   \App\Models\Lists::all();
        $foundAlreadyInNeworders =  \App\Models\NewOrders::all();
        
        
        $exist = false;
        foreach($foundAlreadyInLists as $order) {
            if( substr($order->tel, -8) == $newstring ) { 
                $order->cmdtype = 'oldOrder';
                $results[] = $order;
            }
        }
        foreach($foundAlreadyInNeworders as $order) {
            if( substr($order->tel, -8) == $newstring ) { 
                $order->cmdtype = 'NewOrder';
                $results[] = $order;
            }
        }
        
        
        return $results;
        
    }
    
    
    
    
    
    
   

    
    

}
