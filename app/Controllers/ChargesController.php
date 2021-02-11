<?php

namespace App\Controllers;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;
use \App\Models\{User , Charges};
use \Carbon\Carbon;
use Illuminate\Database\Capsule\Manager as Capsule;

defined('BASEPATH') OR exit('No direct script access allowed');

class ChargesController extends Controller {


    public $folder  = 'charges' ;
    public $model = 'Charges';
    public $route = [ 'index' => 'charges'   ];
    public $control = __CLASS__ ;
    
    
    public $messages = [
        'created'           => 'تم اضافة الحساب بنجاح',
        'deleted'           => 'Ads has been deleted successfully',
        'updated'           => 'Ads has been updated successfully',
        'bulkDelete'        => 'All Adss deleted successfully',
        'cloned'            => 'Ads has been duplicated successfully',  
    ];
    
 
    // Name Spacing
	private $modelSpace = '\\App\\Model\\';
	private $controllerSpace = '\\App\\Controllers\\';
    
    // Tools & Helpers
    protected $container;
    protected $helper;
    protected $lang;
    protected $validator;
    
    
    // Call a Model
    public function init( $model = false ) {
        if($model){
              return '\\App\\Models\\' . ucfirst($model);
        }
        if($this->model) {
            return '\\App\\Models\\' . ucfirst($this->model);
        }
        return false;
    }
      
     

          
    
    public function store($request,$response,$args){
            $content = new \App\Models\Charges();
            $content->name          = $_POST['name'];
            $content->value         = $_POST['value'];
            $content->type          = $_POST['type'];
            $content->save();
    }
    
    public function update($request,$response,$args){
        $modelClass = $this->init();
        $id = rtrim($args['id'], '/');
        if(is_numeric($id)){
            if(class_exists($modelClass)){
                $content = $modelClass::find($id);
                if($content) {   
                    $this->saveData($content,$request->getParams(),'update');
                    $this->flashsuccess($this->messages['updated']);  
                }
            }
        }
        return $response->withStatus(302)->withHeader('Location', $this->router->pathFor($this->route['index']));
    }
      
    
 public function getPageTitle($type){   
    switch ($type) {
        case "all":
            return "قائمة كل التكاليف المدفوعة وغير المدفوعة";
            break;
        case "paied":
            return "قائمة التكاليف المدفوعة";
            break;
        case "notpaied":
            return "قائمة التكاليف الغير المدفوعة";
            break;
        default:
            return "قائمة كل التكاليف المدفوعة وغير المدفوعة";
    }
 }
    


    public function save($request,$response,$args){
        Capsule::table('defaultcharges')->insert(['name' => $_POST['name'] , 'value' => $_POST['amount'] ]);
    }
    


    public function paied($request,$response){
        $id                = $_POST['id'];
        $charges           = Charges::find($id);
        $now               = Carbon::now();
        $charges->paied_at = $now;
        $charges->save();
        return $now->toDateString();
    }
    
        
      
  public function index($request,$response) {
        
        $params = $request->getParams();
        $type   = $params['type'] ?? NULL;
        $paied  = $params['paied'] ?? NULL;
        $from   = $params['from'] ?? NULL;

        $charges = Charges::query();

        if(isset($type) and !empty($type) and !is_null($type) and in_array($type, ['fixed','variable'])){
           $charges =  $charges->where('type',$type);
        }


        if(isset($paied) and !empty($paied) and !is_null($paied) and in_array($paied, ['true','false'])){
           $charges = $paied == 'true' ?  $charges->whereNotNull('paied_at') : $charges->whereNull('paied_at');
        }

        if(isset($from) and !empty($from) and !is_null($from) and in_array($from, ['today','week','month','2-months','year'])){

            if($from == 'today') {
                $charges =  $charges->whereDate('created_at', Carbon::today());
            }

            if($from == 'week') {
                $charges =  $charges->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
            }
            
            if($from == 'month') {
                $currentMonth = date('m');
                $charges =  $charges->whereRaw('MONTH(created_at) = ?',[$currentMonth]);
            }
            
            if($from == 'year') {
                $charges = $charges->whereYear('created_at', date('Y', strtotime('-1 year')));
            }
        }
      
        // Pagination settings
        $count          = $charges->count();         
        $page           = ($request->getParam('page', 0) > 0) ? $request->getParam('page') : 1;
        $limit          = ($request->getParam('limit', 0) > 0) ? $request->getParam('limit') : 10;; 
        $lastpage       = (ceil($count / $limit) == 0 ? 1 : ceil($count / $limit));   
        $skip           = ($page - 1) * $limit;

        // get the listing
        $charges          = $charges->skip($skip)->take($limit);
        $total            = $charges->sum('value');
        $content          = $charges->get();
        
   
        // get the url and clean it for pagination
        $url = $request->getParams();
        unset($url['page']);
        $URLparams = http_build_query($url);
        $urlPattern     =  !empty($URLparams) ? '?'.$URLparams. "&page=(:num)" : "?page=(:num)";
        
        $pagination = new \App\Helpers\Paginator($count, $limit, $page, $urlPattern);

        $fixed_list = Capsule::table('defaultcharges')->get();
        return $this->view->render($response, 'admin/admin/charges.twig', compact('total','count','content','fixed_list','pagination')); 
  }
  
}

