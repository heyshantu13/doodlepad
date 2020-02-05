<?php

/*  Author:  Shantanu K
    Email: heyshantu13@gmail.com
*/


namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\User;
use App\UserProfile;
use App\Http\Requests\CreateUserValidate;
use App\MSG91;
use App\Jobs\SendOtpJob;


class AuthController extends Controller
{

    private $createUser;


    public function checkmobile(Request $request){

         

        if ($request->isMethod('post')) {

             $request->validate([
             'mobile' => 'required|string|min:10|max:10|unique:users',
        ]);      
              $sendOTP = new MSG91();
              $isOTPSend = $sendOTP->sendOTP($request->mobile);

              if($isOTPSend->type == 'success'){
               return response()->json([
                    'status'=>true,
            'message' => 'Otp Sent Successfully.',
        ], 201);
            };

    
        }

    }

      public function verifyOTP(Request $request)
    {

       $request->validate([
             'mobile' => 'required|string|min:10|max:10|unique:users',
             'otp' => 'required|min:4'
        ]);

       $validateOTP = new MSG91();
       $isOTPVerified = $validateOTP->verifyOTP($request->mobile,$request->otp);

         if($isOTPVerified->type == 'success'){
               return response()->json([
                    'status'=>true,
            'message' => 'Otp Verified Successfully.',
        ], 201);
            }
            else{
                 return response()->json([
                    'status'=>false,
            'message' => 'Incorrect OTP.',
        ], 406);
            }

    



     
     }

    

    public function signup(CreateUserValidate $request)
    {
       
        if($request->mobile != NULL){
          
                $createUser = new User([
                'fullname' => $request->fullname,
                'username'=> $request->username,
                'mobile'=> $request->mobile,
                'password'=>bcrypt($request->password),
            ]);
           
                 $createUser->save();
                  $tokenResult = $createUser->createToken('Doodlepad Token');
        $token = $tokenResult->token;  

                
                      return response()->json([
                    'status'=>true,
            'message' => 'Account Successfully Created.',
             'access_token' => $tokenResult->accessToken,
            'token_type' => 'Bearer',
            'expires_at' => Carbon::parse(
                $tokenResult->token->expires_at
            )->getPreciseTimestamp(3)
        ], 201);
                 }

                

            
      
    }


    public function createProfile(Request $request){

          if ($request->isMethod('post')) {

             $request->validate([
             'gender' => 'required|string|max:7',
             'bio' => 'required|min:1|max:140',
             'date_of_birth' => 'required',
             'fcm_registration_id'=> 'required|unique:user_profiles',
             'profile_picture_url' => 'required'
        ]);



             $profile = UserProfile::where('user_id', Auth::user()->id)->first();

       if (!$profile) {
       
           $profile = new UserProfile([
            'gender' => request()->gender,
            'bio' => request()->bio,
            'date_of_birth'=>request()->date_of_birth,
            'fcm_registration_id'=> request()->fcm_registration_id,
            'profile_picture_url' => request()->profile_picture_url,
           ]);

           $profile->user_id = Auth::user()->id;
           $profile->save();

           return response()->json([
                    'status'=>true,
            'message' => 'Profile Created Successfully.',
        ], 201);
        }
        else{
            return response()->json([
                    'status'=>false,
            'message' => 'Something Went Wrong!',
        ], 201);
        }

      


}

     

     
    }

  
  
    /**
     * Login user and create token
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
            'username' => 'required|string|',
            'password' => 'required|string|min:8|max:14'
            
        ]);        
        $credentials = request(['username','password']);        
        if(!Auth::attempt($credentials))
            return response()->json([
                 'status'=>false,
                'message' => 'username or password is invalid'
            ], 401);      

            $user = $request->user();  

            $tokenResult = $user->createToken('Doodlepad Access Token');
        $token = $tokenResult->token;  

         if ($request->remember_me)
            $token->expires_at = Carbon::now()->addWeeks(1);        
        $token->save();       
         return response()->json([
            'status'=>true,
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

    public function resetPassword(Request $request){

          $request->validate([
             'mobile' => 'required|string|min:10|max:10',
        ]);      

         $profile = User::where('mobile', $request->mobile)->first();

         if(!$profile)
         {
            return response()->json([
                 'status'=>false,
                'message' => 'Cant find account'
            ], 401);      
         }
         else{
            $sendOTP = new MSG91();
              $isOTPSend = $sendOTP->sendOTP($request->mobile);

              if($isOTPSend->type == 'success'){
               return response()->json([
                    'status'=>true,
            'message' => 'Otp Sent Successfully.',
        ], 201);
         }
     }
     


      }

public function newPassword(Request $request){

     $request->validate([
             'mobile' => 'required|string|min:10|max:10',
             'otp' => 'required|max:4',
             'password'=> 'required|string|min:8|max:14'

        ]);     

     return response()->json([
                    'status'=>true,
            'message' => 'Password Changed Successfully.',
        ], 201);
}



 

}