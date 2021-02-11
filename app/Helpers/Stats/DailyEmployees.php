<?php 

namespace App\Helpers\Stats;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \App\Models\MultiSale;
use \App\Models\Lists;
use \Carbon\Carbon;

defined('BASEPATH') OR exit('No direct script access allowed');

class DailyEmployees { 
    
    public $params;
    public $listing;
    public $deliver;
    public $query;
    public $sent;
    public $delivred;
    public $canceled;
    public $noanswer;
    public $recall;
    public $employee;
    public $waiting;
    public $all;
    public $days;
    public $employees;
    public $day;
    public $between;
    public $from;
    public $to;
    
    public function isBetween($params){
        if(isset($params) and isset($params['from']) and isset($params['to']) and !is_null($params['from']) and !is_null($params['to']) and !empty($params['from']) and !empty($params['to']) ){
              $this->between = true;

                $this->from    = Carbon::parse($params['from']);
                $this->to      = Carbon::parse($params['to']);

                $this->from_display    = $params['from'];
                $this->to_display      = $params['to'];



              return true;
        }
        $this->between = false;
        return false;
    }

    public function __construct($params = false) {
        
        $this->isBetween($params);

        if( ! $this->between){
            $this->days    =  getDays();
        }
        $this->employees   =  GetEmployees();



        return $this->load();
    }
    
    

    public function load() {
        $data = [];
        
        if( ! $this->between){

            foreach( $this->days as $day ){
                $this->day = $day;
                foreach( $this->employees as $employee ){
                   $this->employee = $employee->id;
                   $data[$day][$employee->username] = $this->list();
                }
            }
        }else {
                foreach( $this->employees as $employee ){
                   $this->employee = $employee->id;
                   $data[$this->from_display .'      -     '. $this->to_display][$employee->username] = $this->list();
                }

        }
        
        return $data;
    }
       

    public function list(){
        $this->manipulate();
        $get = $this->get();
        return array_merge($get,$this->percent($get));;
    }
    
    
    public function init(){

        $query = Lists::where('mowadafaID',$this->employee)->whereNull('duplicated_at')->whereNull('verified_at')->whereNull('accepted_at')->whereNull('deleted_at');

        if($this->between){
            $this->listing = $query->whereBetween('created_at', [$this->from, $this->to]);
        }else{
            $this->listing = $query->whereDate('created_at', '=', $this->day );
        }
        
    }
    
    
    public function all() {

        $this->init();

        $query = Lists::where('mowadafaID',$this->employee);

        if($this->between){
            $this->all = $query->whereBetween('created_at', [$this->from, $this->to])->count();
        }else {
            $this->all = $query->whereDate('created_at', '=', $this->day )->count();
        }

        return $this;
    }

    public function waiting() {

        $this->init();

        if($this->between){
            $this->waiting = $this->listing->whereNull('no_answer_time')->whereNull('recall_at')->whereNull('canceled_at')->count();
        }else {
            $this->waiting = $this->listing->whereNull('no_answer_time')->whereNull('recall_at')->whereNull('canceled_at')->count();
        }

        return $this;
    }

    public function canceled() {
       $this->init();
       $this->canceled = $this->listing->whereNotNull('canceled_at')->count();
       return $this;
    }


    public function noanswer() {
       $this->init();
       $this->noanswer = $this->listing->where('statue','NoAnswer')->count();
       return $this;
    }


    public function recall() {
        $this->init();
        $this->recall =  $this->listing->whereNull('canceled_at')->whereNull('canceled_at')->whereNotNull('recall_at')->count();
        return $this;
    }
 

    public function delivred() {

        $this->init();

        $query = Lists::where('mowadafaID',$this->employee)->whereNotNull('delivred_at');

        if(empty($this->between)) {
          $this->delivred = $query->whereDate('created_at', '=', $this->day )->count();
        }

        if(!empty($this->between)) {
          $this->delivred = $query->whereBetween('created_at', [$this->from, $this->to])->count();
        }

        return $this;
    }
    
    public function sent() {

       $this->init();
        
        $query = Lists::where('mowadafaID',$this->employee)->whereNotNull('accepted_at');

        if(empty($this->between)) {
            $this->sent = $query->whereDate('created_at', '=', $this->day )->count();
        }

        if(!empty($this->between)) {
            $this->sent = $query->whereBetween('created_at', [$this->from, $this->to])->count();

        }     
          
        return $this; 
    }
    
    
    public function get(){
        return [
            'all'        =>  $this->all,  
            'waiting'    =>  $this->waiting,
            'canceled'   =>  $this->canceled,
            'noanswer'   =>  $this->noanswer,
            'recall'     =>  $this->recall,
            'delivred'   =>  $this->delivred,
            'sent'       =>  $this->sent,
        ];
    }


    public function manipulate(){

        $this->waiting()->canceled()->noanswer()->recall()->all()->delivred()->sent();
        return $this;
    }
    
    
    public function percent($employeeData){
        $y = $employeeData['all'];
        $data = [
            'waitingPercent'   =>  $employeeData['waiting'] == 0 ? '0%' : number_format( ($employeeData['waiting'] / $y ) * 100, 2 ) . '%',
            'canceledPercent'  =>  $employeeData['canceled'] == 0 ? '0%' : number_format( ($employeeData['canceled'] / $y ) * 100, 2 ) . '%',
            'noanswerPercent'  =>  $employeeData['noanswer'] == 0 ? '0%' : number_format( ($employeeData['noanswer'] / $y ) * 100, 2 ) . '%',
            'recallPercent'    =>  $employeeData['recall'] == 0 ? '0%' : number_format( ($employeeData['recall'] / $y ) * 100, 2 ) . '%',
            'delivredPercent'  =>  $employeeData['delivred'] == 0 ? '0%' : number_format( ($employeeData['delivred'] / $employeeData['sent'] ) * 100, 2 ) . '%',
            'sentPercent'      =>  $employeeData['sent'] == 0 ? '0%' : number_format( ($employeeData['sent'] / $y ) * 100, 2 ) . '%',
        ];
        return $data;
    }
    


}


