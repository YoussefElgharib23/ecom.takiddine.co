<?php

namespace App\Models;
use illuminate\database\eloquent\model;




class Lists extends model{

    protected $table = 'lists';
    
    protected $guarded = ['id', 'created_at', 'updated_at'];
    
    protected $dates = ['canceled_at', 'created_at', 'updated_at', 'recall_at','to_deliver_at','delivred_at','no_answer_time'];

    protected $appends = ['dayDelivred','cityName','type','handler','tentative','total','lastNoResponse','total','delivred','toDeliver'];

    protected $total;


    public function getDayDelivredAttribute()
    {
        return !empty($this->delivred_at) ? $this->delivred_at->format('Y-m-d') : '';
    }

    public function getToDeliverAttribute()
    {
        return !empty($this->to_deliver_at) ? $this->to_deliver_at->diffForHumans() : '' ;
    }



    public function getCityNameAttribute()
    {

        return isset($this->realcity['city_name']) ? $this->realcity['city_name'] : '';
    }

    public function getLastNoResponseAttribute()
    {

        return !empty($this->no_answer_time) ? $this->no_answer_time->diffForHumans() : '';
    }

    public function getDelivredAttribute()
    {
        return !empty($this->delivred_at) ? $this->delivred_at->diffForHumans() : '';
    }

    public function getTotalAttribute()
    {
        return $this->products->sum('price') ?? 0;
    }

    public function getHandlerAttribute()
    {
        if($this->accepted_at){
            return 'deliver';
        }else {
            return 'employee';
        }
    }

    public function getTentativeAttribute()
    {
        if(!empty($this->no_answer)){


                $type = $this->no_answer;
                if($type == 'no_answer_1'){
                    return 'المرحلة الأولى' ;
                }
                // No answer 2
                if($type == 'no_answer_2'){
                    return 'المرحلة الثانية' ;
                }
                // No answer 3
                if($type == 'no_answer_3'){
                    return 'المرحلة الثالثة' ;
                }
                // No answer 4
                if($type == 'no_answer_4'){
                    return 'المرحلة الرابعة' ;
                }
                
                // No answer 5
                if($type == 'no_answer_5'){
                    return 'المرحلة الخامسة';
                }

                // No answer 6
                if($type == 'no_answer_6'){
                    return 'المرحلة السادسة';
                }       

                // No answer 7
                if($type == 'no_answer_7'){
                    return 'المرحلة السابعة';
                }

                // No answer 8
                if($type == 'no_answer_8'){
                    return 'المرحلة الثامنة';
                }

        }
    }

    public function getTypeAttribute()
    {

        $by = '';
        if(isset($_SESSION['auth-admin']) or isset($_SESSION['auth-data'])){
            if($this->accepted_at){
            $by = '- عند الموزع    ' ;
            }else {
                 $by = '- عند الموظفة  ';
            }
        }
       
        
        if(!empty($this->delivred_at)){
            return 'تم توزيعها'  . $by ; 
        }
        if(!empty($this->canceled_at)){
            return 'ملغية'  . $by ;
        }
        if(!empty($this->recall_at)){
            return 'اعادة الإتصال'  . $by ;
        }
        if(!empty($this->no_answer)){
            return 'لا يجيب '    . ' - ' .  $this->tentative . $by ;
        } 
        
        return 'قيد المعالجة' . $by;
    }



    public function products(){
        return $this->hasMany('\App\Models\MultiSale','listID');
    }
    

    public function items(){
        $items = [];
        $total = 0;
        foreach ($this->products as $product) {
                $item = [
                    'name' =>   $product->product->name ?? '',
                    'price' =>   $product->price ?? 0,
                    'quantity' =>  $product->quanity ?? 0,
                ];
                $total = $total +  $product->price; 
                array_push($items, $item);
        }
        $this->items = $items;
        return $items;
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
    
    
    public function realcity(){
        return $this->belongsTo('\App\Models\Cities','cityID');
    }


    protected $product;


    /*********************** Scopes & Helpers Start *****************/
    public function scopeStockDelivred($query,$provider,$product)
    {

        $this->product = $product;
        return $query->whereNotNull('delivred_at')
            ->where( 'DeliverID' , $provider )
            ->whereHas('products.product', function ($query) {
                    return $query->where('id', '=', $this->product);
            })->get();
    }

    /*
    public function scopeStockEncours($query,$provider,$product)
    {

        $this->product = $product;        
    }
    */






    public function EmployeeMarkAsRecall(){
        
    }
    
    public function EmployeeMarkAsSent($deliver_at){
        $this->canceled_at    = NULL;
        $this->recall_at      = NULL;
        $this->statue         = NULL;
        $this->cancel_reason  = NULL;
        $this->no_answer      = NULL;
        $this->no_answer_time = NULL;
        $this->recall_at      = NULL;
        $this->to_deliver_at  = $deliver_at;
        $this->accepted_at    = \Carbon\Carbon::now();
        return $this; 
    }
    

    public function EmployeeMarkAsUnanswred(){

        $this->delivred_at    = NULL;
        $this->canceled_at = NULL;
        $this->recall_at = NULL;
        $this->cancel_reason = NULL;

        if(is_null($this->count_no_answer_employee)){
            $this->count_no_answer_employee = 1;
            return $this;
        }

        if($this->count_no_answer_employee == 7 ) {
            $this->delivred_at    = NULL;
            $this->recall_at      = NULL;
            $this->no_answer      = NULL;
            $this->no_answer_time = NULL;
            $this->recall_at      = NULL;
            $this->statue         = NULL;
            $this->cancel_reason = "ملغى بسبب لا يجيب لـ 7 مرات";
            $this->canceled_at   =  \Carbon\Carbon::Now();
            $this->save();
            return $this;
        }
      
        $this->count_no_answer_employee = $this->count_no_answer_employee + 1;
        return $this;    
    }






    
    public function EmployeeMarkAsCanceled(){
        
    }

    public function reset(){
        $this->canceled_at = NULL;
        $this->recall_at = NULL;
        $this->cancel_reason = NULL;
        $this->no_answer = NULL;
        $this->statue = NULL;
        $this->recall_at = NULL;
        $this->delivred_at = NULL; 
    }





    

    



    /********      get lists for provider             ******/
    public function scopeCanceledByProviders(){
        
    }

    public function scopeRecallByProviders(){
        
    }

    public function scopeUnansweredByProviders(){
        
    }

    public function scopeWaitingForProviders(){
        
    }




    public function scopeProvider($query,$id){
        return $query->where('DeliverID',$id);
    }

    public function scopeEmployee($query,$id){
        return $query->where('mowadafaID',$id);
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

    /***************** time scopes *********************
    public function scopeCreatedBetween($query,$from,$to) {
        return $query->whereNotNull('delivred_at');
    }
 
   
    */
 public function scopeCreatedDate($query,$day) {
        return $query->whereDate('created_at', '=', $day );
    }
    public function isFull(){

        $full = true;

        if( empty($this->tel) or 
            empty($this->name) or 
            empty($this->adress) or 
            empty($this->cityID) or
            empty($this->DeliverID) ) {

            $full = false;

        }


        return $full;
            

    }



    public function MarkAsRecall($recall_at){

        if($this->count_no_answer == 4 ){
            return $this;
        }

        $this->canceled_at   = NULL;
        $this->recall_at     = $recall_at;
        $this->statue        = NULL;
        $this->cancel_reason = NULL;
        $this->no_answer     = NULL;
        $this->no_answer_time     = NULL;
        $this->statue = 'recall';
        $this->save();
        return $this;
    }
    

    public function ProviderMarkAsDelivred(){

            $this->canceled_at = NULL;
            $this->recall_at = NULL;
            $this->cancel_reason = NULL;
            $this->no_answer = NULL;
            $this->statue = NULL;
            $this->recall_at = NULL;
            $this->delivred_at = \Carbon\Carbon::Now();
            $this->statue = 'sent';
            return $this;

    }

    public function MarkAsCanceled($reason){

        $this->delivred_at    = NULL;
        $this->canceled_at    = \Carbon\Carbon::Now();

        if($this->count_no_answer == 4 ){
            return $this;
        }

        $this->canceled_at    = \Carbon\Carbon::Now();
        
        if(isset($_SESSION['auth-suivi'])){
           $this->deleted_at  = \Carbon\Carbon::Now();
        }

        $this->delivred_at    = NULL;
        $this->recall_at      = NULL;
        $this->no_answer      = NULL;
        $this->statue         = NULL;
        $this->no_answer_time = NULL;
        $this->recall_at      = NULL;
        $this->cancel_reason  = $reason;
        
        return $this;
    }


    public function ProviderMarkAsUnanswred(){

        $this->canceled_at = NULL;
        $this->recall_at = NULL;
        $this->delivred_at    = NULL;


      
        if(is_null($this->count_no_answer_provider)){
            $this->count_no_answer_provider = 1;
            $this->save();
            return $this;
        }
        
        
        if($this->count_no_answer_provider > 4 ){
            $this->delivred_at    = NULL;
            $this->no_answer      = NULL;
            $this->no_answer_time = NULL;
            $this->statue         = NULL;
            $this->count_no_answer = 5 ;
            $this->count_no_answer_provider = 5 ;
            $this->cancel_reason = "ملغى بسبب لا يجيب";
            $this->canceled_at   =  \Carbon\Carbon::Now();
            $this->save();
            return $this;
        }
        
        
        
        if(is_null($this->count_no_answer)){
            $this->count_no_answer = 1;
        }
    
        if($this->count_no_answer == 4 ) {
            $this->delivred_at    = NULL;
            $this->no_answer      = NULL;
            $this->no_answer_time = NULL;
            $this->statue         = NULL;
            $this->count_no_answer = 5 ;
            $this->count_no_answer_provider = 5 ;
            $this->cancel_reason = "ملغى بسبب لا يجيب";
            $this->canceled_at   =  \Carbon\Carbon::Now();
            $this->save();
            return $this;
        }

        $this->count_no_answer = $this->count_no_answer + 1;
        $this->count_no_answer_provider = $this->count_no_answer_provider + 1;
        
        $this->save();
        return $this;
    }



    public function scopeCurrent($query,$type){
        if(in_array($type, ['provider','employee'])){

            if($type == 'provider'){
                if ( Deliver() ){
                    return $query->where('DeliverID',Deliver());
                }
                return $query;
            }

            if($type == 'employee'){
                if ( Employee() ) {
                    return $query->where('mowadafaID',Deliver());
                }
            }

        }

        
    }




    
}