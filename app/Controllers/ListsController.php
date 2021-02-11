<?php

namespace App\Controllers;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use \App\Models\{Lists , Product , Cities , User , MultiSale } ;
use Carbon\Carbon;
use \App\Helpers\{Noanswer , Listing,Revenue , Statue};



defined('BASEPATH') OR exit('No direct script access allowed');


class ListsController extends Controller {



    public function setDelivred ($request,$response) {
        $id = $_POST['list'];
        $this->statue->list($id)->for('provider')->set('delivred')->save();
    }

    public function setCanceled($request,$response){
        $id      = $_POST['list_id'];
        $reason  = $_POST['reason'];
        $this->statue->list($id)->reason($reason)->for('provider')->set('canceled')->save();
    }
    
    public function reset ($request,$response) {
        $id = $_POST['list'];
        $this->statue->list($id)->set('reset')->save();
    }          

    public  function loadHistory(){
        $id = $_POST['id'];
        $list = Lists::find($id);
        return $list->history;
    }     

    public function setRecall($request,$response){
        
        $id   = $_POST['list_id'];
        
        $h = $_POST['recall_houres'];
        $d = $_POST['recall_days'];

        if($h) {
          $recall_at = $this->now->addMinutes($h);
        }
        if($d) {
          $recall_at = Carbon::parse($d);
        }

        $this->statue->list($id)->recall($recall_at)->for('provider')->set('recall')->save();
    }

    public function setNoResponse ($request,$response) {

        $id = $_POST['get_id'];

        if(Deliver()){
            $this->statue->list($id)->for('provider')->set('unanswred')->save();
        }
           
        if(Employee()){
            $this->statue->list($id)->for('employee')->set('unanswred')->save();
        }
           
        return $this->unanswer->start($id);
    }

    public function loadListData($request,$response) {
        $id       = $_POST['list_id'];
        $list     = Lists::with('products','products.product')->find($id)->toArray();
        $view     = 'admin/elements/box.twig';
        return $this->view->render($response, $view , compact('list'));
    }

    public function loadListDataWithActions($request,$response) {
        $id       = $_POST['list_id'];
        $type     = $_POST['type'];
        $list     = Lists::with('products','products.product')->find($id)->toArray();
        $view     = 'admin/elements/boxAction.twig';
        return $this->view->render($response, $view , compact('list','id','type','validnumber'));
    }

    public function setSentEmployee ($request,$response) {

        $id                  = $_POST['list'];
        $list                = Lists::with('products')->withCount('products')->find($id);
        $houres              = $_POST['houres'];
        $days                = $_POST['days'];

        if(!$list->products_count){
                return 'please_fill_info';
        }

        if(!isset($_SESSION['auth-admin'])){ 
            if(empty($list->cityID) || empty($list->name) || empty($list->adress) ){
                return 'NEDDED_INFO';
            }
        }
      
        if(!empty($houres)) {
           $deliver_at = Carbon::Now()->addHours($houres+1); 
        }
        
        if(!empty($days)) {
          $deliver_at = Carbon::parse($days);
        } 
       
        $this->statue->loadList($list)->deliver_at($deliver_at)->for('employee')->set('sent')->save();
    }



    public function edit($request,$response,$args){

        $id   = rtrim($args['id'], '/');
        $list = Lists::with('products')->find($id);

        if(isset($_SESSION['auth-admin'])) {
            $view = 'admin/admin/edit-lists.twig';
        }else {
            $view = 'admin/employee/edit.twig';
        }
        return $this->view->render($response, $view ,compact('list'));
    }

       
    public function stock($request,$response){
        $view =  'admin/embalage/entree.twig';
        return $this->view->render($response,$view);    
    }
     


    public function confirmation($request,$response) {
            $params         = $request->getParams();
                   //   $params['limit']  = 600;

            $route          = $request->getAttribute('route');
            $listing        = new Listing($params,$route);
            $pagination     = $listing->pagination();
            $lists          = $listing->list();
            $view           = $listing->view();
            $type           = $listing->viewType();
            $cities         = (new \App\Helpers\Stats())->ConfirmationCities();
            $cities         = array_chunk($cities, 3);

/*
 $view = 'admin/admin/confirmation.twig';
 $lists =  \App\Models\Lists::with('deliver','employee','products','products.product','realcity')->whereNull('deleted_at')
                                ->whereNotNull('accepted_at')
                                ->whereNull('duplicated_at')
                                ->whereNull('canceled_at')
                                ->whereNull('recall_at')
                                ->whereNull('delivred_at')
                                ->whereNull('verified_at')
                                ->get()->toArray();
        
*/


        return $this->view->render($response,$view,compact('cities','type','lists','params','pagination'));
    }

    public function confirm($request,$response,$args){
        $ids = explode(',',$request->getParam('confirmation_ids')) ?? [];
        ConfirmOrders($ids);
        $this->flashsuccess('تم تفعيل الطلبات وارسالها الى الموزعين');
        return $response->withRedirect($this->router->pathFor('confirmation').'?type=confirmation');   
    }



    public function employeeListing( $request,$response ) {


        $route          = $request->getAttribute('route');

        $params         = $request->getParams();

        if(isset($_SESSION['auth-deliver'])){
          $params['limit']  = 2000;
        }

        $listing        = new Listing($params,$route);

        $pagination     = $listing->pagination();

        $lists          = $listing->list();
        
        $view           = $listing->view();

        $type           = $listing->viewType();

        return $this->view->render($response,$view,compact('type','lists','params','pagination'));

    }


    public function loadUserDeliveryPrice($request,$response){
        return isset($_POST['city_id']) ? (Cities::with('deliver')->find($_POST['city_id'])->deliver->deliver_price ?? '') : '' ;
    }





    public function index($request,$response) {
        
        $route          = $request->getAttribute('route');
        $params         = $request->getParams();
     //   if(isset($_SESSION['auth-deliver'])){
        $params['limit']  = 40;
      //  }


        $listing        = new Listing($params,$route);
        $pagination     = $listing->pagination();
        $lists          = $listing->list();
        
        $view           = $listing->view();
        $type           = $listing->viewType();
        return $this->view->render($response,$view,compact('type','lists','params','pagination'));
    }

 
    public function suivi($request,$response) {
        $route          = $request->getAttribute('route');
        $params         = $request->getParams();
        $listing        = new Listing($params,$route);
        $pagination     = $listing->pagination();
        $lists          = $listing->list();
        $view           = $listing->view();
        $type           = $listing->viewType();
        return $this->view->render($response,$view,compact('type','lists','params','pagination'));
    }


    public function VerfiedCash($request,$response) {
        $data          = $request->getAttribute('route')->getArguments();
        (new \App\Helpers\Cash)->verfiey($data);
        return $response->withRedirect('/cash');
    }



    public function cash($request,$response) {

        // get all
        $lists = \App\Models\Lists::with('products.product')->whereDate('delivred_at',$_POST['date'])->where('DeliverID',$_POST['deliver'])->get()->toArray();
//199+498+
        $data = [];

        foreach ($lists as $list) {
            foreach ($list['products'] as $product) {

                if(!isset($data[$product['id']])){

                    $data[$product['id']] = 
                    [
                        'quantity' => $product['quanity'] , 
                        'name' => $product['product']['name'] ,  
                        'price' => $product['price'] ,  
                    ];
                }else {
                   $data[$product['id']] = $data[$product['id']]['quantity'] +  $product['quanity'];
                   $data[$product['id']] = $data[$product['id']]['price'] +  $product['price'];
                }
            }
        }

        $total = [
            'price' => array_sum(array_column($data,'price')),
            'quantity' => array_sum(array_column($data,'quantity')),
        ];

        $view       = 'admin/elements/revenueForm.twig';
        return $this->view->render($response, $view , compact('data','total'));
    }



    public function stats($request,$response) {
        $file = 'admin/admin/stats.twig';
        return $this->view->render($response,$file);
    }
    




    

    public function all($request,$response){
        $params         = $request->getParams();
        $listing        = new \App\Helpers\Listing($params);
        $lists          = $listing->listing();
        $pagination     = $listing->pagination();
        return $this->view->render($response,$view,compact('lists','pagination'));
    }



    // show the create page
    public function createForm($request,$response){
        return $this->container->view->render(
            $response,'admin/admin/create-lists.twig',
            compact('products','cities','employees')
        );
    }    
    
    // store the list
    public function create($request,$response) {  
        $post =  clean($request->getParams());
        $Lists = new Lists();
        $listID = $this->saveList($Lists,$post,true);
        $this->saveMultiSale($post,$listID);
        $this->flashsuccess('تم اضافة الطلب بنجاح');
        $redirectURL  = $post['redirectURL'];
        return $response->withRedirect($redirectURL);
    } 


    
        
    // Update the list
    public function update($request,$response,$args){
        $post =  clean($request->getParams());
        $link = $post['redirectURL'];
        unset($post['redirectURL']);
        $id  = rtrim($args['id'], '/');
        $list = Lists::find($id);
        $listID = $this->saveList($list,$post);
        $this->saveMultiSale($post,$listID,true);
        $this->flashsuccess('تم تعديل الطلب بنجاح');

        if(empty($link)){
            return $response->withRedirect('/lists');
        }

        return $response->withRedirect($link);
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
    
    
    // creating the list OR update 
    public function saveList($model,$post,$checkNumber = false){
        
        $model->name               = $post['name'];
        $model->adress             = $post['adress'];
        $model->tel                = $post['tel'];
        $model->cityID             = $post['cityID'];
        $model->DeliverID          = Cities::find($post['cityID'])->user_id;
        $model->note               = $post['note'] ;
        $model->prix_de_laivraison = $post['prix_de_laivraison'] ;

        $model->mowadafaID         = $post['employee'] ?? $model->mowadafaID  ;
       

        if(! Employee() ){
            if($checkNumber  == true ){
               if( $this->checkDuplicatedNumber($post['tel'])){
                   $model->duplicated_at = Carbon::NOW();
               } 
            }
        }

        $model->save();
        return $model->id;
    }   

    // the action of saving the products of the listing 
    public function multiSaleProductsSave($post,$listID){
       for($x=0;$x< count($post['ProductID']);$x++){
            $pro = new MultiSale();
            $pro->listID    = $listID;
            $pro->productID = $post['ProductID'][$x];
            $pro->price     = $post['prix'][$x];
            $pro->quanity   = $post['quantity'][$x];
            $pro->save();
        } 
    }

    // save OR update the products of the order
    public function saveMultiSale($post,$listID,$update = false){
        if($update){
                MultiSale::where('listID', $listID)->delete(); 
                $this->multiSaleProductsSave($post,$listID);
        }
        else{
            $this->multiSaleProductsSave($post,$listID);
        }
    }
  


    public function loadEmployeesCount($request,$response){
        $data = [];
        foreach (GetEmployees() as $employee) {
            $inj = (new Listing([ 'employee'=> $employee->id , 'type'=> 'waiting' ]))->countTotal;
            $new = (new Listing([ 'employee'=> $employee->id , 'type'=> 'NoAnswer' ]))->countTotal;
            $rcall = (new Listing([ 'employee'=> $employee->id , 'type'=> 'recall' ]))->countTotal;
            array_push($data, [$employee->id ,'inj:' . $new  . ' - ' . 'enoure:'.$inj   . ' - ' . 'recall:'.$rcall          ]);
        }
        return json_encode($data);
    }
    
   
    

    public function transform($request,$response){     
        if($request->getMethod() == 'POST' )  {
            $post = clean($request->getParams());
            if(($post['from'] != $post['to']) and is_numeric($post['from']) and is_numeric($post['to'])){
                    $lists        = (new Listing(['employee' => $post['from'] , 'type' => 'waiting' ]))->listing;
                    foreach($lists as $list){
                        $list->mowadafaID = $post['to'];
                        $list->save();
                    }
                    $this->flashsuccess('تم تحويل الطلبات بنجاح');
                    return $response->withRedirect($this->router->pathFor('lists.transform'));
            }
        }
        $file = 'admin/admin/transform.twig';
        return $this->view->render($response,$file);
    }


    
}

