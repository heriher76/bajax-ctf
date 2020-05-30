<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\User;
use Validator;
use App\ChallengeLog;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Mail;

class AuthController extends Controller
{
    public function register(Request $request){
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:191',
            'email' => 'required|email|max:191|unique:users',
            'password' => 'required|min:8',
            'birthplace' => 'required|max:191', 
            'dateofbirth' => 'required|date_format:Y-m-d', 
            'address' => 'required',
            'website' => 'required|max:191',
            'aboutme' => 'required'
        ]);
        if ($validator->fails()){
            return response()->json([
                'success' => false,
                'messages' => 'Please fill in the blank !',
                'data' => $validator->errors(),
            ], 400);
        }

        $emailVerifyCode=str_random(40);
        $data = $request->all();
        $register=User::create([
            'email' => $data['email'],
            'password' => Hash::make($data['password']),

            'name' => $data['name'],
            'birthplace' => $data['birthplace'],
            'dateofbirth' => $data['dateofbirth'],
            'aboutme' => $data['aboutme'],
            'address' => $data['address'],
            'website' => $data['website'],
            
            'email_code' => $emailVerifyCode,
        ]);

        if($register){
            $url=config('app.client_server.verifyemail').$emailVerifyCode;
            Mail::send(new \App\Mail\SendEMailVerification($register,$url));
            $register->assignRole([3]);
            return response()->json([
                'success' => true,
                'messages' => 'Register Success !',
                'data' => $register
            ], 201);
        }
        else {
            return response()->json([
                'success' => false,
                'messages' => 'Can\'t Register !',
                'data'=>NULL,
            ], 400);
        }
    }
    public function login(Request $request){
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|max:191',
            'password' => 'required',
        ]);
        if ($validator->fails()){
            return response()->json([
                'success' => false,
                'messages' => 'Please fill in the blank !',
                'data' => $validator->errors(),
            ], 400);
        }

        $email = $request->input('email');
        $password = $request->input('password');

        $user = User::where('email', $email)->first();
        if(!empty($user) and Hash::check($password, $user->password)){
            if($user->email_code === NULL){
                $apiToken=base64_encode($user->id.'@'.str_random(40));
                $user->update([
                    'api_token' => $apiToken
                ]);
                return response()->json([
                    'success' => true,
                    'messages' => 'Login Success !',
                    'data' => [
                        'user' => $user,
                        'permissions' => User::find($user->id)->getPermissionsViaRoles(),
                        'api_token' => $apiToken
                    ]
                ], 200);
            }
            else {
                return response()->json([
                    'success' => false,
                    'messages' => 'You must verify email first !',
                    'data' => NULL
                ], 403);
            }
        }
        else {
            return response()->json([
                'success' => false,
                'messages' => 'Wrong Email Or Password !',
                'data'=>NULL,
            ], 400);
        }
    }
    public function logout(Request $request){
        $api_token= $request->header('Authorization');
        $user = User::where('api_token', $api_token)->first();
        $user->update([
            'api_token' => ''
        ]);
        return response()->json([
            'success' => true,
            'messages' => 'Logout Success !',
            'data' => ''
        ], 200);
    }

    public function token($token){
        $user = User::where('api_token', $token)->first();
        if($user){
            $challenges = ChallengeLog::where('user_id',$user->id)->get();

            $rank= User::where([
                ['point',$user->point??0],
                ['last_submit_flag','<=',$user->last_submit_flag??0],
            ])->orderBy('point','DESC')->orderBy('last_submit_flag','ASC')->count();

            $rank+= User::where('point','>',$user->point??0)->orderBy('point','DESC')->orderBy('last_submit_flag','ASC')->count();

            return response()->json([
                'success' => true,
                'messages' => 'Token Valid !',
                'data' => [
                    'user' => $user,
                    'challenges' => $challenges,
                    'rank' => $rank
                ]
            ], 200);
        }
        else
            return response()->json([
                'success' => false,
                'messages' => 'Token Not Valid !',
                'data'=>NULL,
            ], 400);
    }
    public function resendemail(Request $request){
        $validator = Validator::make($request->all(), [
            'email' => ['required','email',
                Rule::exists('users')->where(function ($query) {
                    $query->where('email_code', '!=', NULL);
                }),
            ],
        ]);
        if ($validator->fails()){
            return response()->json([
                'success' => false,
                'messages' => 'Can\'t Resend Email !',
                'data' => NULL,
            ], 400);
        }
        else {
            $user=User::where('email',$request->input('email'))->first();
            $url=config('app.client_server.verifyemail').$user->email_code;
            Mail::send(new \App\Mail\SendEMailVerification($user,$url));
            return response()->json([
                'success' => true,
                'messages' => 'A new verification email has been sent !',
                'data' => NULL,
            ], 200);            
        }
    }

    public function verifyemail(Request $request){
        $validator = Validator::make($request->all(), [
            'email_code' => 'required|exists:users',
        ]);
        if ($validator->fails()){
            return response()->json([
                'success' => false,
                'messages' => 'Can\'t Verify Email !',
                'data' => $validator->errors(),
            ], 400);
        }
        else {
            $user=User::where('email_code',$request->input('email_code'))->update([
                'email_code'=>NULL
            ]);
            return response()->json([
                'success' => true,
                'messages' => 'Email Successfully Verified !',
                'data' => NULL,
            ], 200);
        }
    }
    public function forgotpassword(Request $request){
        $validator = Validator::make($request->all(), [
            'email' => 'required|exists:users',
        ]);
        if ($validator->fails()){
            return response()->json([
                'success' => false,
                'messages' => 'Can\'t Reset Password !',
                'data' => $validator->errors(),
            ], 400);
        }
        else {
            $forgot_password=str_random(40);
            $user=User::where('email',$request->input('email'));
            $user->update(['forgot_password'=>$forgot_password]);

            $url=config('app.client_server.resetpassword').$forgot_password;
            Mail::send(new \App\Mail\SendResetPasswordCode($user->first(),$url));
            return response()->json([
                'success' => true,
                'messages' => 'Email verification has been sent !',
                'data'=>NULL,
            ], 200);
        }
    }
    public function cekforgotpassword(Request $request){
        $validator = Validator::make($request->all(), [
            'forgot_password' => 'required|exists:users',
        ]);
        if ($validator->fails()){
            return response()->json([
                'success' => false,
                'messages' => 'Code Not Valid !',
                'data' => $validator->errors(),
            ], 400);
        }
        else
            return response()->json([
                'success' => true,
                'messages' => 'Code Valid !',
                'data'=>NULL,
            ], 200);
    }

    public function resetpassword(Request $request){
        $validator = Validator::make($request->all(), [
            'password' => 'required|confirmed',
            'forgot_password' => 'required|exists:users',
        ]);
        if ($validator->fails()){
            return response()->json([
                'success' => false,
                'messages' => 'Can\'t Reset Password !',
                'data' => $validator->errors(),
            ], 400);
        }
        else {
            $user=User::where('forgot_password',$request->input('forgot_password'))->update([
                'password'=>Hash::make($request->input('password')),
                'forgot_password'=>NULL
            ]);
            return response()->json([
                'success' => false,
                'messages' => 'Reset Password Success !',
                'data'=>NULL,
            ], 200);
        }
    }
}