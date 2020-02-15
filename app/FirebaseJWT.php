<?php

namespace app;

/**
* class MSG91 to send SMS on Mobile Numbers.
* @author Shantanu Kulkarni
*/


use Illuminate\Http\Request;
use Firebase\JWT\JWT;


class FirebaseJWT
{


public static function create_custom_token() {
    $uid = 1;
    global $service_account_email, $private_key;
    $private_key =   "-----BEGIN PRIVATE KEY-----\nMIIEvAIBADANBgkqhkiG9w0BAQEFAASCBKYwggSiAgEAAoIBAQC8AMDyPolkDx3A\nFqHVQvbAUTkYvbakjfDtinfvnVft0Jop/RDTlx0CW5IwRcPPQS4NffkUpPH/SUD4\nLbR6ZflAjEv+veSTGclbXn8PsoT1xQz8zmlDflSxrOMgBn4IL8C8VaFN1kaYvas/\nUIExs79asu+0QRICB1LOmzEqEGMUzDWrrLzFRVUdO/ClaT4ERysFVd6b3lwJM8kU\n3Kb7MnlBjHKhMiWXk7dvTUds0hPDiI14QnFap99Fd8O005pMEVmdd7Z8L8iT8qzJ\nhQH85KKHy7QBYWSE+xP32ynunnQ30OpwOh+ZNGh+hhy/rs0W9/KuDB3wNvlTk1FW\nWyXFGSK1AgMBAAECggEACOKa5oKrc53TRh92XoyKT48x0kgvuESNEmzs5iza2akg\n4Bh5vHDz31ynOBN+rEOPAvblLWNDnKZq+k+5Voo4kXhlTxDNG2FTRuIxwcgqHdjG\nQwcY/9DvUnEH+vIo8Adxux+WPrDV0qTZfGXi9Tpi3M0qzwC+8nbuJEg+tpeNAeU1\nPAzZ7+SmA0o3Cd8Ar2XXLOmOygh7ftwNAPHIucAqjj3m4CMHL4A+fz0xT0geTOZi\n8AZyGaw8El3x2qBycgf5ZSX5gZn1/9bJiU58YiUjVUdlmaoa9yccMTVGqMlVTNvr\nz55iR/ijxREF6uS/P5WmhXgRpMqKksz449O23URRsQKBgQD7OYfkWFq6YsjQ0SXl\niWbQCQD8RvAAz/fnhVx50swMovMfVNwnvW+mB8t8NbL/Bxx/6hnl6vak3bYjT5uq\nnHQOZ9hJM2XxlRGPRfbwuvHpKw/+XRuS4wbEhDMF2v2H+XSOK1KiT8wU9TFitT+A\nYYxUAfIqMvWRdFaJZVRKwV+TrwKBgQC/k5VO1NgLB5KPWMcoH4fCrc6SeAkejnUy\nllK+xgaiFsq2wKmUuwAygfsxAMipbMyahnCpZssXef5UazMb5IgurzpfUtxjBjKp\nWlZiwVOUdxD9E0VTdQpnq4SqrwO2SJ5tKwuy+ySSwwLuLGCTbmA0go+4tb8Wwyxi\n7hyQN1302wKBgCktdt6wtL5ULsI7ZdDHwrhaoXS4U+JuNB4Yt92n9ZYkp2D9UdCZ\ntlEqeen0C1DN45f70R7kcmT3ikEjbUp7tnIeB0+IQy9j1ar8NsuieMBVNJvmuvxK\nhfN0D8Dn8iJ1eutKopLELZlJzzx0pOHwG0Yxw3WzQJkEzqJan4RZScc1AoGAKvTF\nqtpw4vA8vaWzaly/jTh5tD/0E4Tv5HrubNZsUFX5+EZ0/+N6ZNjeYRuC4vKBYji4\n9FMXQNla3MT4vc1dd9JUrWEgB5gxLeYSrqwYuJaGGakDh3Yb8ij0Y21A0NOxqlrX\nkbdceQf/FTagJQ6/xaZs4YbnJQx4XGp1vySbDbcCgYBMR5jCZrPe7T4WxxjrfF5Z\nnYv52pPvJfEZ8LTftINSG2JOzXelDICMUJP3pmA1Xu2lLJxTcmwv4UwILK5zJSj/\nGYL1c+ThMVA5yi4q0y2Ih5Gzdt1952nekyYQBS69PiUF2Cf9/OUpGgptZiGhdVOC\n0+gt0AjQ/mSVSBQpq9dyJA==\n-----END PRIVATE KEY-----\n";
    $service_account_email = 'firebase-adminsdk-js1z9@doodlepadfirebaseindia.iam.gserviceaccount.com';
  
    $now_seconds = time();
    $payload = array(
      "iss" => $private_key,
      "sub" => $service_account_email,
      "aud" => "https://identitytoolkit.googleapis.com/google.identity.identitytoolkit.v1.IdentityToolkit",
      "iat" => $now_seconds,
      "exp" => $now_seconds+(60*60),  // Maximum expiration time is one hour
      "uid" => $uid,
      "name"=>"heyshantu",
   
    );
    return JWT::encode($payload,config('constants.PRIVATE_KEY'), "HS256");
  }



}
