<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Exception;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;


class customerController extends Controller
{
    public function register(Request $request)
    {
        try {
            $customer = new User();
            $customer->name = $request->name;
            $customer->email = $request->email;
            // $customer->password = Crypt::encrypt($request->input('password'));
            $customer->password = Hash::make($request->input('password'));
            $customer->role = 'admin';
            $customer->save();

            return response([
                'message' => 'User Registerd Successfully.'
            ], 200);
        } catch (\Throwable $th) {
            return response([
                'message' => 'User not Registered.'
            ], 400);
        }
    }


    public function login( Request $request ){
        $user = User::where("email",$request->email)->first();
         if (!$user || !Hash::check($request->password, $user->password)) {
            return response([
                'message' => 'These credentials do not match our records.'
            ], 401);
         }
        //  $token = $user->createToken('my-app-token')->plainTextToken;
         $token = $user->createToken('my-app-token', ['expires' => now()->addDays(7)])->plainTextToken;
         $response = [
             'user' => $user,
             'token' => $token,
         ];
         return response($response, 200);
       }

      public function forgotPassword(){
        $user = Auth::user();
        $otp = rand(0, 9999);
        try{
            DB::table('otps')->insert([
            'otp'=>$otp,
            'user_id' => $user->id,
            'status' =>1
            ]);
            $content = "Your otp is ".$otp;
            Mail::raw($content,function ($message) use ($user, $otp) {
                $message->from('abc@gmail.com', 'demo mail');
                $message->to($user['email'], $user['name'])->subject('Forgot Password OTP');
            });
          return response(['Message'=>"OTP has been sent to your registered Email ID." .$user['email']],200);

        }catch(Exception $e){
            return $e;
        }
    }

    public function verifyOtp(Request $request){
        $validator = Validator::make($request->all() ,[
            'email'=>'email|required',
            'otp' => 'integer|required'
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();
            return response($errors);
        } else {
            $email = $request->email;
        $user =User::where('email',$email)->first();
        if(!empty($user)){
            $otp = $request->otp;
            $data = DB::table('otps')->where('user_id', $user->id)->where('otp',$otp)->where('status',1)->first();
            if(!empty($data)){
                $token = $user->createToken('my-app-token', ['expires' => now()->addDays(7)])->plainTextToken;
                return response()->json([
                    'message'  => 'otp verified',
                    'token' => $token,
                    'status' => 200,
                    'success' =>true
                ]);
            }else{
                return response()->json([
                    'message'  => 'otp not matched',
                    'status' => 400,
                    'success' =>false
                ]);
            }
        }else{
            return response()->json([
                'message'  => 'user not found',
                'status' => 404,
                'success' =>false
            ]);
        }
        }
        
        
        
    }

    public function updatePassword(Request $request){
        $validator = Validator::make($request->all() ,[
            'password' => 'required|min:6',
            'confirm_password' => 'required|same:password'
        ]);
    
        if ($validator->fails()) {
            $errors = $validator->errors();
            return response($errors);
        } else {
            $password = Hash::make($request->input('password')); 
    
            $user = Auth::user();
            // return $user;
    
            if(!empty($user)){
                $user1 = User::find($user->id);
                $user1->update(['password' => $password]);
    
                return response()->json([
                    'message'  => 'Password updated successfully',
                    'status' => 200,
                    'success' => true
                ]);
            } else {
                return response()->json([
                    'message'  => 'User not found',
                    'status' => 404,
                    'success' => false
                ]);
            }
        }
    }


    public function getAllUsers(){
        $user = User::all();
        
       
        return response()->json($user);
    }

    public function show($id){
        $user = User::find($id);
        if (!$user) {
            return response()->json([
                "message" => "User Not Found",
                "status" => 404
            ], 404);
        }else{
            return response()->json($user);
        }
        }

        public  function delete( $id){
            $user = User::find($id);
            if(!$user){
                return response()->json([
                   "message"=>"User not found",
                   "status"=>404
               ],404);
           }else{
               $user->delete();
               return response()->json(['message' => 'User deleted successfully']);
           }
        }

        public function update ( Request $request , $id){
            $user = User::find($id);
            if ($user) {
                $user->name = $request->input('name');
                $user->email = $request->input('email');
                $user->password = Hash::make($request->input('password'));
                $user->save();
        
                return response()->json(['message' => 'User update successfully']);
            }  else {
                return response()->json(['message' => 'User  not updated ']);
             }
    }
}

  
    

    



