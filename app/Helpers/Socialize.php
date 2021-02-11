<?php 

namespace App\Helpers;


defined('BASEPATH') OR exit('No direct script access allowed');


class Socialize { 
 

    protected $list;
    protected $message;

    public function __construct($list){
        $this->message($list);
        return $this;
    }

    public function deliver($id){
      return $this;
    }

    public function telegram(){
        Longman\TelegramBot\Request::sendMessage([
          'chat_id' => '461362983',
          'text'    => $this->message,
        ]);
    }

    public function whatsupp(){
        $data = [
            'phone' => '+33611607210', 
            'body' => $this->message,
        ];

        $url = 'https://eu106.chat-api.com/instance103645/message?token=ibcecxwwgfppng9m';
        $options = stream_context_create(['http' => [
                'method'  => 'POST',
                'header'  => 'Content-type: application/json',
                'content' => json_encode($data)
            ]
        ]);
        $result = file_get_contents($url, false, $options);
    }
  
    public function message(){
        $message  = "------------------------------\n";
        $message .= "Nom : ".$list['name'] ."\n";
        $message .= "Tel : ".$list['tel'] ."\n";
        $message .= "ville : ".$list['city'] ."\n";
        $message .= "adress : ".$list['adress'] ."\n";
        $message .= "articles :"."\n";
        foreach ($list['products'] as $product) {
          $message .= $product['0'] . ' X ' . $product['1']. ' = ' . $product['2'] .'DH'."\n";
        }
        $message .= "------------------------------";
        $this->message = $message;
        return $this;
    }
}




