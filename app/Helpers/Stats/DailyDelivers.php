<?php 

namespace App\Helpers\Stats;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Carbon\Carbon;
use \App\Models\Lists;

defined('BASEPATH') OR exit('No direct script access allowed');

class DailyDelivers { 
    

 
    public $params;
    public $listing;
    public $deliver;
    public $query;
    public $canceled;
    public $noanswer;
    public $recall;
    public $employee;
    public $waiting;
    public $all;
    public $days;
    public $delivers;
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
        
        $this->delivers   =  GetDelivers();

        unset($params);

        return $this->load();

    }
    
    
    

    public function load() {
        
        $data = [];
        

        if( ! $this->between){

            foreach( $this->days as $day ){
                
                $this->day = $day;

                foreach( $this->delivers as $employee ){
                    
                   $this->deliver = $employee->id;
                  
                   $data[$day][$employee->username] = $this->list();
                   
                }
                    
            }

            return $data;
        }


        foreach( $this->delivers as $employee ){
                   $this->deliver = $employee->id;
                   $data[$this->from_display .'   -  '. $this->to_display][$employee->username] = $this->list();
        }

        return $data;
    }
    
    
        
    public function list(){
        $this->manipulate();
        $get = $this->get();
        return array_merge($get,$this->percent($get));
    }
    
   
    public function init(){

        $query = Lists::provider($this->deliver)->NotDeleted()->NotDuplicated()->Accepted()->Verified();
        
        if( ! $this->between){
             $this->listing = $query->createdDate( $this->day );
             return $this;
        }

        if( $this->between ){
             $this->listing = $query->whereBetween( 'created_at', [$this->from, $this->to] );
             return $this;
        }



        $this->listing = $query;

        return $this;
    }


    public function all() {
       $this->init();
       $this->all = $this->listing;

       if( $this->between ){
             $this->all = $this->all->whereBetween( 'created_at', [$this->from, $this->to] );
       }

        $this->all = $this->all->count();

       return $this;
    }
    


    public function waiting() {
       $this->init();
       $this->waiting =  $this->listing->NotCanceled()->NotDelivred()->NotRecall()->NotNoAnswer();

       if( $this->between ){
             $this->waiting = $this->waiting->whereBetween( 'created_at', [$this->from, $this->to] );
       }

       $this->waiting = $this->waiting->count();

       return $this;
    }
    


    public function canceled() {
       $this->init();
       $this->canceled = $this->listing->Canceled()->NotDelivred()->NotRecall()->NotNoAnswer();

        if( $this->between ){
             $this->canceled = $this->canceled->whereBetween( 'created_at', [$this->from, $this->to] );
        }

        $this->canceled = $this->canceled->count();

       return $this;
    }


    public function noanswer() {
       $this->init();
       $this->noanswer = $this->listing->NotCanceled()->NotRecall()->NotDelivred()->NoAnswer();
       if( $this->between ){
        
             $this->noanswer = $this->noanswer->whereBetween( 'created_at', [$this->from, $this->to] );
       }

       $this->noanswer = $this->noanswer->count();

       return $this;
    }

    public function recall() {
        $this->init();
        $this->recall =  $this->listing->Recall()->NotCanceled()->NotDelivred()->NotNoAnswer();

        if( $this->between ){
             $this->recall = $this->recall->whereBetween( 'created_at', [$this->from, $this->to] );
        }

       $this->recall = $this->recall->count();

        return $this;
    }
 
    public function delivred() {
       $this->init();
        if( $this->between){

            $this->delivred =  $this->listing->Delivred()->whereBetween( 'created_at', [$this->from, $this->to] )->count();
        }else {
            $this->delivred =  $this->listing->Delivred()->count();

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
        ];
    }

    public function manipulate(){
        $this->waiting()->canceled()->noanswer()->recall()->all()->delivred();
        return $this;
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
    

}
