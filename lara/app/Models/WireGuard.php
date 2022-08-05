<?php

namespace App\Models;
class WireGuard{
   
   public $url, $key, $raw=true, $wg='wg0';
   
   function __construct($url=null, $key=null) {
      if(!$url){
         $this->url=env('WG_API_URL');
      }else{
         $this->url=$url;
      }
      if(!$key){
         $this->key=env('WG_KEY');
      }else{
         $this->key=$key;
      }
      if(function_exists('env') && env('WG_INTERFACE')){
         $this->wg=env('WG_INTERFACE');
      }
   }
   
   public function request($path='', $type='get', $data=[], $raw=false){
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, $this->url.$path);
      curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json', 'Authorization: Bearer '.$this->key]);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      if($type!='get' && $data){
         $json_data=json_encode($data, JSON_UNESCAPED_UNICODE);
         curl_setopt($ch, CURLOPT_POSTFIELDS, $json_data);
      }
      
      if($type=='post'){
         curl_setopt($ch, CURLOPT_POST, true);
      }else{
         curl_setopt($ch, CURLOPT_CUSTOMREQUEST, strtoupper($type));
      }
      
      $response = curl_exec($ch);
      return($response);
      if($this->raw==false && $raw==false){
         return $response;
      }else{
         return json_decode($response, true);
      }
   }
   
   public function Get_peers(){
      return $this->request("devices/{$this->wg}/peers/");
   }

   public function Add_peer($allowed_ips){
      $preshared_key=base64_encode(random_bytes(32));
      return $this->request("devices/{$this->wg}/peers/", 'post', [ 'allowed_ips'=>$allowed_ips, 'preshared_key'=>$preshared_key ]);
   }
   
   public function Delete_peer($id){
      return $this->request("devices/{$this->wg}/peers/{$id}/", 'delete');
   }
   
   public function Get_devices(){
      return $this->request("devices/");
   }
   
   public function Get_config($id){
      return $this->request("devices/{$this->wg}/peers/{$id}/quick.conf");
   }
   
   public function Get_qrconfig($id, $width=200){
      return $this->request("devices/{$this->wg}/peers/{$id}/quick.conf.png?width={$width}");
   }
   
}