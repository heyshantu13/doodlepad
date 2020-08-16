<?php

namespace app;

/**
* class MSG91 to send SMS on Mobile Numbers.
* @author Shantanu Kulkarni
*/


class MSG91 {

    protected $mobile;
    protected $otp;

    public function __construct(){

    }

    
 public static function sendOTP($mobile,$countrycode=91){

$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => "https://api.msg91.com/api/v5/otp?invisible=1&userip=IPV4 User IP&authkey=308630AjqDL3wsLHmS5df877fb&email=Email ID&mobile=".$mobile."&template_id=5df87792d6fc05218a127fad&otp_expiry=10&country=".$countrycode,
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => "",
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 30,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => "POST",
  CURLOPT_POSTFIELDS => "",
  CURLOPT_SSL_VERIFYHOST => 0,
  CURLOPT_SSL_VERIFYPEER => 0,
  CURLOPT_HTTPHEADER => array(
    "content-type: application/json"
  ),
));

$response = curl_exec($curl);
$err = curl_error($curl);

curl_close($curl);

return json_decode($response);

    }


public static function verifyOTP($mobile,$otp,$countrycode = 91){

    $curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => "https://api.msg91.com/api/v5/otp/verify?otp=".$otp."&authkey=308630AjqDL3wsLHmS5df877fb&mobile=".$countrycode.$mobile,
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => "",
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 30,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => "POST",
  CURLOPT_POSTFIELDS => "",
  CURLOPT_SSL_VERIFYHOST => 0,
  CURLOPT_SSL_VERIFYPEER => 0,
));

$response = curl_exec($curl);
$err = curl_error($curl);

curl_close($curl);


  return json_decode($response);

}


 }

?>
