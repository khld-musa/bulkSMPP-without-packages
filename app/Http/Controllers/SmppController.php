<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\SmppClient;
use App\Services\SocketTransport;
use App\Services\GsmEncoder;
// use App\Services\SmppAddress;

class SmppController extends Controller
{
    public function sendSms(Request $request)
    {
        // Construct transport and client
        $transport = new SocketTransport(['10.190.2.253'], 10010);
        $transport->setRecvTimeout(10000);
        $smpp = new SmppClient($transport);

        // Activate binary hex-output of server interaction
        $smpp->debug = true;
        $transport->debug = true;

        // Open the connection
        $transport->open();
        $smpp->bindTransmitter("MobiaddTest", "MobiaddTest");

        // Optional connection specific overrides
        // SmppClient::$sms_null_terminate_octetstrings = false;
        // SmppClient::$csms_method = SmppClient::CSMS_PAYLOAD;
        // SmppClient::$sms_registered_delivery_flag = SMPP::REG_DELIVERY_SMSC_BOTH;

        // Prepare message
        $message = 'H€llo world';
        $encodedMessage = GsmEncoder::utf8_to_gsm0338($message);
        $from = new SmppAddress('SMPP Test', SMPP::TON_ALPHANUMERIC);
        $to = new SmppAddress(4512345678, SMPP::TON_INTERNATIONAL, SMPP::NPI_E164);

        // Send
        $smpp->sendSMS($from, $to, $encodedMessage, $tags);

        // Close connection
        $smpp->close();

        return response()->json(['message' => 'SMS sent successfully']);
    }
}
