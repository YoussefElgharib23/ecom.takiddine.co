<?php 

namespace App\Helpers;
use \Psr\Http\Message\ServerRequestInterface as Request;
use App\Models\MultiSale;

defined('BASEPATH') OR exit('No direct script access allowed');

class DeliverHelper { 
    

    public $params;
    public $listing;
    public $pagination;
    public $type;
    public $order;
    public $deliver;
    public $limit;
    public $page;
    public $lists;
    public $query;
    public $viewType;
    public $orderby;


    public function __construct($params) {
        $this->setup($params);
        $this->init();
        return $this;
    }

    public function setup($params){

        $this->params = $params;

        // set parameters
        $this->order    = $params['order'] ?? 'DESC';
        $this->type     = $params['type'] ?? 'waiting';
        $this->page     = $params['page'] ?? 1;
        $this->limit    = 200;

        // set deliver
        $this->deliver  = Deliver(); 

        return $this;    
    }


    public function init(){
      //   dd(Deliver());
        $this->listing = \App\Models\Lists::with('deliver','employee','products','products.product','realcity')
       
        ->where('DeliverID',Deliver())
        ->whereNull('deleted_at')
        ->whereNotNull('accepted_at')
        ->whereNotNull('verified_at')
        ->whereNull('duplicated_at');
        return $this;
        
        
    }


    public function pagination(){
         return $this->pagination;
    }


    public function new() {
       $this->listing = $this->listing->whereNull('canceled_at')
                            ->whereNull('delivred_at')
                            ->whereNull('recall_at')
                         //   ->where('statue','!=','NoAnswer')

                            // اظهار الطلبات التي لا تجيب بعد 15 دقيقة 
                            ->orwhere(function($query) {
                                $query
                                ->where('DeliverID',Deliver())
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
                                 ->where('DeliverID',Deliver())
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
                            });
    

       $this->viewType = 'waiting';
       $this->orderby  = 'accepted_at';
       $this->manipulate(); 
       return $this;
    }




    public function canceled() {
         $this->listing  = $this->listing->whereNotNull('canceled_at')->where('statue','!=','NoAnswer')->whereNull('delivred_at')->whereNull('recall_at');
         $this->viewType = 'canceled';
         $this->orderby  = 'canceled_at';
         $this->manipulate(); 
         return $this;
    }


    public function delivered() {
        $this->listing = $this->listing->whereNull('canceled_at')->whereNotNull('delivred_at')->where('statue','!=','NoAnswer')->whereNull('recall_at');
        $this->viewType = 'delivred';
        $this->orderby = 'delivred_at';
        $this->manipulate(); 
        return $this;
    }

    public function NoResponse() {
        $this->listing = $this->listing->whereNull('canceled_at')->where('statue','NoAnswer');
        $this->viewType = 'NoAnswer';
        $this->orderby = 'no_answer_time';
        $this->manipulate(); 
        return $this;
    }


    public function NoResponseDetails() {
        $this->listing = $this->listing->whereNull('canceled_at')->where('statue','NoAnswer')->whereNull('delivred_at');
        $this->viewType = 'NoAnswer';
        $this->orderby = 'no_answer_time';
        $this->manipulate(); 
        return $this;
    }



    public function recall() {
        $this->listing = $this->listing->whereNull('canceled_at')
                                       ->whereNull('delivred_at')
                                       ->where('statue','!=','NoAnswer')
                                       ->whereNotNull('recall_at');
        $this->viewType = 'recall';
        $this->orderby = 'recall_at';
        $this->manipulate(); 
        return $this;
    }

    public function toArray(){
      return  $this->listing->toArray();
    }

    public function paginate(){
        return $this->pagination;
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

    public function load(){
      return  $this->json;
    }

    public function viewType(){
        return $this->viewType;
    }


    public function manipulate() {
        
        $lists          = $this->listing ;
        $count          = $lists->count();       
        $lastpage       = (ceil($count / $this->limit) == 0 ? 1 : ceil($count / $this->limit));   
        $skip           = ($this->page - 1) * $this->limit;

        // set the lists
        $lists          = $lists->skip($skip)->take($this->limit)->orderBy($this->orderby,'DESC');

        //$this->query    = $this->listing;
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
        $pagination = new \App\Helpers\Paginator($count, $this->limit, $this->page, $urlPattern);
        $this->pagination    = $pagination;
        return $this;
    }


}




