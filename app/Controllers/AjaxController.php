<?php

namespace App\Controllers;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use \App\Models\User;
use \App\Helpers\Search;
use Illuminate\Database\Capsule\Manager as DB;

defined('BASEPATH') OR exit('No direct script access allowed');

class AjaxController extends Controller {
    



    public function stats($request,$response) {
       

        $count = 'SELECT count(*) FROM new';
        $after_hour_hlf =  \Carbon\Carbon::now()->subMinutes(180)->toDateTimeString();
        $now =  \Carbon\Carbon::now();


$result =   DB::select('SELECT 
    ( ' . $count . ' where `id` is not null  ) as new ,
    ( ' . $count . ' where `source` = ? and `deleted_at` is null and `duplicated_at` is null ) as  sheet ,
    ( ' . $count . ' where `source` != ? and `duplicated_at` is null and `deleted_at` is null ) as stores ,
    ( ' . $count . ' where `duplicated_at` is not null and `deleted_at` is null ) as duplicated ,
    ( ' . $count . ' where `deleted_at` is not null ) as deleted ,
    
( SELECT count(*) as aggregate from lists where deleted_at is null and accepted_at is not null and duplicated_at is null and canceled_at is null and recall_at is null and delivred_at is null and verified_at is null) as confirmation

    '


 
    ,['sheet','sheet',$after_hour_hlf,$now,'NoAnswer'])[0];

        $data = [
                'all'        => $result->new,
                'deleted'    => $result->deleted,
                'sheet'      => $result->sheet,
                'stores'     => $result->stores,
                'duplicated' => $result->duplicated,
                'confirmation' => $result->confirmation,
        ];

        echo json_encode($data);

    }

        





    public function search($request,$response) {
        $post     = clean($request->getParams());
        $route    = $request->getAttribute('route')->getName();
        $search   = new Search($post,$route);
        $view     = '/admin/elements/instant-search.twig';
        $lists    = $search->search();
        $number   = $search->number();
        return $this->view->render($response, $view, compact('lists'));
    }

        


    public function delete($request,$response,$args) {
        $list = \App\Models\Lists::find($args['id']);
        $list->deleted_at = \Carbon\Carbon::now();
        $list->save();
        header('Location: '.$_SERVER['PHP_SELF']);
die;
    }

    


}
