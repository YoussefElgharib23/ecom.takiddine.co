<?php

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

// make namespace short
use \App\Controllers\AuthController as auth;
use \App\Middleware\flashMiddleware as flash;
use \App\Middleware\OldInputMidddleware as old;
use \App\Middleware\logoutMiddleware as logout;
use \App\Controllers\StockGeneralController as StockGeneral;
use \App\Controllers\EmbalageController as Embalage;
use \App\Controllers\ApiController as Api;


// security , disable direct access
defined('BASEPATH') or exit('No direct script access allowed');



$app->post('/login[/]', auth::class .':login')->setName('login');
$app->get('/logout[/]', auth::class .':logout')->setName('logout')->add( new logout($container) );



$app->get('/cleanstock', function(){
    \App\Models\StockEntree::truncate();
    \App\Models\StockGeneral::truncate();
    \App\Models\StockSortie::truncate();
    \App\Models\StockSortieList::truncate();
    \App\Models\HistoryEntree::truncate();
    \App\Models\Retour::truncate();

});


$app->get('/cleancmds', function(){
    \App\Models\MultiSale::truncate();
    \App\Models\Lists::truncate();
});



$app->group('', function ($container) use($app) {

    


    
    // Dashboard index
    $this->get('[/]','Lists:stats')->setName('admin.index')->add( new App\Middleware\adminMiddleware($container));
   
   
   
    $this->any('/export/data', 'ExcelExporter:exportData')->setName('exportData');  
        
    $this->post('/instant/search', 'Ajax:search');  
    $this->any('/instant/delete/{id}', 'Ajax:delete');  



    // Stock General System
    $this->group('/stock/general', function (){
        $this->get('[/]', StockGeneral::class.':index')->setName('stockGeneral');
        $this->post('/get/rest[/]', StockGeneral::class.':rest')->setName('rest');
        $this->get('/create_entree[/]', StockGeneral::class.':create_entree')->setName('stockGeneral.create.entree');
        $this->post('/create_entree[/]', StockGeneral::class.':store_entree')->setName('stockGeneral.create.entree');
        $this->get('/create_sortie[/]', StockGeneral::class.':create_sortie')->setName('stockGeneral.create.sortie');
        $this->post('/create_sortie[/]', StockGeneral::class.':store_sortie')->setName('stockGeneral.create.sortie');
        $this->get('/waiting[/]', StockGeneral::class.':waitingGet')->setName('stockGeneral.waitingGet');
        $this->post('/waiting[/]', StockGeneral::class.':waitingAction')->setName('stockGeneral.waitingAction');
        $this->post('/validateEntree[/]', StockGeneral::class.':validateEntree')->setName('stockGeneral.validateEntree');
        $this->post('/loadSortieLists[/]', StockGeneral::class.':loadSortieList')->setName('stockGeneral.loadSortieList');
        $this->post('/validateSortieList[/]', StockGeneral::class.':validateSortieList')->setName('stockGeneral.validateSortieList');
        $this->any('/loadEntreeHistory[/]', StockGeneral::class .':loadEntreeHistory')->setName('stockGeneral.loadEntreeHistory');
        $this->any('/loadHistorySortie[/]', StockGeneral::class .':loadHistorySortie')->setName('stockGeneral.loadSortieHistory');
        $this->any('/save/retour[/]', StockGeneral::class .':saveRetour')->setName('stockGeneral.save.retour');
        $this->any('/list/retour[/]', StockGeneral::class .':listRetour')->setName('stockGeneral.retour');        
    });




    $this->group('/embalage', function (){
        $this->get('[/]', Embalage::class .':index')->setName('embalage');
        $this->any('/sortie[/]', Embalage::class .':sortie')->setName('embalage.sortie');
        $this->any('/entree[/]', Embalage::class .':entree')->setName('embalage.entree');
        $this->any('/createproduct[/]', Embalage::class .':createproduct')->setName('embalage.createproduct');
        $this->any('/storeStock[/]', Embalage::class .':storeStock')->setName('embalage.storeStock');
        $this->any('/storeStockSortie[/]', Embalage::class .':storeStockSortie')->setName('embalage.storeStockSortie');
        $this->any('/checkSumEntree[/]', Embalage::class .':checkSumEntree')->setName('checkSumEntree');
        $this->any('/getSotieTotal[/]', Embalage::class .':getSotieTotal')->setName('getSotieTotal');
    });






    $this->group('/deliver/listing', function (){
        $this->get('/new[/]', 'Deliver:index')->setName('deliver.orders');    
        $this->get('/canceled[/]', 'Deliver:canceled')->setName('deliver.orders.canceled');    
        $this->get('/recall[/]', 'Deliver:recall')->setName('deliver.orders.recall');    
        $this->get('/delivered[/]', 'Deliver:delivered')->setName('deliver.orders.delivered');    
        $this->get('/no/response[/]', 'Deliver:NoResponse')->setName('deliver.orders.NoResponse');    
    });

 
    $this->group('/employee/listing', function (){
        $this->get('/new[/]', 'Employee:index')->setName('employee.orders');    
        $this->get('/canceled[/]', 'Employee:canceled')->setName('employee.orders.canceled');    
        $this->get('/recall[/]', 'Employee:recall')->setName('employee.orders.recall');    
        $this->get('/no/response[/]', 'Employee:NoResponse')->setName('employee.orders.NoResponse');    
    });



    $this->group('/delivers/listing', function (){
        $this->get('/new[/]', 'Deliver:index')->setName('delivers.orders');    
        $this->get('/canceled[/]', 'Deliver:canceled')->setName('delivers.orders.canceled');    
        $this->get('/recall[/]', 'Deliver:recall')->setName('delivers.orders.recall');    
        $this->get('/delivered[/]', 'Deliver:delivered')->setName('delivers.orders.delivered');    
        $this->get('/no/response[/]', 'Deliver:NoResponse')->setName('delivers.orders.NoResponse');    
    });

 
    $this->group('/employees/listing', function (){
        $this->get('/new[/]', 'Employee:index')->setName('employees.orders');    
        $this->get('/canceled[/]', 'Employee:canceled')->setName('employees.orders.canceled');    
        $this->get('/recall[/]', 'Employee:recall')->setName('employees.orders.recall');    
        $this->get('/no/response[/]', 'Employee:NoResponse')->setName('employees.orders.NoResponse');
    });















    $this->any('/statistiques', 'Pages:statistiques')->setName('statistiques');  
    $this->any('/statistiques/daily/employees', 'Pages:statistiques_employees')->setName('statistiques.employees');  
    $this->any('/statistiques/daily/delivers', 'Pages:statistiques_delivers')->setName('statistiques.delivers');  
    $this->any('/statistiques/products', 'Pages:statistiques_products')->setName('statistiques.products');  
    $this->any('/statistiques/others', 'Pages:others')->setName('statistiques.others');  
    $this->any('/reception', 'Pages:reception')->setName('reception');  
    $this->any('/confirmation', 'Lists:confirmation')->setName('confirmation');  
    $this->any('/suivi', 'Lists:suivi')->setName('suivi.listing');  
    $this->get('/revenue', 'Pages:revenue')->setName('pages.revenue');  
    $this->get('/revenue/spent/{date}/{ads}', 'Pages:ads')->setName('pages.ads');  
    $this->get('/search', 'Pages:search')->setName('pages.find');  
    $this->any('/verfiey/{date}/{money}/{deliver_id}', 'Lists:VerfiedCash')->setName('cash.verified');  
    $this->any('/cities', 'Pages:cities')->setName('pages.cities');
    $this->any('/cities/edit/{id}', 'Pages:citiesedit')->setName('pages.cities.edit');
    $this->get('/cash', 'Pages:cash')->setName('pages.cash');
    $this->get('/export/revenue', 'Pages:ExportRevenue')->setName('export.revenue');
    $this->get('/double', 'Pages:double')->setName('pages.duplique');  



    $this->any('/deliver/cash[/]', 'Pages:Delivercash')->setName('deliver.cash');
    $this->any('/loadHistory', 'Lists:loadHistory');  
    $this->any('/loadEmployeesCount', 'Lists:loadEmployeesCount');  

  
  
    //
    $this->any('/download/excel/{jour}/{deliver}', 'Pages:downloadExcelDeliverJour')->setName('deliver.downloadJour');    
  
  
     // new orders system
    $this->group('/new-orders', function (){
        $this->get('[/]', 'NewOrders:index')->setName('NewOrders');
        $this->post('/upload[/]', 'NewOrders:uploadTheSheet')->setName('NewOrders.upload');
        $this->post('/city[/]', 'NewOrders:assignToCity')->setName('NewOrders.assignToCity');
        $this->any('/product[/]', 'NewOrders:assignToProduct')->setName('NewOrders.assignToProduct');
        $this->any('/employee[/]', 'NewOrders:assignToEmployee')->setName('NewOrders.assignToEmployee');
        $this->any('/delete[/]', 'NewOrders:delete')->setName('NewOrders.delete');
        $this->any('/restore[/]', 'NewOrders:restoreOrders')->setName('NewOrders.restore');
        $this->any('/duplicates[/]', 'NewOrders:assignToEmployee')->setName('NewOrders.assignToEmployee');
        $this->any('/search[/]', 'NewOrders:AdvancedSearch')->setName('NewOrders.AdvancedSearch');
        $this->any('/loadcount[/]', 'NewOrders:loadcount')->setName('NewOrders.loadcount');
        $this->any('/restoreduplicates[/]', 'NewOrders:RestoreFromDuplicates')->setName('NewOrders.RestoreFromDuplicates');
        $this->any('/remove[/]', 'NewOrders:remove')->setName('NewOrders.remove');
    });



    // new orders system
    $this->group('/data', function (){
        $this->get('[/]', 'Data:index')->setName('data');
        $this->post('/upload[/]', 'Data:uploadTheSheet')->setName('data.upload');
        $this->post('/city[/]', 'Data:assignToCity')->setName('data.assignToCity');
        $this->any('/product[/]', 'Data:assignToProduct')->setName('data.assignToProduct');
        $this->any('/employee[/]', 'Data:assignToEmployee')->setName('data.assignToEmployee');
        $this->any('/delete[/]', 'Data:delete')->setName('data.delete');
        $this->any('/restore[/]', 'Data:restoreOrders')->setName('data.restore');
        $this->any('/duplicates[/]', 'Data:assignToEmployee')->setName('data.assignToEmployee');
        $this->any('/search[/]', 'Data:AdvancedSearch')->setName('data.AdvancedSearch');
        $this->any('/loadcount[/]', 'Data:loadcount')->setName('data.loadcount');
        $this->any('/restoreduplicates[/]', 'Data:RestoreFromDuplicates')->setName('data.RestoreFromDuplicates');
        $this->any('/remove[/]', 'Data:remove')->setName('data.remove');
    });

     
    // employee and admin Lists System
    $this->group('/lists', function (){
        $this->any('[/]', 'Lists:index')->setName('lists');
        $this->any('/export/deliver', 'ExcelExporter:exportDeliver')->setName('lists.deliver.export');
        $this->any('/export/deliver/selected', 'ExcelExporter:exportDeliverSelected')->setName('lists.deliver.export.selected');
        $this->any('/suivi', 'Lists:index')->setName('suivi');
        $this->any('/reset', 'Lists:reset');
        $this->any('confirmLists', 'Lists:confirm')->setName('lists.confirm');
        $this->any('/export', 'ExcelExporter:export')->setName('lists.export');
        $this->any('/exportConfirmation', 'ExcelExporter:exportConfirmation')->setName('lists.exportConfirmation');
        $this->any('/cash/', 'Lists:cash');
        $this->any('/transform[/]', 'Lists:transform')->setName('lists.transform');
        $this->any('/all[/]', 'Lists:index')->setName('lists.all');
        $this->get('/create[/]', 'Lists:createForm')->setName('lists.create');
        $this->post('/create[/]', 'Lists:create')->setName('lists.create');
        $this->get('/edit/{id}[/]', 'Lists:edit')->setName('lists.edit');
        $this->post('/edit/{id}[/]', 'Lists:update')->setName('lists.update');
        $this->any('/delete/{id}[/]', 'Lists:delete')->setName('lists.delete');
        $this->any('/blukdelete[/]', 'Lists:blukdelete')->setName('lists.blukdelete');
        $this->any('/setCanceled[/]', 'Lists:setCanceled')->setName('lists.setCanceled');
        $this->any('/setNoResponse[/]', 'Lists:setNoResponse')->setName('lists.NoResponse');
        $this->any('/setSent[/]', 'Lists:setSent')->setName('lists.Sent');
        $this->any('/setRecall[/]', 'Lists:setRecall')->setName('lists.setRecall');
        $this->any('/stock[/]', 'Lists:stock')->setName('lists.stock');
        $this->any('/loadDeliverdbyDeliver[/]', 'Lists:loadDeliverdbyDeliver')->setName('lists.loadDeliverdbyDeliver');
        $this->any('/loadAction/{id}', 'Lists:loadListDataWithActions')->setName('loadListDataWithAction');
        $this->any('/deliveryPrice/', 'Lists:loadUserDeliveryPrice');
        $this->any('/search/', 'Lists:search')->setName('lists.search');
        $this->any('/setSentEmployee', 'Lists:setSentEmployee');
        $this->any('/{id}', 'Lists:loadListData')->setName('loadListData');
    });


    // deliver Lists System
    $this->group('/sentlists', function (){
        $this->any('', 'Lists:index')->setName('Sentlists');        
        $this->any('/setCanceled[/]', 'Lists:setCanceled')->setName('Sentlists.setCanceled');
        $this->any('/setNoResponse[/]', 'Lists:setNoResponse')->setName('Sentlists.NoResponse');
        $this->any('/setDelivred[/]', 'Lists:setDelivred')->setName('Sentlists.Sent');
        $this->any('/setRecall[/]', 'Lists:setRecall')->setName('Sentlists.setRecall');
        $this->any('/edit/{id}[/]', Sentlists::class .':edit')->setName('Sentlists.edit');
    });

        // users System
    $this->group('/users', function () {
        $this->get('[/]', 'Users:index')->setName('users');
        $this->any('/create[/]', 'Users:create')->setName('users.create');
        $this->any('/delete/{id}[/]', 'Users:delete')->setName('users.delete');
        $this->any('/activate/{id}[/]', 'Users:delete')->setName('users.activate');
        $this->any('/block/{id}[/]', 'Users:block')->setName('users.block');
        $this->any('/mutliAction[/]','Users:mutliAction')->setName('users.mutliAction');        
        $this->get('/export/csv[/]', 'Users:export_csv')->setName('usersToCsv');
        $this->get('/export/pdf[/]', 'Users:export_pdf')->setName('usersToPdf');
        $this->get('/blukdelete[/]', 'Users:blukdelete')->setName('users.blukdelete');
        $this->any('/{username}[/]', 'Users:edit')->setName('users.edit');
    });

    // Products system
    $this->group('/products', function (){
        $this->get('[/]', 'Products:index')->setName('products');
        $this->any('/create[/]', 'Products:create')->setName('products.create');
        $this->any('/edit/{id}[/]', 'Products:edit')->setName('products.edit');
        $this->get('/delete/{id}[/]', 'Products:delete')->setName('products.delete');
        $this->get('/duplicate/{id}[/]', 'Products:duplicate')->setName('products.duplicate');
        $this->get('/blukdelete[/]', 'Products:blukdelete')->setName('products.blukdelete');
    });

   
    // المراقبة اليومية
    $this->group('/charges', function (){
        $this->any('', 'Charges:index')->setName('charges');        
        $this->post('/store/', 'Charges:store')->setName('charges.store');        
        $this->any('/edit/{id}/', 'Charges:edit')->setName('charges.edit');        
        $this->post('/store/{id}/', 'Charges:edit')->setName('charges.update');        
        $this->post('/save', 'Charges:save')->setName('charges.save');        
        $this->post('/paied', 'Charges:paied');        
    });
    


     $this->any('/load/ajax/stats', 'Ajax:stats');  



    
    
})->add( new App\Middleware\authMiddleware($container) );




$app->post('/storeApi/[/]', function ($request, $response, $args) {  

        $productname = '';
        if($_POST['ProductReference']){
             $found = \App\Models\Product::where('name',trim($_POST['ProductReference']))->first();
             if($found){
                 $productname = $found->id;
             }
        }
    
       $data = [
            'name'  =>  $_POST['name'] ,
            'tel'  =>  $_POST['tel'] ,
            'adress'  =>  $_POST['adress'] ,
            'city'  =>  $_POST['city'] ,
            'quantity' => $_POST['quantity'],
            'price' =>  $_POST['price'],
            'source' =>  $_POST['source'],
            'ProductReference' => $_POST['ProductReference'],
            'productID' => $productname ,
        ];
        
    
        
        $newstring = substr($_POST['tel'], -8);
        $foundAlreadyInLists     =   \App\Models\Lists::all();
        $foundAlreadyInNeworders =  \App\Models\NewOrders::all();
        
        $exist = false;
        foreach($foundAlreadyInLists as $order) {
            if( substr($order->tel, -8) == $newstring ) { $exist = true; }
        }
        foreach($foundAlreadyInNeworders as $order) {
            if( substr($order->tel, -8) == $newstring ) { $exist = true; }
        }
        
        if($exist == true ) {
             $data['duplicated_at'] = \Carbon\Carbon::Now();
        }
        
        \App\Models\NewOrders::create($data);
    });


//   Middlewares
$app->add( new flash($container) );
$app->add( new old($container) );



