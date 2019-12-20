<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\User;
use App\Http\Requests\CreateUserValidate;
use App\MSG91;
use App\Jobs\SendOtpJob;


class AuthController extends Controller
{

    private $createUser;
    

    public function signup(CreateUserValidate $request)
    {
        $sendOTP = new MSG91();

        if($request->mobile != NULL){

            $isOTPSend = $sendOTP->sendOTP($request->mobile);

            if($isOTPSend->type == 'success'){
                $createUser = new User([
                'fullname' => $request->fullname,
                'username'=> $request->username,
                'mobile'=> $request->mobile,
                'password'=>bcrypt($request->password)
            ]);
                 $createUser->save();

                  return response()->json([
                    'status'=>true,
            'message' => 'Account Successfully Created.',
        ], 201);

            }
        }

        if($request->email != NULL){

                $otp = rand(1111,9999);

             $createUser = new User([
                'fullname' => $request->fullname,
                'username'=> $request->username,
                'mobile'=> $request->mobile,
                'password'=>bcrypt($request->password)
            ]);
                $createUser->otp = $otp;
                 $createUser->save();
                 SendOtpJob::dispatch()->delay(now()->addSeconds(2));

                  return response()->json([
                    'status'=>true,
            'message' => 'Account Successfully Created.',
        ], 201);

        }



    }

    public function verifyOTP(CreateUserValidate $request)
    {

    }
  
    /**
     * Login user and create token
     *
     * @param  [string] email
     * @param  [string] password
     * @param  [boolean] remember_me
     * @return [string] access_token
     * @return [string] token_type
     * @return [string] expires_at
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
            'remember_me' => 'boolean'
        ]);        $credentials = request(['email', 'password']);        if(!Auth::attempt($credentials))
            return response()->json([
                'message' => 'Unauthorized'
            ], 401);        
            $user = $request->user();        
            $tokenResult = $user->createToken('Personal Access Token');
        $token = $tokenResult->token;       
         if ($request->remember_me)
            $token->expires_at = Carbon::now()->addWeeks(1);        
        $token->save();       
         return response()->json([
            'access_token' => $tokenResult->accessToken,
            'token_type' => 'Bearer',
            'expires_at' => Carbon::parse(
                $tokenResult->token->expires_at
            )->toDateTimeString()
        ]);
    }
  
    /**
     * Logout user (Revoke the token)
     *
     * @return [string] message
     */
    public function logout(Request $request)
    {
        $request->user()->token()->revoke();        return response()->json([
            'message' => 'Successfully logged out'
        ]);
    }
  
    /**
     * Get the authenticated User
     *
     * @return [json] user object
     */
    public function user(Request $request)
    {
        return response()->json($request->user());
    }
}