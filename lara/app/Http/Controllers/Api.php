<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PushNowpayment;

class Api extends Controller
{
    public function from_nowpayments(Request $request){
        $log=new PushNowpayment();
        $request_json = file_get_contents('php://input');
        $log->json=$request_json;
        $log->save();
        
        $data=$this->check_ipn_request_is_valid();
        if(!$data[1]){
            $log->json=$data[0];
        }else{
            $log->err=$data[1];
            echo $data[1];
        }
        $log->save();
    }
    
    public function check_ipn_request_is_valid(){
        $error_msg = null;//"Unknown error";
        $auth_ok = false;
        $request_data = null;
        $request_json = null;
        if (isset($_SERVER['HTTP_X_NOWPAYMENTS_SIG']) && !empty($_SERVER['HTTP_X_NOWPAYMENTS_SIG'])) {
            $recived_hmac = $_SERVER['HTTP_X_NOWPAYMENTS_SIG'];
            $request_json = file_get_contents('php://input');
            $request_data = json_decode($request_json, true);
            ksort($request_data);
            $sorted_request_json = json_encode($request_data, JSON_UNESCAPED_SLASHES);
            if ($request_json !== false && !empty($request_json)) {
                $hmac = hash_hmac("sha512", $sorted_request_json, trim($this->ipn_secret));
                if ($hmac == $recived_hmac) {
                    $auth_ok = true;
                } else {
                    $error_msg = 'HMAC signature does not match';
                }
            } else {
                $error_msg = 'Error reading POST data';
            }
        } else {
            $error_msg = 'No HMAC signature sent.';
        }
        return([$request_json,$error_msg]);
    }
}
