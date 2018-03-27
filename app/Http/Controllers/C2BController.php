<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Transaction;
// use SMSProvider;

class C2BController extends Controller
{
    public function index()
    {
    	$response = \Registrar::register(603022)
                    ->onConfirmation('https://4ddb5895.ngrok.io/api/confirm')
                    ->onValidation('https://4ddb5895.ngrok.io/api/validate')
                    ->submit();

        if($response)
        {
            $payload = json_decode(json_encode($response),true);
            return $payload;
        }

    }

    //c2b

    //validate
    public function validatation()
    {
    	$payload =  file_get_contents('php://input');
 
        $result = json_decode($payload,true);

        if(intval($result['TransAmount']) != 100)
        {
        	return response()->json([
								    'ResultCode' => 'C2B00013',
								    'ResultDesc' => 'Invalid Amount',
								    'ThirdPartyTransID' => 0
								]);
        	//echo ‘{“ResultCode”:0, “ResultDesc”:”. $resultdesc.”, “ThirdPartyTransID”: 0}’;
        }

//uncomment if necessary
        // if($result['ThirdPartyTransID'] != 0)
        // {
        // 	return response()->json([
								//     'ResultCode' => 'C2B00012',
								//     'ResultDesc' => 'Invalid Account number',
								//     'ThirdPartyTransID' => 0
								// ]);
        // 	// echo ‘{“ResultCode”:0, “ResultDesc”:”. $resultdesc.”, “ThirdPartyTransID”: 0}’;
        // }

  //       result_code       result_description
		// C2B00011          Invalid MSISDN
		// C2B00012          Invalid Account number
		// C2B00013          Invalid Amount
		// C2B00014          Invalid KYC details
		// C2B00015          Invalid Shortcode
		// C2B00016          Other Error

        return response()->json([
								    'ResultCode' => 0,
								    'ResultDesc' => 'successful validation',
								    'ThirdPartyTransID' => 0
								]);
    }
//c2b
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
                $mpesa->FirstName = $result['FirstName'];
                $mpesa->MiddleName = $result['MiddleName'];
                $mpesa->LastName = $result['LastName'];


                $mpesa->save();
            }

             return response()->json([
								    'ResultCode' => 0,
								    'ResultDesc' => 'Confirmation received successfully'
								]);

    		//echo ‘{“ResultCode”:0,”ResultDesc”:”Confirmation received successfully”}’;
    }

    //https://www.franktekmicrosystems.com/mpesa-api-documentation-paybill-lipa-na-mpesa.html

    public function simulate()
    {
        $response = \Simulate::request(10)
                                        ->from(254722000000)
                                        ->usingReference('f4u239fweu')
                                        ->setCommand('CustomerPayBillOnline')
                                        ->push();

        // $response = \Simulate::push(10, 254722000000, 'f4u239fweu', Simulate::CUSTOMER_PAYBILL_ONLINE);
        if($response)
        {
            $payload = json_decode(json_encode($response),true);
            return $payload;
        }
    }
}
