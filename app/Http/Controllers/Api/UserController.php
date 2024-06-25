<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PhpParser\Node\Expr\FuncCall;

class UserController extends Controller
{
    //
    public function index()
    {
        $user = Auth::user();
        return response()->json([
            'status'=>true,
            'data'=>$user
        ]);
    }
    public function update(Request $request)
    {
        $user = Auth::user();
        $user->update([
            'name'=>$request->name,
            'birthday'=>$request->birthday,
            'address'=>$request->address,
            'phone_number'=>$request->phone_number
        ]);
        return response()->json([
            'status'=>true,
            'data'=>$user
        ],200);
    }
    public function search(Request $request)
    {
        $keyword = $request->get('keyword');
        if($keyword){
            $users = User::where('name','like',"%{$keyword}%")->get();
        }
        if($users){
            return response()->json([
                'status'=>true,
                'data'=>$users
            ]);
        }
    }
}
