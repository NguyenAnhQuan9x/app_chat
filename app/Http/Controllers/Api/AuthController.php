<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    //
    public function signUp (Request $request)
    {
        try {
            $validationUser = Validator::make($request->all(),[
                'name'=>'required',
                'email'=>'required|email|unique:users,email'
            ],
        [
            'name.required'=>'Tên không được bỏ trống',
            'email.required'=>'Email không được bỏ trống',
            'email.email'=>'Email không đúng định dạng'
        ]);
        if($validationUser->fails()){
            return response()->json(
                [
                    'status'=>false,
                    'message'=>'Validation error',
                    'errors'=>$validationUser->errors()
                ], 401
            );
        }
        $user_data = [
            'name'=>$request->name,
            'email'=>$request->email,
            'password'=>bcrypt($request->password)
        ];
        $user = User::create($user_data);
        if($user){
            return response()->json([
                'status'=>true,
                'message'=>'User created successfully',
                'token'=>$user->createToken("API TOKEN")->plainTextToken
            ],200);
        }
        } catch (\Throwable $th) {
            return response()->json([
                'status'=>false,
                'message'=>$th->getMessage()
            ],500);
        }
    }
    public function signIn (Request $request)
    {
        try {
            $validationUser = Validator::make($request->all(),
            [
                'email'=>'required',
                'password'=>'required'
            ],
            [
                'email.required'=>'Email không được bỏ trống',
                'password.required'=>'Mật khẩu không được bỏ trống'
            ]);
            if($validationUser->fails()){
                return response()->json([
                    'status'=>false,
                    'message'=>'Validation error',
                    'errors'=>$validationUser->errors()
                ],401);
            }
            if(!Auth::attempt($request->only(['email', 'password']))){
                return response()->json([
                    'status' => false,
                    'error' => 'Email & Password does not match with our record.',
                ], 401);
            }

            $user = User::where('email', $request->email)->first();

            return response()->json([
                'status' => true,
                'message' => 'User Logged In Successfully',
                'token' => $user->createToken("API TOKEN")->plainTextToken
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status'=>false,
                'message'=>$th->getMessage()
            ],500);
        }
    }
    public function logout()
    {
        Auth::user()->currentAccessToken()->delete();
        return response()->json([
            'status'=>true,
            'message'=>'Logout Successfully'
        ]);
    }
}
