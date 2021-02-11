<?php 

namespace App\Helpers;

/*
*	usage  $statue = $this->list($list_id)->for('employee')->set('canceled')->save();
*/

defined('BASEPATH') OR exit('No direct script access allowed');


class Statue { 
    

	  protected $employee_statues = ['canceled','sent','recall','unanswred'];
	  protected $provider_statues = ['canceled','delivred','recall','unanswred'];
	  protected $provider;
	  protected $employee;
	  protected $list;
	  protected $query;
	  protected $agents = ['employee','provider'];
	  protected $agent;	
	  protected $action_made;	
	  protected $at;	
	  protected $recall_at;
	  protected $deliver_at;
	  protected $reason;
	  protected $statue;


	  public function __construct(){
	  		$this->at = \Carbon\Carbon::now();
	  }


	  public function list($id){
	  		$this->list =  \App\Models\Lists::find($id);
	  		return $this;
	  }

	  public function for($agent){
	  		$this->agent = $agent;
	  		return $this;
	  }

	  public function set($statue){

	  		$this->statue = $statue;

		  	if($this->agent == 'employee'){
		  		$this->ForEmployee($statue);
		  	}

		  	if($this->agent == 'provider'){
		  		$this->ForProdiver($statue);
		  	}

		  	if($statue == 'reset'){
		  		$this->reset();
		  	}

		  	return $this;
	  }


	public function save(){
		$this->history();
		$this->list->save();
	  	return $this;
	}	

	public function deliver_at($deliver_at){
		$this->deliver_at = $deliver_at;
		return $this;
	}	

	public function recall($recall_at){
		$this->recall_at = $recall_at;
		return $this;
	}	


	public function reason($reason){
		$this->reason = $reason;
		return $this;
	}	

	public function history(){

		$actions = [
			'recall' => '',
			'sent' => '',
			'unanswred' => '',
			'delivred' => '',
			'canceled' => '',
			'recall' => '',
		];

		$this->action_made .= ' - '  . $this->at ;
		$this->action_made .= '  من طرف ';
		$this->action_made .=  auth()->username;

		$message = $this->action_made;

		$load_history = $this->list->history;
        
        if($load_history){
                $history = json_decode($load_history);
                array_push($history,$message);
                $history = json_encode($history, JSON_UNESCAPED_UNICODE);
                $this->list->history  = $history;
        }else {
                $history = [];
                array_push($history,$message);
                $history = json_encode($history, JSON_UNESCAPED_UNICODE);
                $this->list->history  = $history;
        }
        
        return $this;
	}


	public function ForEmployee($statue){

		  	switch($statue){

				    case 'cancel':

				    	$this->action_made  = ' - تم  تعيين ك ملغى ';
				    	$this->list = $this->list->MarkAsCanceled();

				    break; 

				    case 'sent':
				    	$this->action_made  = 'تم  قبول الطلب' ;
				    	$this->list = $this->list->EmployeeMarkAsSent($this->deliver_at);

				    break;

				    case 'recall':
				    	$this->action_made  =  'تم  تعيين ك إعادة الإتصال ';
						$this->list = $this->list->MarkAsRecall();

				    break; 

				    case 'unanswred':
				    
				    	$this->list = $this->list->EmployeeMarkAsUnanswred();

				    	if($this->list->count_no_answer_employee == 7 ) {
				    		$this->action_made  =  'تم  الإلغاء بسبب الرقم لا يجيب ل7  مرات' ;
				    	}
				    	

				    break;				    
			}

			return $this;

	}


	public function ForProdiver($statue){

			switch($statue){
 
				    case 'canceled':

				     	$this->action_made  = ' - تم  تعيين ك ملغى ';
				    	$this->list = $this->list->MarkAsCanceled($this->reason);

				    break; 

				    case 'delivred':

				   	    $this->action_made  = 'تم  قبول الطلب ' ; 
				    	$this->list = $this->list->ProviderMarkAsDelivred();

				    break;

				    case 'recall':

				    	$this->action_made  =  'تم  تعيين ك إعادة الإتصال ';
						$this->list = $this->list->MarkAsRecall($this->recall_at);

				    break; 

				    case 'unanswred':

				    	$this->list = $this->list->ProviderMarkAsUnanswred();

				    	if($this->list->count_no_answer == 4 ) {
				    		$this->action_made  =  'تم  الإلغاء بسبب الرقم لا يجيب ل4  مرات'  ;
				    	}
				    	
				    	if($this->list->count_no_answer_provider == 7 ) {
				    		$this->action_made  =  'تم  الإلغاء بسبب الرقم لا يجيب ل7  مرات' ;
				    	}

				    break;	
			}
			return $this;
	}



	public function reset(){
		$this->list = $this->list->reset();
		$this->action_made  = 'تم اعادة تعيين الطلب الى الحالة الإفتراضية';
		return $this;
	}
				    

	public function loadList($list){
		$this->list = $list;
		return $this;
	}
				    


  
}