<?php 

namespace App\Helpers;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \App\Models\NewOrders;

defined('BASEPATH') OR exit('No direct script access allowed');

class Stats { 
    

    public function confirmation(){
        return (new Listing(['type' => 'confirmation' ]))->countTotal;
    }


    public function employees(){
         return [
            'canceled' => (new Listing(['type' => 'canceled' ]))->countTotal,
            'waiting'  => (new Listing(['type' => 'waiting' ]))->countTotal,
            'NoAnswer' => (new Listing(['type' => 'NoAnswer' ]))->countTotal ,
            'Recall'   => (new Listing(['type' => 'recall' ]))->countTotal
        ];
    }

    public function newOrders(){
        return [
                'all'        => NewOrders::count(),
                'deleted'    => NewOrders::archived()->count(),
                'sheet'      =>  NewOrders::sheet()->count(),
                'stores'     => NewOrders::stores()->count(), 
                'duplicated' => NewOrders::duplicated()->count() ,
        ];
    }

    public function delivers(){
        return [
            'canceled' => (new Listing(['type' => 'driver_canceled' ]))->countTotal,
            'waiting'  => (new Listing(['type' => 'driver_waiting' ]))->countTotal,
            'sent'     => (new Listing(['type' => 'driver_delivred' ]))->countTotal ,
            'recall'   => (new Listing(['type' => 'driver_recall' ]))->countTotal,
            'NoAnswer' => (new Listing(['type' => 'driver_NoAnswer' ]))->countTotal,
            'delivred' => (new Listing(['type' => 'driver_delivred' ]))->countTotal,
        ];
    }

    public function deliver(){

    }


    public function employee(){

    }

    public function all(){

    }

    public function cash(){

    }


   public function ConfirmationCities(){
    $cities = \App\Models\Lists::

                                whereNull('deleted_at')
                                ->whereNotNull('accepted_at')
                                ->whereNull('duplicated_at')
                                ->whereNull('canceled_at')
                                ->whereNull('recall_at')
                                ->whereNull('delivred_at')
                                ->whereNull('verified_at')
                                ->where('statue','!=','NoAnswer')
            ->select('cityID','delivred_at')
            ->get()
            ->makeHidden('city')
            ->makeHidden('products')
            ->makeHidden('delivred_at')
            ->makeHidden('lastNoResponse')
            ->makeHidden('tentative')
            ->makeHidden('handler')
            ->makeHidden('type')
            ->makeHidden('total')
            ->makeHidden('delivred')
            ->toArray();
            return countCities($cities);
    }   




    public function cities(){
    $cities = \App\Models\Lists::whereNotNull('delivred_at')
            ->select('cityID','delivred_at')
            ->get()
            ->makeHidden('city')
            ->makeHidden('products')
            ->makeHidden('delivred_at')
            ->makeHidden('lastNoResponse')
            ->makeHidden('tentative')
            ->makeHidden('handler')
            ->makeHidden('type')
            ->makeHidden('total')
            ->makeHidden('delivred')
            ->toArray();
            return countCities($cities);
    }   


    public function products(){

        $products = \App\Models\Lists::with('products','products.product')->whereNotNull('delivred_at')
                    ->get()->pluck('products')->toArray();
        $lists = [];
        foreach ($products as $product) {
            foreach ($product as $list) {
                $data = [
                    'product'  =>  $list['product']['name'],
                    'quantity' =>  $list['quanity']
                ];
                array_push($lists, $data);
            }
        }    
        return countProucts($lists);
    }   

}




