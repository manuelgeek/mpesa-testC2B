<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\STKTransaction;
use App\FailedTransaction;

class STKController extends Controller
{
    public function index()
    {
    	//STK push
        $response = \STK::push(10, 254726871890, 'f4u239fwyr', 'Test Payment');


    	if($response)
    	{
            $payload = json_decode(json_encode($response),true);

            // $result = json_decode($payload,true);
            
    		return $payload['CheckoutRequestID'];
    	}
    }
//callback

    public function payment()
    {
        $payload =  file_get_contents('php://input');
 
        $result = json_decode($payload,true);

        if($result['Body']['stkCallback']['ResultCode'] == 0)
        {
            $trans_no = $result['Body']['stkCallback']['CallbackMetadata']['Item'][1]['Value'];
            $trans = STKTransaction::where('mpesa_receipt_no',$trans_no)->first();
            if(!$trans)
            {
                $mpesa = new STKTransaction;

                $mpesa->merchant_id = $result['Body']['stkCallback']['MerchantRequestID'];
                $mpesa->checkout_id = $result['Body']['stkCallback']['CheckoutRequestID'];
                $mpesa->result_code = $result['Body']['stkCallback']['ResultCode'];
                $mpesa->phone_no = $result['Body']['stkCallback']['CallbackMetadata']['Item'][4]['Value'];
                $mpesa->amount = $result['Body']['stkCallback']['CallbackMetadata']['Item'][0]['Value'];
                $mpesa->mpesa_receipt_no = $result['Body']['stkCallback']['CallbackMetadata']['Item'][1]['Value'];

                $mpesa->save();

            }
        } else
        {
            $mpesa = new FailedTransaction;

            $mpesa->merchant_id = $result['Body']['stkCallback']['MerchantRequestID'];
            $mpesa->checkout_id = $result['Body']['stkCallback']['CheckoutRequestID'];
            $mpesa->result_code = $result['Body']['stkCallback']['ResultCode'];
            $mpesa->result_desc = $result['Body']['stkCallback']['ResultDesc'];

            $order = Order::where('CheckoutRequestID',$result['Body']['stkCallback']['CheckoutRequestID'])->first();

            $mpesa->amount = $order->amount;
            $mpesa->phone_no = $order->user->phone_no;

            $mpesa->save();

        }

            // return (array) $mpesa;
        
    }

}
