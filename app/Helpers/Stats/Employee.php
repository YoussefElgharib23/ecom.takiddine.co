<?php 

namespace App\Helpers\Stats;
use \Psr\Http\Message\ServerRequestInterface as Request;
use App\Models\MultiSale;

defined('BASEPATH') OR exit('No direct script access allowed');

class Employee { 
    
    public $params;
    public $listing;
    public $deliver;
    public $query;
    public $delivred;
    public $canceled;
    public $noanswer;
    public $recall;
    public $employee;
    public $waiting;
    public $all;
    
    
    public function __construct() {
        $this->init();
    }
    
    public function init(){
        $this->listing = \App\Models\Lists::where('mowadafaID',$this->employee)->whereNull('duplicated_at')->whereNull('verified_at')->whereNull('accepted_at')->whereNull('deleted_at');
        return $this;
    }
    
    
    public function all() {
       $this->init();
       $this->all = \App\Models\Lists::where('mowadafaID',$this->employee)->count();
    }
    
    
    public function waiting() {
         $this->init();
       $this->waiting = $this->listing->whereNull('no_answer_time')->whereNull('recall_at')->whereNull('canceled_at')->count();
    }
    
    public function canceled() {
         $this->init();
       $this->canceled = $this->listing->whereNotNull('canceled_at')->count();
    }

    public function noanswer() {
       $this->init();
       $this->noanswer = $this->listing->where('statue','NoAnswer')->count();
    }

    public function recall() {
        $this->init();
        $this->recall =  $this->listing->whereNull('canceled_at')->whereNotNull('recall_at')->count();
    }
 
    public function delivred() {
       $this->init();
       $this->delivred = \App\Models\Lists::where('mowadafaID',$this->employee)->whereNotNull('delivred_at')->count();
    }
    

    public function get(){
        return [
            'all'        =>  $this->all,  
            'waiting'    =>  $this->waiting,
            'canceled'   =>  $this->canceled,
            'noanswer'   =>  $this->noanswer,
            'recall'     =>  $this->recall,
            'delivred'   =>  $this->delivred,
        ];
    }

    public function manipulate(){
        $this->waiting();
        $this->canceled();
        $this->noanswer();
        $this->recall();
        $this->all();
        $this->delivred();
    }
    
    
    public function percent($employeeData){
        $y = $employeeData['all'];
        $data = [
            'waitingPercent'   =>  $employeeData['waiting'] == 0 ? '0%' : number_format( ($employeeData['waiting'] / $y ) * 100, 2 ) . '%',
            'canceledPercent'  =>  $employeeData['canceled'] == 0 ? '0%' : number_format( ($employeeData['canceled'] / $y ) * 100, 2 ) . '%',
            'noanswerPercent'  =>  $employeeData['noanswer'] == 0 ? '0%' : number_format( ($employeeData['noanswer'] / $y ) * 100, 2 ) . '%',
            'recallPercent'    =>  $employeeData['recall'] == 0 ? '0%' : number_format( ($employeeData['recall'] / $y ) * 100, 2 ) . '%',
            'delivredPercent'  =>  $employeeData['delivred'] == 0 ? '0%' : number_format( ($employeeData['delivred'] / $y ) * 100, 2 ) . '%',
        ];
        return $data;
    }
    
    
    public function list(){
        
        $data = [];
        foreach(GetEmployees() as  $employee ){
            $this->employee = $employee->id;
            $this->init();
            $this->manipulate();
            $get = $this->get();
            $data[$employee->username] = array_merge($get,$this->percent($get));
        }
        
        return $data;
    }
    


}




