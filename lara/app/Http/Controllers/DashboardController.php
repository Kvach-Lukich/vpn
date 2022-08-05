<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Models\Billing;
use App\Models\WireGuard;
use Carbon\Carbon;
use App\Models\Nowpayments;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
   public function show(Request $request){
      $np=new Nowpayments();
      
      $billing=Billing::where('id',Auth::id())->first();
      if(!$billing){
         $billing = Billing::create([
            'id'=>Auth::id(),
            'balance'=>0,
            'active_subscription'=>0
         ]);
         $billing->save();
      }
      
      if($request->input('trial')){
         $wg=new WireGuard();
         $billing->wg_json=$wg->Add_peer( ['10.66.66.10/32'] );
         $billing->url_safe_public_key=json_decode($billing->wg_json, true)['url_safe_public_key'];
         $billing->trial=1;
         $billing->last_paid=Carbon::now();
         $billing->active_subscription=1;
         $billing->save();
      }
      
      $data['balance']=$billing->balance;
      $data['trial']=$billing->trial;
      $data['invite_token']=Auth::user()->invite_token;
      $data['active_subscription']=$billing->active_subscription;

      if($data['active_subscription']){
         $data['remains']=Carbon::now()->diffInDays(Carbon::parse($billing->last_paid)->addDays(30), false);
         if($data['trial']==1){
            $data['remains']=1;
         }
         if($data['remains']<0) $data['remains']=0;
      }else{
         $data['remains']=0;
      }
      
      $data['currencies']=$np->getCurrencies()['currencies'];
      $data['cur_currency']=$request->input('currency','btc');
      $data['price']=env('NOWPAYMENTS_PRICE');
      //$data['EstimatePrice']
      $data['eur1']=$np->getEstimatePrice(['amount'=>1, 'currency_from'=>'eur', 'currency_to'=>$data['cur_currency']])['estimated_amount'] ?? 0;
      $data['minpay']=$np->getMinimumPaymentAmount([ 'currency_from'=>$data['cur_currency'] ])['min_amount']/$data['eur1'];
      $data['minpay']=round($data['minpay'],2);
      
      
      $data['pay']=$request->input('pay',$data['price']);
      $data['EstimatePrice']=$data['eur1']*$data['price'];
      
      if( $request->session()->get('transaction') ){
         $data['pol_transaction']=1;
      }else{
         $data['pol_transaction']=null;
      }
      //$request->session()->forget('transaction');
      //print_r($data);
      return view('dashboard', compact('data'));
   }
   
   public function config(){
      $wg=new WireGuard();
      $billing=Billing::where('id',Auth::id())->first();
      if($billing->active_subscription){
         return response($wg->Get_config($billing->url_safe_public_key))
            ->withHeaders([
                'Content-Type' => 'text/plain',
                'Cache-Control' => 'no-store, no-cache',
                'Content-Disposition' => 'attachment; filename="vpn.conf"',
            ]);
      }
   }
   
   public function qrconfig(){
      $wg=new WireGuard();
      $billing=Billing::where('id',Auth::id())->first();
      if($billing->active_subscription){
         return response($wg->Get_qrconfig($billing->url_safe_public_key))
            ->withHeaders([
                'Content-Type' => 'image/png',
                'Cache-Control' => 'no-store, no-cache',
            ]);
      }
   }
   
   public function charge(Request $request){
      $np=new Nowpayments();
      $pay['order_id']=Auth::id().'-'.Carbon::now()->timestamp;
      $pay['price_amount']=$request->input('pay');
      $pay['price_currency']='eur';
      $pay['pay_currency']=$request->input('currency');
      $pay['ipn_callback_url']=env('NOWPAYMENTS_BACK_URL');
      $pay['order_description']='vpn';
      $pay['success_url']=env('NOWPAYMENTS_SUCCESS_URL');
      $invoice=$np->createInvoice($pay);
      
      //DB::unprepared('LOCK TABLES transactions WRITE');
      Transaction::create([
         'order_id'=>$invoice['order_id'],
         'user_id'=>Auth::id(),
         'price_amount'=>$invoice['price_amount'],
         'pay_currency'=>$invoice['pay_currency'],
         'payment_status'=>'Created',
         'status_id'=>0,
         'order_description'=>'vpn',
         'np_created_at'=>substr( $invoice['created_at'], 0,-1),
      ])->save();
      //DB::unprepared('UNLOCK TABLES');
      
      exec(base_path().'/../scripts/getstatus.sh>/dev/null 2>/dev/null &');
      $request->session()->put('transaction', $invoice['order_id']);
      
      print_r($invoice);
      echo "<br/><a target=_blank href='{$invoice['invoice_url']}'>{$invoice['invoice_url']}</a>";
      echo "<script> window.open('{$invoice['invoice_url']}', '_blank').focus(); </script>";
   }
   
   public function transactionstatus(Request $request){
      if( $request->session()->get('transaction') ){
         $transaction=Transaction::where('order_id',$request->session()->get('transaction'))->first();
         if($transaction->payment_status=='finished'){
            $request->session()->forget('transaction');
         }
         return $transaction->payment_status;
      }
   }
   
   public function seeall(Request $request){
      //echo base_path();
      //echo "\n<br />\n";
      $wg=new WireGuard();
      //$wg->raw=true;
      //return response($wg->Add_peer( ['10.66.66.10/32'] ))->withHeaders(['Content-Type'=>'application/json']);
      //return response($wg->Get_peers())->withHeaders(['Content-Type'=>'application/json']);
      //return response($wg->Delete_peer("iyi7c2aOjzMkCBrlxtFQ7CH9fccwJemQfwzlaWSMKSo="))->withHeaders(['Content-Type'=>'application/json']);
      //return response($wg->Get_devices())->withHeaders(['Content-Type'=>'application/json']);
//      return $wg->Get_config("iyi7c2aOjzMkCBrlxtFQ7CH9fccwJemQfwzlaWSMKSo=");
      return response($wg->Get_config("iyi7c2aOjzMkCBrlxtFQ7CH9fccwJemQfwzlaWSMKSo="))
                ->withHeaders([
                    'Content-Type' => 'text/plain',
                    'Cache-Control' => 'no-store, no-cache',
                    'Content-Disposition' => 'attachment; filename="vpn.conf"',
                ]);
      //
      //print_r($request->all());
      //print_r(file_get_contents('php://input'));
   }
}
