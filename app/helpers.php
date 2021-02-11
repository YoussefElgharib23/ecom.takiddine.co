<?php




function Deliver(){
    return  $_SESSION['auth-deliver'] ?? false;
}

function Employee(){
   return  $_SESSION['auth-employee'] ?? false; 
}


function lastWeek(){
    $days = [];
    for($x=0;$x<7;$x++){
      $days[] = date("Y-m-d", strtotime("-$x day"));
    }
    return $days;
}



function last2Weeks(){
    $days = [];
    for($x=0;$x<14;$x++){
      $days[] = date("Y-m-d", strtotime("-$x day"));
    }
    return $days;
}


function lastMonth(){
    $days = [];
    for($x=0;$x<30;$x++){
      $days[] = date("Y-m-d", strtotime("-$x day"));
    }
    return $days;
}






function auth(){
    return $_SESSION['auth-logged'];
}

function getQuantityFromList($list){
          $ok = [];
          foreach($list as $item){
                foreach($item['products'] as $product){    
                    $ok[] = $product['quanity'];
                }
          }
          
          return array_sum($ok);
}



function validateSortieItem($productID,$CityID,$quantity){
    
    $stock = \App\Models\Stock::where('CityID',$CityID)->where('ProduitID',$productID)->first();
    
    if(!$stock){
        $data = [
            'CityID'=> $CityID,
            'ProduitID'=> $productID,
            'Recue'=>$quantity,
            'StockPhisique'=>$quantity,
        ];
        \App\Models\Stock::Create($data);
    }
    
    if($stock){
       $stock->Recue =  $stock->Recue + $quantity;
       $stock->StockPhisique =  $stock->StockPhisique + $quantity;
       $stock->save();  
    }
    
}


function countProucts($array_in){

      $hash = array();
      $array_out = array();
      $total  = 0;
      foreach($array_in as $item) {
          $hash_key = $item['product'];
          if(!array_key_exists($hash_key, $hash)) {
              $hash[$hash_key] = sizeof($array_out);
              array_push($array_out, array(
                  'product'    => $item['product'],
                  'client'     => 0,
                  'quantity'   => 0,
              ));
          }
          $array_out[$hash[$hash_key]]['client'] += 1;
          $array_out[$hash[$hash_key]]['quantity'] += $item['quantity'];
      }

      $total = array_sum(array_column($array_out, 'quantity'));
      $data = [];
      foreach($array_out as $product) {
        $percent = (($product['quantity'] / $total) * 100);
        $l = [
          'product'  => $product ['product'],
          'client'   => $product ['client'],
          'quantity' => $product ['quantity'],
          'percent'  => number_format((float)$percent, 2, '.', '').'%',
          'color'    => getColor($percent),
        ];
        array_push($data, $l);
      }
      return $data;
}



  /*
  *    Debug
  */
  function st($string){
      if($string){
          echo '<pre>';
          print_r($string);
          echo '</pre>';
      }
      return ' ';
  }
  



  function getColor($number){
    if($number < 20 ) {
      return 'danger';
    }
    if($number < 50 and $number > 20 ){
      return 'primary';
    }
    if($number < 70 and $number > 50 ){
      return 'info';
    }
    if($number > 70 ){
      return 'success';
    }
  }


  function countCities($array_in){
    $hash = array();
    $array_out = array();
    $total = count($array_in);
    foreach($array_in as $item) {
        $hash_key = $item['cityName'];
        if(!array_key_exists($hash_key, $hash)) {
            $hash[$hash_key] = sizeof($array_out);
            array_push($array_out, array(
                'city' => $item['cityName'],
                'count' => 0,
            ));
        }
        $array_out[$hash[$hash_key]]['count'] += 1;
    }
    $data = [];
    foreach($array_out as $cities) {
      $percent = (($cities['count'] / $total) * 100);
      $l = [
        'city'    => $cities['city'],
        'count'   => $cities['count'],
        'percent' => number_format((float)$percent, 2, '.', '').'%',
        'color'   => getColor($percent),
      ];
      array_push($data, $l);
    }
    return $data;
}

  /*
  *    Debug
  */
  function sv($string){
      st($string);
      exit;
  }

  function dd($string){
    return sv($string);
  }




  /*
  * Clean POST data
  */
  function clean($post) {
    $clean = [];
    if(is_array($post)){
      
      foreach ($post as $key => $value):
        $clean[$key] = clean($value);
      endforeach;
      return $clean;
    }else{
      return safe($post);
    }
  }

   
    
    
    
    /*
    *    Clean the Inputs
    */
    function safe($data) {
        // Strip HTML Tags
        $clear = strip_tags($data);
        // Clean up things like &amp;
        $clear = html_entity_decode($clear);
        // Strip out any url-encoded stuff
        $clear = urldecode($clear);
        // Replace Multiple spaces with single space
        $clear = preg_replace('/ +/', ' ', $clear);
        // Trim the string of leading/trailing space
        $clear = trim($clear);
        return $clear;
    }
    

  