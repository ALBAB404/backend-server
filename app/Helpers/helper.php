<?php
use Twilio\Rest\Client;


function send_ms($msg,$status,$code){

    $res = [
        'status' => $status,
        'message' => $msg,
   ];
    return response()->json($res,$code);
}

if (!function_exists('twilio_env'))
{
    function twilio_env()
    {
        $sid = getenv("TWILIO_ACCOUNT_SID");
        $token = getenv("TWILIO_AUTH_TOKEN");
        $verificationSId = getenv("TWILIO_VERIFICATION_SID");
        $twilio = new Client($sid, $token);

        $verification = $twilio->verify->v2->services($verificationSId);

        return $verification;
    }
}
