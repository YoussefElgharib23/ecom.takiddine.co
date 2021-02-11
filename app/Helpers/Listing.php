<?php 

namespace App\Helpers;
use \Psr\Http\Message\ServerRequestInterface as Request;
use App\Models\MultiSale;

defined('BASEPATH') OR exit('No direct script access allowed');

class Listing { 
    
    public $params;
    public $listing;
    public $pagination;
    public $type;
    public $order;
    public $city;
    public $employee;
    public $deliver;
    public $limit;
    public $page;
    public $lists;
    public $route;
    public $json;
    public $viewType;
    public $orderby;
    public $query;
        public $product;


    public static function delivredByDay($day,$deliver = false){
        $lists   = \App\Models\Lists::with('deliver','employee','products','products.product','realcity');
        $lists   = $lists->whereDate('delivred_at', $day);
        if(isset($deliver) and !empty($deliver) and is_numeric($deliver)){
            $lists   = $lists->where('DeliverID', $deliver);
        }
        return  $lists->get();
    }
    

    public static function search($search){
        $lists   = \App\Models\Lists::with('deliver','employee','products','products.product','realcity');
        $lists   = $lists->where('tel', 'LIKE', '%' . $search . '%');
        return  $lists->get()->toArray();
    }
    

    public function viewType(){
        return $this->viewType;
    }
    


    public function init(){
        $this->listing = \App\Models\Lists::with('deliver','employee','products','products.product','realcity');
        return $this;
    }

    public function __construct($get = false,$route = false){
        $this->params = $get;

        if($route){
          $this->route = $route->getName();
        }
        $this->manipulate($get);
        return $this;
    }

    public function setParams($params){
        $this->url      = $params;
        $this->order    = $params['order'] ?? NULL;
        $this->type     = $params['type'] ?? NULL;
        $this->employee = $params['employee'];
        $this->city     = $params['city'] ?? NULL;
        $this->page     = $params['page'] ?? NULL;
        $this->limit    = $params['limit'] ?? NULL;
        $this->deliver  = $params['deliver'] ?? NULL;
        $this->find     = $params['find'] ?? NULL;
        $this->product     = $params['product'] ?? NULL;        
        return $this;
    }

    public function pagination(){
         return $this->pagination;
    }




    public function getLimit($get){
        if(!isset($get['limit'])){
            return 10;
        }else {
            return $get['limit']; 
        }
    }



    public function manipulate($get){

    
        $params['type']     = $this->getType($get);
        $params['order']    = $get['order'] ?? 'DESC' ;
        $params['city']     = $get['city'] ?? NULL ;
        $params['employee'] = $this->getEmployee($get);
        $params['deliver']  = $this->getDeliver($get);
        $params['limit']    = $this->getLimit($get) ;
        $params['page']     = $get['page'] ?? 1 ;
        $params['find']     = $get['find'] ?? NULL ;
        $params['product']  = $get['product'] ?? NULL ;

        // set params
        $this->setParams($params);


        // get listing
        $lists   =  $this->init()
                    ->type($this->type)
                    ->city($this->city)
                    ->employee($this->employee)
                    ->deliver($this->deliver)
                    ->order($this->order)
                    ->find($this->find);

        if(isset($this->product)){
              $lists  =  $this->listing->whereHas('products', function($q){
                $q->where('productID', $this->product);
            });
        }


               $params['limit'] = 100;
     

        $count            = $lists->count();       
        $this->countTotal = $count; 
        $lastpage         = (ceil($count / $params['limit']) == 0 ? 1 : ceil($count / $params['limit']));   
        $skip             = ($params['page'] - 1) * $params['limit'];


        // set the lists
        $lists          = $lists->skip($skip)->take($params['limit']);

        $this->query    = $this->listing;
        $this->listing  = $lists->get();
        $this->count    = $this->listing->count();
        $this->json     = $this->listing->toArray();
       
        $url        = $this->params;
        if(empty($url) and !is_array($url)){
            $url = [];
        }
        unset($url['page']);
        $URLparams  = http_build_query($url);
        $urlPattern = !empty($URLparams) ? '?'.$URLparams. "&page=(:num)" : "?page=(:num)";

        $pagination = new \App\Helpers\Paginator($count, $params['limit'], $params['page'], $urlPattern);


        $this->pagination    = $pagination;
        return $this;
    }

    public function toArray(){
      return  $this->listing->toArray();
    }


    public function getType($params){

            if(!isset($params['type'])){
                $type = NULL;
            }

            if($this->route == 'lists' and isset($_SESSION['auth-employee']) and is_numeric($_SESSION['auth-employee'])){
                $type = 'waiting';
            }

            if($this->route == 'Sentlists' and isset($_SESSION['auth-deliver']) and is_numeric($_SESSION['auth-deliver'])){
                $type = 'driver_waiting';
            }

            if(isset($params['type'])){
                $type = $params['type'];
            }
            return $type;
    }



    
    public function view(){
        $employee     = 'admin/employee/index.twig';
        $deliver      = 'admin/deliver/lists.twig';
        $admin        = 'admin/admin/lists.twig';
        $all          = 'admin/admin/all-lists.twig';
        $adminDeliver = 'admin/admin/sentlists.twig';
        $confirmation = 'admin/admin/confirmation.twig';
        $suivi        = 'admin/suivi/index.twig';


        if(is_numeric(Deliver())){
            return $deliver;
        }
        if(is_numeric(Admin())){

            if($this->route == 'lists.all' ){
                return $all;
            }
            if($this->route == 'Sentlists' or $this->route == 'suivi'){
                return $adminDeliver;
            }
            if($this->route == 'confirmation'){
                return $confirmation;
            }

            return $admin;
        }
        
        if(is_numeric(Employee())){
            return $employee;
        }

        if(is_numeric(Suivi())){
            return $suivi;
        }
    } 


    

    public function paginate(){
        return $this->pagination;
    }

  


    public function getEmployee($get){

        if(isset($_SESSION['auth-employee']) and is_numeric($_SESSION['auth-employee'])) {

            return $_SESSION['auth-employee'];
        }

        if(isset($get['employee'] ) and is_numeric($get['employee'] )){
            return $get['employee'] ;
        }

        return NULL;
    }

    public function getDeliver($get){
        if(isset($_SESSION['auth-deliver']) and is_numeric($_SESSION['auth-deliver'])) {
            return $_SESSION['auth-deliver'];
        }

        if(isset($get['deliver'] ) and is_numeric($get['deliver'] )){
            return $get['deliver'] ;
        }

        if(isset($get['deliver'])){ 
            return 'all';
        }
        return NULL;
    }



    public function type($type){

       
        $lists = $this->listing;


        if(isset($type) and !empty($type) and !is_null($type)){

            if($type == 'suivi') {
                 $lists = $lists->whereNull('deleted_at')
                                ->whereNotNull('accepted_at')
                                ->whereNotNull('verified_at')
                                ->whereNull('duplicated_at')
                                ->whereNotNull('canceled_at')
                                ->whereNull('delivred_at')
                                
                                ->orWhereNotNull('no_answer_time');
                                
                                
                                
                $this->viewType = 'suivi';
                $this->orderby = 'accepted_at';
                return $this;
            }
            if($type == 'confirmation') {

                 $lists = $lists->whereNull('deleted_at')
                                ->whereNotNull('accepted_at')
                                ->whereNull('duplicated_at')
                                ->whereNull('canceled_at')
                                ->whereNull('recall_at')
                                ->whereNull('delivred_at')
                                ->whereNull('verified_at');
                               // ->where('statue','!=','NoAnswer')
                $this->viewType = 'confirmation';
                $this->orderby = 'to_deliver_at';
                return $this;
            }
            
            else if($type == 'all') {
                if($this->route == 'Sentlists'){
                    $this->listing->whereNotNull('accepted_at');
                    $this->orderby = 'accepted_at';
                }
                if($this->route == 'lists'){
                    $this->listing->whereNull('accepted_at');
                    $this->orderby = 'created_at';
                }
                return $this; 
            }

            else if(substr( $type, 0, 6 ) === "driver") {

                    // get the deliver default

                    $lists = $lists->whereNull('deleted_at')->whereNotNull('accepted_at')->whereNotNull('verified_at')->whereNull('duplicated_at');

                    // No answer
                    if($type == 'driver_NoAnswer'){
                       $lists = $lists->whereNull('canceled_at')->where('statue','NoAnswer')->whereNull('delivred_at');
                       $this->viewType = 'NoAnswer';
                       $this->orderby = 'no_answer_time';
                    }

                    // waiting for Deliver
                    elseif($type == 'driver_delivred'){
                       $lists = $lists->whereNull('canceled_at')->whereNotNull('delivred_at')->where('statue','!=','NoAnswer')->whereNull('recall_at');
                        $this->viewType = 'delivred';
                        $this->orderby = 'delivred_at';
                    }

                    // waiting for Deliver
                    elseif($type == 'driver_canceled'){
                       $lists = $lists->whereNotNull('canceled_at')->where('statue','!=','NoAnswer')->whereNull('delivred_at')->whereNull('recall_at');
                       $this->viewType = 'canceled';
                       $this->orderby = 'canceled_at';
                    }

                     // waiting for Deliver
                    if($type == 'driver_recall'){
                       $lists = $lists->whereNull('canceled_at')
                                      ->whereNull('delivred_at')
                                      ->where('statue','!=','NoAnswer')
                                      ->whereNotNull('recall_at');
                        $this->viewType = 'recall';
                        $this->orderby = 'recall_at';
                    }

                    // waiting for Deliver
                    if($type == 'driver_waiting'){
                        
                        
                     //   dd($_SESSION['auth-deliver']);
                         //dd('fmfmf');:
                       if(isset($_SESSION['auth-deliver'])){
                           
                           
                           //  dd('d:d:d');
                             
                             
                            $lists = $lists->whereNull('canceled_at')->whereNull('delivred_at');
                       
                            /*
                         $lists = $lists->whereNull('canceled_at')->whereNull('delivred_at')
                            ->orwhere(function($query) {
                            $query->whereNotNull('no_answer_time');
                            }) ;
                            */

                       }else {
                         $lists = $lists->whereNull('canceled_at')->whereNull('recall_at')->whereNull('delivred_at')->where('statue','!=','NoAnswer');
                       }
                        $this->viewType = 'waiting';
                        $this->orderby = 'accepted_at';
                    }
                }

            else {
                    $employee = $lists->whereNull('duplicated_at')->whereNull('verified_at')->whereNull('accepted_at')->whereNull('deleted_at');


                    // waiting for employee
                    if($type == 'waiting'){
                        

                        if(isset($_SESSION['auth-employee'])){

                                  $lists = $employee->whereNull('no_answer_time')->whereNull('duplicated_at')->whereNull('recall_at')->whereNull('canceled_at')
                                        
                            ->orwhere(function($query) {
                            $query
                            ->whereNotNull('no_answer_time')
                            ->where('no_answer_time', '<', \Carbon\Carbon::now()->subHours(3)->toDateTimeString());
                            }) ;


                        }else {
                            $lists = $employee->whereNull('no_answer_time')->whereNull('duplicated_at')->whereNull('recall_at')->whereNull('canceled_at');
                        }
                 
                        $this->viewType = 'waiting';
                        $this->orderby = 'created_at';

                    }
                    
                    // Canceled for the employee
                    if($type == 'canceled'){
                       $lists = $employee->whereNotNull('canceled_at'); 
                        $this->viewType = 'canceled';
                        $this->orderby = 'canceled_at';
                    }
                    
                    // Recall for the employee
                    if($type == 'recall'){	
                        $lists = $employee->whereNull('canceled_at')->whereNotNull('recall_at')->whereNull('no_answer_time');
                        $this->viewType = 'recall';
                        $this->orderby = 'recall_at';
                    }

                    // No answer for the employee
                    if($type == 'NoAnswer'){
                        $lists = $employee->where('statue','NoAnswer')->whereNull('canceled_at');
                        $this->viewType = 'NoAnswer';
                         $this->orderby = 'no_answer_time';
                    }


                    // No answer 1
                    if($type == 'NoAnswer_1'){
                        $lists = $employee->where('no_answer','no_answer_1');
                        $this->viewType = 'NoAnswer';
                        $this->orderby = 'no_answer_time';
                    }
                    // No answer 2
                    if($type == 'NoAnswer_2'){
                        $lists = $employee->where('no_answer','no_answer_2');
                         $this->viewType = 'NoAnswer';
                         $this->orderby = 'no_answer_time';
                    }
                    // No answer 3
                    if($type == 'NoAnswer_3'){
                        $lists = $employee->where('no_answer','no_answer_3');
                         $this->viewType = 'NoAnswer';
                         $this->orderby = 'no_answer_time';
                    }
                    // No answer 4
                    if($type == 'NoAnswer_4'){
                        $lists = $employee->where('no_answer','no_answer_4');
                         $this->viewType = 'NoAnswer';
                         $this->orderby = 'no_answer_time';
                    }

                    // No answer 5
                    if($type == 'NoAnswer_5'){
                        $lists = $employee->where('no_answer','no_answer_5');
                         $this->viewType = 'NoAnswer';
                         $this->orderby = 'no_answer_time';
                    }
                    // No answer 6
                    if($type == 'NoAnswer_6'){
                        $lists = $employee->where('no_answer','no_answer_6');
                         $this->viewType = 'NoAnswer';
                         $this->orderby = 'no_answer_time';
                    }
                    // No answer 7
                    if($type == 'NoAnswer_7'){
                        $lists = $employee->where('no_answer','no_answer_7');
                         $this->viewType = 'NoAnswer';
                         $this->orderby = 'no_answer_time';
                    }

                    
            }           

            

        }
        
       
        $this->listing = $lists;
        return $this; 
     }


    public function by($roles = false,$id = false){
        if(!is_null($roles) and !is_null($id) and isset($roles) and !empty($roles) and in_array($roles, ['deliver','employee']) and is_numeric($id)){
            if($roles == 'deliver'){
                $this->listing =   $this->listing->where('DeliverID',$id);
            }
            if($roles == 'employee'){
                $this->listing =   $this->listing->where('mowadafaID',$id);
            }  
        }
        return $this; 
    }



    public function employee($empolyee){

        if(isset($empolyee) and !empty($empolyee) and is_numeric($empolyee)){
           $lists = $this->listing->where('mowadafaID',$empolyee);
        }
        if(isset($empolyee) and !empty($empolyee) and $empolyee == 'all'){
            $lists = $this->listing->where('mowadafaID','!=','');
        }
        return $this; 

    }


    public function deliver($deliver){
         if(isset($deliver) and !empty($deliver) and is_numeric($deliver)){
           $lists = $this->listing->where('DeliverID',$deliver);
        } 
      
        if(isset($deliver) and !empty($deliver) and $deliver == 'all'){
            $lists = $this->listing->where('DeliverID','!=','');
        }
        return $this; 
    }



    public function city($city = null){
        if(!is_null($city) and isset($city) and !empty($city) and is_numeric($city)){
           $this->listing =   $this->listing->where('cityID',$city);
        }
        return $this; 
    }  
    

    public function store($store){
        if(isset($store) and !empty($store)){
           $this->listing =   $this->listing->where('store',$store);
        }
        return $this;
    }


    public function find($id){
        if( !is_null($id) and isset($id) and !empty($store) and is_numeric($id)){
           $this->listing =   $this->where('id',$id);
        }
        return $this;
    }


    public function phone($phone){
        if(isset($phone) and !empty($phone)){
           $this->listing =   $this->listing->where('tel',$phone);
        }
        return $this; 
    }


    public function NoAnswertype($type){
        if(isset($type) and !empty($type)){
           $this->listing =   $this->listing->where('no_answer',$type);
        }
        return $this; 
    }

    public function order($order){
         if(isset($order) and !empty($order) and in_array($order, ['ASC','DESC'])){
            if(!empty($this->orderby)){
                    $this->listing =   $this->listing->orderby($this->orderby,$order);
            }else{
                $this->listing =   $this->listing->orderby('created_at',$order);
            }

        }
        return $this; 
    }

    
    public function between($from,$to,$field = false){
        if(!empty($from) and !empty($to)) {
            $from =  \Carbon\Carbon::parse($from);
            $to   =  \Carbon\Carbon::parse($to);           
            $this->listing =  $this->listing->whereBetween($field, [$from, $to]);
        }
        return $this;
    }

    public function count(){
        return $this->listing->count();
    }

    public function get(){
        return $this->listing->get();
    }  

    public function toSql(){
        return $this->listing->toSql();
    }  

    public function skip($skip){
    $this->listing = $this->listing->skip($skip);
    return $this;
    }  

    public function take($take){
     $this->listing = $this->listing->take($take);
     return $this;
    }  

    public function list(){
     return $this->json ;
    }  

}




