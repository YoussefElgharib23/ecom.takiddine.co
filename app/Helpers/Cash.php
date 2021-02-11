<?php 

namespace App\Helpers;
use App\Models\MultiSale;
use PHPtricks\Orm\Database;
use Illuminate\Database\Capsule\Manager as Capsule;



defined('BASEPATH') OR exit('No direct script access allowed');

class Cash { 
    
  protected $query;
  protected $deliver;
  protected $from;
  protected $to;


  
  public function __construct($params = false){

      // set the dates from to
      $this->setDates($params);

      // set the deliver
      $this->setDeliver($params);
   
  }
  
  public function countPaid($list){
     return array_count_values(array_column($list, 'paied'))[1] ?? 0; 
  }

  
  public function setDeliver($params){
      
      
      $deliver =  @$params['deliver'];
      if(isset($deliver) and !empty($deliver) and !is_null($deliver) and is_numeric($deliver)) {
        $this->deliver = $deliver;
      }
      
      
      if(Deliver()){
          $this->deliver = Deliver();
      }
      
      
      return $this;
  }

  public function setDates($post) {
          if(empty($post['from']) and empty($post['to']) ){
              return $this;
          }
          $this->from  = \Carbon\Carbon::parse($post['from']);
          $this->to    = \Carbon\Carbon::parse($post['to']);
          return $this;
  }


  public function listing(){
        
      if(is_numeric($this->deliver)) {
        $data = [];
        $list      = $this->deliverCash($this->deliver);
        if(!empty($list)) {
            $paied = $this->countPaid($list);
            $notPaied = count($list) - $paied;
            $username = \App\Models\User::find($this->deliver)->username;
            $data[$username]['id'] = $this->deliver;
            $data[$username]['paied'] = $paied;
            $data[$username]['notPaied'] = $notPaied;
            $data[$username]['list'] = $list;
        }
        return $data;
      }
      else {
        $data = [];
        foreach ( $this->delivers() as $deliver ) {
          $list      = $this->deliverCash($deliver['id']);
          if(!empty($list)) {
             $paied = $this->countPaid($list);
             $notPaied = count($list) - $paied;
             $data[$deliver['username']]['id'] = $deliver['id'];
             $data[$deliver['username']]['paied'] = $paied;
             $data[$deliver['username']]['notPaied'] = $notPaied;
             $data[$deliver['username']]['list'] = $list;
          }
        }
        return $data;
      }
  }

  public function delivers(){
      return \App\Models\User::where('role','deliver')->select('id','username')->get()->toArray();
  }

  function groupByDay($array_in){
      $hash = array();
      $array_out = array();
      foreach($array_in as $item) {
          $hash_key = $item['dayDelivred'];
          if(!array_key_exists($hash_key, $hash)) {
              $hash[$hash_key] = sizeof($array_out);
              array_push($array_out, array(
                  'day' => $item['dayDelivred'],
                  'count' => 0,
              ));
          }
          $array_out[$hash[$hash_key]]['count'] += 1;
    }
    return $array_out;
  }

  

  public function verfiey($data){
      return Capsule::table('payments')->insert($data);
  }


  public function delivredByDeliver($id){
      $lists =  \App\Models\Lists::whereNotNull('delivred_at');

      if(!empty($this->from) and !empty($this->to)){
          $lists =  $lists->whereBetween('delivred_at', [$this->from, $this->to]);
      }
      $lists =  $lists->where('DeliverID',$id)->select('cityID','delivred_at')->get();

      return $lists->toArray();
  }
  
  public function getDayTotalByDeliver($id,$day){

  //  dd('lkdkldkld');
      return \App\Models\Lists::whereDate('delivred_at', $day)
              ->where('DeliverID',$id)
              ->get()
              ->sum('total');
  }
  
  
  
    
  public function getSumDelivraison($id,$day){
      return \App\Models\Lists::whereDate('delivred_at', $day)->where('DeliverID',$id)->get()->sum('prix_de_laivraison');
  }
  
  
  
  public function checkPaied($id,$day){
      return Capsule::table('payments')
                    ->where('date', $day)
                    ->where('deliver_id',$id)
                    ->count();
  }


  public function deliverCash($id = false){
      if(isset($id) and is_numeric($id)){
        $cmds = $this->delivredByDeliver($id);
        $days = $this->groupByDay($cmds);
        $cash = [];
        foreach ($days as $day) {
           $data['deliver_id'] = $id;
           $data['day']        = $day['day'];
           $data['count']      = $day['count'];
           $data['laivraison'] = $this->getSumDelivraison($id,$day['day']);
           $data['total']      = $this->getDayTotalByDeliver($id,$day['day']);
           $data['rest']       =  $data['total'] - $data['laivraison'];
           $data['paied']      = $this->checkPaied($id,$day['day']);
           array_push($cash, $data);
        } 
        return $cash;
      }
      return true;
  }

  public function list($id = false){
    $days = \App\Models\Lists::whereNotNull('delivred_at')->select('cityID','delivred_at')->get()->toArray();
    $days = $this->groupByDay($days);
    $cash = [];
    foreach ($days as $day) {
       $data['day']     = $day['day'];
       $data['count']   = $day['count'];
       $data['total']   = \App\Models\Lists::whereDate('delivred_at', $day['day'])->get()->sum('total');
       array_push($cash, $data);
    } 
    return $cash;
  }



  
}




