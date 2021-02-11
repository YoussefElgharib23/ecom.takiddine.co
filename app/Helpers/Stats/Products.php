<?php 

namespace App\Helpers\Stats;
use \Psr\Http\Message\ServerRequestInterface as Request;
use App\Models\MultiSale;

defined('BASEPATH') OR exit('No direct script access allowed');

class Products { 
    

 
    public $params;
    public $listing;
    public $deliver;
    public $query;
    public $accepted;
    public $waiting;
    public $all;
    public $days;
    public $delivers;
    public $day;
    public $product;
    public $from;
    public $to;
    public $between;

    
    public function __construct($params = false) {


        if(isset($params) and isset($params['from']) and isset($params['to']) and !is_null($params['from']) and !is_null($params['to']) and !empty($params['from']) and !empty($params['to']) ){
            
            $this->from  =  \Carbon\Carbon::parse($params['from']);
            $this->to  = \Carbon\Carbon::parse($params['to']);
            $this->from_display    = $params['from'];
            $this->to_display      = $params['to'];


             $this->between  = 'true';
        }else {
         $this->false  = 'true';   
         $this->days        = lastWeek();
        }
        
        
        $this->products   =  \App\Models\Product::all()->toArray();
        return $this->load();
    }
    


    function createDateRangeArray($strDateFrom,$strDateTo) {
        
        $period = new \DatePeriod(
             new \DateTime($strDateFrom),
             new \DateInterval('P1D'),
             new \DateTime($strDateTo)
        );
        
        $days = []; 
        foreach ($period as $key => $value) {
            $days[] = $value->format('Y-m-d');      
        }
        $days[] = $this->to;
        return $days;
    }



    public function load() {
        
        $data = [];
        if(!empty($this->from)) {
            
                foreach( $this->products as $product ){
                    
                   $this->product = $product['id'] ;
                   $data[$this->from_display .'      -     '. $this->to_display][$product['name']] = $this->list();
                   

                } 
            
            
            }else {
                
              foreach( $this->days as $day ){
            
                $this->day = $day;
    
                foreach( $this->products as $product ){
                    
                   $this->product = $product['id'] ;
                   $data[$day][$product['name']] = $this->list();
                   
                }
                    
            }
        }
      
        
        return $data;
    }
    
    public function list(){
        $this->manipulate();
        $get = $this->get();
        return array_merge($get,$this->percent($get));
    }
    
    

    
    public function init(){
        
        if(!empty($this->from)) {
            
            $this->listing = \App\Models\Lists::with('products','products.product')->whereBetween('created_at', [$this->from, $this->to])->whereNull('deleted_at')->whereNull('canceled_at')->whereNull('duplicated_at')->whereHas('products.product', function ($query) {
                    return $query->where('id', '=', $this->product);
            });
        }
        
        if(empty($this->from)) {
        
             $this->listing = \App\Models\Lists::with('products','products.product')->whereDate('created_at', '=', $this->day )->whereNull('deleted_at')->whereNull('canceled_at')->whereNull('duplicated_at')->whereHas('products.product', function ($query) {
                        return $query->where('id', '=', $this->product);
            });
        
        }
        
        
       
        return $this;
    }
    
    public function all() {
       $this->init();
       $this->all = $this->listing->count();
    }
    
    public function accepted() {
        $this->init();
        $this->accepted =  $this->listing->whereNotNull('accepted_at')->count();
    }
 
    public function delivred() {
       $this->init();
       $this->delivred =  $this->listing->whereNotNull('delivred_at')->count();
    }
    
    public function get(){
        return [
            'all'        =>  $this->all,  
            'accepted'     =>  $this->accepted,
            'delivred'   =>  $this->delivred,
        ];
    }

    public function manipulate(){
        $this->all();
        $this->accepted();
        $this->delivred();
    }
    
    
    public function percent($employeeData){
        $y = $employeeData['all'];
        $data = [
            'acceptedPercent'   =>  $employeeData['accepted'] == 0 ? '0%' : number_format( ($employeeData['accepted'] / $y ) * 100, 2 ) . '%',
            'delivredPercent'  =>  $employeeData['delivred'] == 0 ? '0%' : number_format( ($employeeData['delivred'] / $y ) * 100, 2 ) . '%',
        ];
        return $data;
    }
    


}