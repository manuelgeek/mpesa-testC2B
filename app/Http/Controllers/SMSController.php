<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SMSController extends Controller
{
    // sms by afrikas talking
    return $response = \SMS::send('+254704073851', 'Hi me there','GEEK');
    
}
