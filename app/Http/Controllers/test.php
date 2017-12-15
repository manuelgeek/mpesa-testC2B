<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Transaction;

class test extends Controller
{
    public function index()
    {
    	$response = \STK::push(10, 254724540039, 'f4u239fwyr', 'Test Payment');

    	$response = \Registrar::register(734328)
			        ->onConfirmation('http://8794364f.ngrok.io/api/confirm')
			        ->onValidation('http://8794364f.ngrok.io/api/validate')
			        ->submit();


    	if($response)
    	{
    		return redirect()->back();
    	}
    }

    //validate
    public function validatation()
    {
    	$payload =  file_get_contents('php://input');
 
        $result = json_decode($payload,true);

        if($result['TransAmount'] != 100)
        {
        	return response()->json([
								    'ResultCode' => 0,
								    'ResultDesc' => 'error',
								    'ThirdPartyTransID' => 0
								]);
        	//echo ‘{“ResultCode”:0, “ResultDesc”:”. $resultdesc.”, “ThirdPartyTransID”: 0}’;
        }

        // if($result['ThirdPartyTransID'] != 0)
        // {
        // 	return response()->json([
								//     'ResultCode' => 0,
								//     'ResultDesc' => 'error',
								//     'ThirdPartyTransID' => 0
								// ]);
        // 	// echo ‘{“ResultCode”:0, “ResultDesc”:”. $resultdesc.”, “ThirdPartyTransID”: 0}’;
        // }

        return response()->json([
								    'ResultCode' => 0,
								    'ResultDesc' => 'success',
								    'ThirdPartyTransID' => 0
								]);
    }

    //confirm
     public function confirm()
    {

    	$payload =  file_get_contents('php://input');
 
        $result = json_decode($payload,true);

        $trans_no = $result['TransID'];
            $trans = Transaction::where('TransID',$trans_no)->first();
            if(!$trans)
            {
                $mpesa = new Transaction;

                $mpesa->TransID = $result['TransID'];
                $mpesa->TransAmount = $result['TransAmount'];
                $mpesa->BillRefNumber = $result['BillRefNumber'];
                $mpesa->InvoiceNumber = $result['InvoiceNumber'];
                $mpesa->OrgAccountBalance = $result['OrgAccountBalance'];
                $mpesa->ThirdPartyTransID = $result['ThirdPartyTransID'];
                $mpesa->MSISDN = $result['MSISDN'];
                $mpesa->InvoiceNumber = $result['FirstName'];
                $mpesa->InvoiceNumber = $result['MiddleName'];
                $mpesa->InvoiceNumber = $result['LastName'];


                $mpesa->save();
            }

             return response()->json([
								    'ResultCode' => 0,
								    'ResultDesc' => 'Confirmation received successfully'
								]);

    		//echo ‘{“ResultCode”:0,”ResultDesc”:”Confirmation received successfully”}’;
    }

    //https://www.franktekmicrosystems.com/mpesa-api-documentation-paybill-lipa-na-mpesa.html

}
