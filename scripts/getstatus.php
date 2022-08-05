<?php
//ini_set('display_errors', '1');
//ini_set('display_startup_errors', '1');
//error_reporting(E_ALL);
require_once(__DIR__.'/libs/load.php');
require_once(__DIR__.'/../lara/app/Models/Nowpayments.php');
require_once(__DIR__.'/../lara/app/Models/WireGuard.php');
use App\Models\Nowpayments;
use App\Models\WireGuard;

$sql="SELECT order_id, transactions.* FROM transactions WHERE payment_status NOT IN('finished', 'failed', 'refunded', 'expired', 'partially_paid') AND check_count<=100 ORDER BY np_created_at;\n";
$sql.="UPDATE transactions SET check_count=check_count+1 WHERE payment_status NOT IN('finished', 'failed', 'refunded', 'expired', 'partially_paid') AND check_count<100;";
$transactions=$pdo->query($sql)->fetchAll(PDO::FETCH_UNIQUE);
//print_r($transactions); die();
if($transactions){
   $fst_tr=reset($transactions);
   $np=new App\Models\Nowpayments($cnf['NOWPAYMENTS_TOKEN'], $cnf['NOWPAYMENTS_URL']);
   
   if($np->status()['message']=='OK'){
      $list_transactions=$np->getListPayments(['limit'=>100, 'dateFrom'=>str_replace(' ','T',$fst_tr['created_at']).'Z' ]);
      //print_r($list_transactions);
      foreach($list_transactions['data'] as $tr){
         if( $transactions[ $tr['order_id'] ] ){
            $data=$tr;
            //$data['np_created_at']=substr($tr['created_at'], 0, -1);
            unset($data['created_at'], $data['updated_at'], $data['order_id']);
            $set=setsql($data);
            $sql="UPDATE transactions SET {$set} WHERE order_id='{$tr['order_id']}'";
            //print_r($sql);
            $pdo->exec($sql);
            if($tr['payment_status']=='finished' && $transactions[ $tr['order_id'] ]['status_id']!=1){
               $sql="UPDATE transactions SET status_id=1 WHERE order_id='{$tr['order_id']}'";
               $pdo->exec($sql);
               
               $sql="SELECT * FROM billings WHERE id={$transactions[ $tr['order_id'] ]['user_id']} LIMIT 1";
               $billing=$pdo->query($sql)->fetch();
               $billing['balance']=$billing['balance']+$data['price_amount'];
               //print_r($billing);
               if($billing['balance']>=$cnf['NOWPAYMENTS_PRICE'] && $billing['active_subscription']==0){
                  $newbilling['balance']=$billing['balance']-$cnf['NOWPAYMENTS_PRICE'];
                  $newbilling['active_subscription']=1;

                  $wg=new  App\Models\WireGuard($cnf['WG_API_URL'], $cnf['WG_KEY']);
                  $newbilling['wg_json']=$wg->Add_peer( ['10.66.66.10/32'] );
                  $newbilling['url_safe_public_key']=json_decode($newbilling['wg_json'], true)['url_safe_public_key'];
                  if($newbilling['url_safe_public_key']){
                     $set=setsql($newbilling);
                     $sql="UPDATE billings SET {$set} , last_paid=NOW() WHERE id={$transactions[ $tr['order_id'] ]['user_id']};\n";
                     //print_r($sql);
                     $sql.="UPDATE billings as b
                     JOIN users as u ON b.id=u.parent_id
                     SET b.balance=b.balance+{$cnf['INVITE_PRICE']}
                     WHERE u.id={$transactions[ $tr['order_id'] ]['user_id']} AND u.parent_id!=1";
                     $pdo->exec($sql);
                     $sql='';
                  }
               }else{
                  $sql="UPDATE billings SET balance={$billing['balance']} WHERE id={$transactions[ $tr['order_id'] ]['user_id']}";
                  $pdo->exec($sql);
               }
            }
         }
      }
   }
   
}else{
   unlink('/run/getstatuspid');
   echo 1;
}
