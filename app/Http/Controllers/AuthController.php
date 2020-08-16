<?php

/*  Author:  Shantanu K
    Git: heyshantu13
    Description:  Authentication Management Controller
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
use Kreait\Firebase\Factory;
use Kreait\Firebase\Auth as FirebaseAuth;
use Kreait\Firebase\Exception\Auth\AuthError;
use Illuminate\Support\Facades\Storage;
use App\AppConfig;


class AuthController extends Controller
{

    private $createUser;
    public $auth;
    private $otp;
    private $firebase;
//     private $defaulturl = "";

   public function __construct(){
    $this->auth = (new Factory)
    ->withServiceAccount(base_path('doodlepadfirebaseindia-3f2e8d93da3a.json'))
    ->createAuth();
    $this->otp = new MSG91();
     $this->firebase = (new Factory())
    ->withServiceAccount(base_path('doodlepadfirebaseindia-3f2e8d93da3a.json'))
    ->createDatabase();
   }



    public function checkmobile(Request $request){
        if ($request->isMethod('post')) {
             $request->validate([
             'mobile' => 'required|string|min:10|max:10|unique:users',
        ]);      
             
              $isOTPSend =  $this->otp->sendOTP($request->mobile,$request->country_code);
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
             'otp' => 'required|min:4|max:4'
        ]);

       $validateOTP = new MSG91();
       $isOTPVerified = $this->otp->verifyOTP($request->mobile,$request->otp);

         if($isOTPVerified->type == 'success'){
               return response()->json([
                    'status'=>true,
                    'message' => 'Otp Verified Successfully.',
            ], 201);
            }
            
            else{
                return response()->json([
                    'status'=>false,
            'message' => 'Incorret Otp',
            ], 201);

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
                $tokenResult = $createUser->createToken('Doodlepad Access Token');
                $token = $tokenResult->token;  
                return response()->json([
                'status'=>true,
                'message' => 'Account Successfully Created.',
                'access_token' => $tokenResult->accessToken,
                'expires_at' => Carbon::parse(
                $tokenResult->token->expires_at
                )->getPreciseTimestamp(3)
        ], 200);
                 }
    }




    public function createProfile(Request $request)
    if ($request->isMethod('post')) {
             $request->validate([
             'gender' => 'required|string|max:7',
             'bio' => 'max:140|string',
             'date_of_birth' => 'required',
             'fcm_registration_id'=> 'string',
             'profile_picture_url'=>'image|mimes:jpeg,png,jpg,gif'
        ]);
              
        if ($request->hasFile('profile_picture_url')) {
            $file = request()->file('profile_picture_url');
            $name=time().$file->getClientOriginalName();
            $filePath = 'profiles/' . $name;
            $strg = Storage::disk('s3')->put($filePath, file_get_contents($file),'public');
            $imgpath = env('AWS_URL')."/".$filePath;
            }
            else{
                  $imgpath = "http://api.doodlepad.in/user.png";
            }
              
             
         
            $profile = UserProfile::where('user_id', Auth::user()->id)
            ->first();
            if (!$profile) {
                $profile = new UserProfile([
            'gender' => request()->gender,
            'bio' => request()->bio,
            'date_of_birth'=>request()->date_of_birth,
            'fcm_registration_id'=> request()->fcm_registration_id,
            'profile_picture_url'=>$imgpath,
            ]);


           $profile->user_id = Auth::user()->id;
           $userProperties = [
            'phoneNumber'=>'+91'.Auth::user()->mobile,
            'uid'=>Auth::user()->id ,
            'displayName' => Auth::user()->username,
            'photoUrl' =>env('AWS_URL')."/".$filePath,
            'disabled' => false,
            ];
           try{
            $createdUser = $this->auth->createUser($userProperties);
            $isSaved = $profile->save();



            $jwtToken = $this->auth->createCustomToken((string)Auth::user()->id);
            return response()->json([
                'status'=>true,
                'message' => 'Profile Created Successfully.',
                'jwt_token' => (string) $jwtToken,
            ], 201);
            }
            catch(AuthError $e){
            return response()->json([
                'status'=>false,
                'message' => 'Something Went Wrong!',
            ], 400);
        }
        }
        else{
            return response()->json([
                    'status'=>false,
            'message' => 'Account Already Exist!',
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
     * @return [string] expires_at
     * @return [string] jwt_token
     */

    public function login(Request $request)
    {
         $request->validate([
            'username' => 'required|string|min:4|max:20',
            'password' => 'required|string|min:8|max:14'
            
        ]);        
        $credentials = request(['username','password']);        
        if(!Auth::attempt($credentials))
            return response()->json([
                 'status'=>false,
                'message' => 'Username or Password is Invalid'
            ], 401);      

            $user = $request->user(); 

            $tokenResult = $user->createToken('Doodlepad Access Token');
        $token = $tokenResult->token;  
        $uid = $user->id;

            $isProfileCreated = UserProfile::where('user_id',$uid)->first();
       
         if ($request->update == 1 && $isProfileCreated){
            $request->validate([
            'fcm_registration_id' => 'required|string',
             ]);       
            $isProfileCreated->fcm_registration_id = $request->fcm_registration_id;
            $isProfileCreated->save();
                 
         }
            
        $token->save();       

            
       
        $jwtToken = $this->auth->createCustomToken((string)$uid);
         return response()->json([
            'status'=>true,
            'access_token' => $tokenResult->accessToken,
            'expires_at' => Carbon::parse(
                $tokenResult->token->expires_at
            )->toDateTimeString(),
            'jwt_token' => (string) $jwtToken,
            'profilecreated'=> ($isProfileCreated) ? (true) : (false),
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
            'message' => 'Successfully Logged Out'
        ]);
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
            ], 404);      
         }
         else{
          
              $isOTPSend = $this->otp->sendOTP($request->mobile);

              if($isOTPSend->type == 'success'){

               return response()->json([
                    'status'=>true,
            'message' => 'Otp Sent Successfully.',
        ], 200);
         }
     }
     


      }

public function newPassword(Request $request){

     $request->validate([
             'mobile' => 'required|string|min:10|max:10',
             'otp' => 'required|max:4',
             'password'=> 'required|string|min:8|max:14'

        ]);     

      $validateOTP = new MSG91();
       $isOTPVerified = $this->otp->verifyOTP($request->mobile,$request->otp);
         if($isOTPVerified->type == 'success'){
             $user = User::where('mobile', $request->mobile)->first();

              if($user){
            $user->password = bcrypt($request->password);
            $user->save();
             return response()->json([
                    'status'=>true,
            'message' => 'Password Changed Successfully.',
        ], 201);
         }

            }
            else{
                return response()->json([
                    'status'=>true,
            'message' => 'Invalid Otp',
        ], 201);
            }


      }


    public function checksession(){
        $is_created = UserProfile::where('user_id',Auth::user()->id)->first(['id']);
        $app_configs = AppConfig::all();
        foreach($app_configs as $app_config){
            $app_version = $app_config->app_version;
        }
        if($is_created){
            return response()->json(['message'=>true,'profile_created'=>true,'app_version'=>$app_version],200);
        }
        return response()->json(['message'=>true,'profile_created'=>false,'app_version'=>$app_version],200);
       
    }

 

}
