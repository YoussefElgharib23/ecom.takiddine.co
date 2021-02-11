<?php

namespace App\Models;
use illuminate\database\eloquent\model;


class Api extends model{

    protected $table = 'lists';
    
       protected $guarded = ['id', 'created_at', 'updated_at'];

    
    public function products(){
        return $this->hasMany('\App\Models\MultiSale','listID');
    }
    
    public function deliver(){
       return $this->belongsTo('\App\Models\User','DeliverID'); 
    }

    public function employee(){
       return $this->belongsTo('\App\Models\User','mowadafaID');
    }
  
    public function city(){
        return $this->belongsTo('\App\Models\Cities','cityID')->withDefault([
        'city' => 'غير معروفة',
        ]);
    }


   public function scopeLastWeekDelivred($query){
        $date    = \Carbon\Carbon::today()->subDays(7);
        $query->with('products')->whereNotNull('delivred_at')->where('delivred_at','>=',$date)->orderBy('delivred_at', 'desc');
   }






   public function scopeLastMonthDelivred($query){
        $date    = \Carbon\Carbon::today()->subDays(30);
        $query->with('products')->whereNotNull('delivred_at')->where('delivred_at','>=',$date)->orderBy('delivred_at', 'desc');
   }


    

   public function scopeLastDelivredFromTo($query,$from,$to){
     //  dd($from);
     //   dd($to);
        $query->with('products')->whereNotNull('delivred_at')->whereBetween('delivred_at',[$from,$to])->orderBy('delivred_at', 'desc');
   }



    



    /***************** Not *********************/
    public function scopeNotDeleted($query) {
        return $query->whereNull('deleted_at');
    }
    public function scopeNotDuplicated($query) {
        return $query->whereNull('duplicated_at');
    }
    public function scopeNotCanceled($query) {
        return $query->whereNull('canceled_at');
    }
    public function scopeNotDelivred($query) {
        return $query->whereNull('delivred_at');
    }
    public function scopeNotRecall($query) {
        return $query->whereNull('recall_at');
    }
    public function scopeNotAccepted($query) {
        return $query->whereNull('accepted_at');
    }
    public function scopeNotVerified($query) {
        return $query->whereNull('verified_at');
    }
    public function scopeNotNoAnswer($query) {
       return $query->where('statue','=!','NoAnswer');
    }
     


    /***************** has *********************/
    public function scopeAccepted($query) {
        return $query->whereNotNull('accepted_at');
    }
    public function scopeVerified($query) {
        return $query->whereNotNull('verified_at');
    }
    public function scopeCanceled($query) {
        return $query->whereNotNull('canceled_at');
    }
    public function scopeRecall($query) {
        return $query->whereNotNull('recall_at');
    }
    public function scopeDelivred($query) {
        return $query->whereNotNull('delivred_at');
    }
    public function scopeNoAnswer($query) {
       return $query->where('statue','NoAnswer');
    }



    public function scopeWaitingForProviders($query) {
       return $query->NotDeleted()->NotDuplicated()->Accepted()->Verified()->NotCanceled()->NotDelivred()->NotRecall()->where('statue','!=','NoAnswer');
                            
    }

    public function scopeCanceledForProviders($query) {
       return $query->NotDeleted()->NotDuplicated()->Accepted()->Verified()->Canceled()->NotDelivred()->NotRecall()->where('statue','!=','NoAnswer');
    }

    public function scopeUnanswredForProviders($query) {
       return $query->NotDeleted()->NotDeleted()->NotDuplicated()->Accepted()->Verified()->NotCanceled()->NotRecall()->NotDelivred()->where('statue','NoAnswer');
    }

    public function scopeRecallForProviders($query) {
       return $query->NotDeleted()->NotDuplicated()->Accepted()->Verified()->NotCanceled()->Recall()->NotDelivred()->where('statue','!=','NoAnswer');
    }

    public function scopeDelivredForProviders($query) {
       return $query->NotDeleted()->NotDuplicated()->Accepted()->Verified()->Delivred();
    }

    public function scopeWaitingForEmployees($query) {
       return $query->whereNull('duplicated_at')
                          ->whereNull('verified_at')
                          ->whereNull('accepted_at')
                          ->whereNull('deleted_at')
                          ->whereNull('no_answer_time')
                          ->whereNull('duplicated_at')
                          ->whereNull('recall_at')
                          //->where('statue','!=','NoAnswer');
                          ->whereNull('canceled_at');
    }


    public function scopeCanceledForEmployees($query) {
       return $query->whereNull('duplicated_at')
                          ->whereNull('verified_at')
                          ->whereNull('accepted_at')
                          ->whereNull('deleted_at')
                          ->whereNotNull('canceled_at');
    }




    public function scopeUnanswredForEmployees($query) {
       return $query->whereNull('duplicated_at')
                          ->whereNull('verified_at')
                          ->whereNull('accepted_at')
                          ->whereNull('deleted_at')
                          ->where('statue','NoAnswer')->whereNull('canceled_at');
    }




    public function scopeRecallForEmployees($query) {
       return $query->whereNull('duplicated_at')
                          ->whereNull('verified_at')
                          ->whereNull('accepted_at')
                          ->whereNull('deleted_at')
                          ->whereNull('canceled_at')->whereNotNull('recall_at')->whereNull('no_answer_time');
    }


    public function scopeSentForEmployees($query) {
       return $query->whereNull('duplicated_at')
                          ->whereNotNull('verified_at')
                          ->whereNotNull('accepted_at')
                          ->whereNull('deleted_at')
                          ->whereNull('canceled_at');
    }










    public function scopeSelecting($query) {
       return $query->select('mowadafaID','no_answer_time','DeliverID','id','created_at','accepted_at','verified_at','delivred_at','deleted_at','canceled_at','duplicated_at','recall_at','statue');
    }

   public function scopeOrderCreated($query) {
       return $query->orderBy('created_at', 'desc');
    }



   public function scopeDuration($query,$params) {

      if(isset($params['duration'])){
          if($params['duration']  == 'month' ){
              $date    = \Carbon\Carbon::today()->subDays(30);  
          }
          if($params['duration']  == 'week' ){
              $date    = \Carbon\Carbon::today()->subDays(7);  
          }
          
          return $query->where('created_at','>=',$date);
      }

      if(isset($params['from']) and isset($params['to'])){
          $from = str_replace('/','-',$params['from']);
          $to = str_replace('/','-',$params['to']);
          return $query->whereBetween('created_at',[$from,$to]);
      }


       return $query->orderBy('created_at', 'desc');
    }





    
}