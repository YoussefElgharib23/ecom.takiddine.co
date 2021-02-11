<?php 

namespace App\Helpers;
use \Psr\Http\Message\ServerRequestInterface as Request;
use App\Models\MultiSale;

defined('BASEPATH') OR exit('No direct script access allowed');

class EmployeeHelper { 
    

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


    public function __construct($params = false) {
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
        $this->limit    = $params['limit'] ?? 10;
        $this->number   = $params['v'] ?? NULL;

        // set deliver
        $this->deliver  = Deliver(); 

        return $this;    
    }


    public function init(){
        $this->listing = \App\Models\Lists::with('deliver','employee','products','products.product','realcity')
        ->where('mowadafaID',Employee())
        ->whereNull('duplicated_at')
        ->whereNull('verified_at')
        ->whereNull('accepted_at')
        ->whereNull('deleted_at');
        return $this;
    }


    public function pagination(){
         return $this->pagination;
    }


    public function new() {
    
     //   dd(\Carbon\Carbon::now()->subMinutes(1)->toDateTimeString());
    
    
       $this->listing = $this->listing->whereNull('no_answer_time')
                                      ->whereNull('duplicated_at')
                                      ->whereNull('recall_at')
                                      ->whereNull('canceled_at')

                                      ->orwhere(function($query) {
                                            $query
                                            ->whereNull('duplicated_at')
                                            ->whereNull('verified_at')
                                            ->whereNull('accepted_at')
                                            ->whereNull('deleted_at')
                                            ->whereNull('duplicated_at')
                                            ->whereNull('canceled_at')
                                            ->whereNull('recall_at')
                                            ->whereNotNull('no_answer_time')
                                            ->where('no_answer_time', '<', \Carbon\Carbon::now()->subMinutes(300)->toDateTimeString());
                                       })
                                        
                                        ->orwhere(function($query) {
                                            $query
                                            ->whereNull('verified_at')
                                            ->whereNull('accepted_at')
                                            ->whereNull('deleted_at')
                                            ->whereNull('duplicated_at')
                                            ->whereNull('canceled_at')
                                            ->whereNotNull('recall_at')
                                            ->where('recall_at', '<', \Carbon\Carbon::now());
                                        });
                                        


       $this->viewType = 'waiting';
       $this->orderby  = 'accepted_at';
       $this->manipulate(); 
       return $this;
    }


    public function canceled() {
         $this->listing  = $this->listing->whereNotNull('canceled_at');
         $this->viewType = 'canceled';
         $this->orderby  = 'canceled_at';
         $this->manipulate(); 
         return $this;
    }

    public function NoResponse() {

        $this->listing = $this->listing->where('statue','NoAnswer')->whereNull('canceled_at')->whereNull('recall_at');

        if(!is_null($this->number)){
            $no_answer = 'no_answer_' . $this->number;
            $this->listing = $this->listing->where('no_answer',$no_answer);
        }

        
        $this->viewType = 'NoAnswer';
        $this->orderby = 'no_answer_time';
        $this->manipulate(); 
        return $this;
    }


    public function recall() {
       
        $this->listing = $this->listing->whereNull('canceled_at')->whereNotNull('recall_at')->where('statue','recall')->where('recall_at', '>', \Carbon\Carbon::now());
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
            
        $this->limit    = $_GET['limit'] ?? 100 ;
        $lists          = $this->listing ;
        $count          = $lists->count();       
        $lastpage       = (ceil($count / $this->limit) == 0 ? 1 : ceil($count / $this->limit));   
        $skip           = ($this->page - 1) * $this->limit;


       // dd();
        // set the lists
        //$lists          = $lists->skip($skip)->take($this->limit)->orderBy($this->orderby,'DESC');
       $lists          = $lists->skip($skip)->take($this->limit)->orderBy('id','ASC');
       // $lists          = $lists->orderBy('id','ASC');

        //$this->query    = $this->listing;
        $this->listing  = $lists->get();
        $this->count    = 0;
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




