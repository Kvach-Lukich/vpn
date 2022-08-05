<?php
//ini_set('display_errors', '1');
//ini_set('display_startup_errors', '1');
//error_reporting(E_ALL);
require_once(__DIR__.'/libs/load.php');
require_once(__DIR__.'/../lara/app/Models/WireGuard.php');
use App\Models\WireGuard;

$sql="SELECT * FROM billings WHERE active_subscription=1 AND trial=1 AND DATE_ADD( last_paid, INTERVAL 24 HOUR )<=NOW()";
$billings=$pdo->query($sql)->fetchAll();
//print_r($billings); die();
foreach($billings as $billing){
   if($billing['balance']<$cnf['NOWPAYMENTS_PRICE']){
      $wg=new  App\Models\WireGuard($cnf['WG_API_URL'], $cnf['WG_KEY']);
      $ok=$wg->Delete_peer($billing['url_safe_public_key']);
      if($ok==''){
         $data['active_subscription']=0;
         $data['trial']=2;
         $set=setsql($data);
         $sql="UPDATE billings SET {$set}, wg_json=NULL, url_safe_public_key=NULL WHERE id={$billing['id']}";
         $pdo->exec($sql);
      }else{
         print_r($billing);
      }
   }
}

$data=[];
$billings=[];

$sql="SELECT * FROM billings WHERE active_subscription=1 AND trial!=1 AND DATE_ADD( last_paid, INTERVAL 720 HOUR )<=NOW()";
$billings=$pdo->query($sql)->fetchAll();

foreach($billings as $billing){
   if($billing['balance']<$cnf['NOWPAYMENTS_PRICE']){
      $wg=new  App\Models\WireGuard($cnf['WG_API_URL'], $cnf['WG_KEY']);
      $ok=$wg->Delete_peer($billing['url_safe_public_key']);
      if($ok==''){
         $data['active_subscription']=0;
         $set=setsql($data);
         $sql="UPDATE billings SET {$set}, wg_json=NULL, url_safe_public_key=NULL WHERE id={$billing['id']}";
         $pdo->exec($sql);
      }else{
         print_r($billing);
      }
   }
}

?>