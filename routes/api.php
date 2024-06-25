<?php

use App\Http\Controllers\Api\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });
Route::group(['middleware'=>'auth:sanctum'],function(){
    //api logout
    Route::get('/auth/logout',[\App\Http\Controllers\Api\AuthController::class, 'logout']);
    //api user profile
    Route::group(['prefix'=>'user'],function(){
        Route::get('/',[\App\Http\Controllers\Api\UserController::class,'index']);
        Route::put('/update',[\App\Http\Controllers\Api\UserController::class,'update']);
        Route::get('/search',[\App\Http\Controllers\Api\UserController::class,'search']);
    });
    //api conversation
    Route::group(['prefix'=>'conversations'],function(){
        Route::get('/',[\App\Http\Controllers\Api\ConversationController::class,'index']);
        Route::get('/{conversation}',[\App\Http\Controllers\Api\ConversationController::class,'show']);
        Route::post('/group/store',[\App\Http\Controllers\Api\ConversationController::class,'storeGroup']);
        Route::post('/group/member/add/{conversation}',[\App\Http\Controllers\Api\ConversationController::class,'addMemberGroup']);
        Route::post('/group/member/delete/{conversation}/{user}',[\App\Http\Controllers\Api\ConversationController::class,'deleteMemberGroup']);
        Route::post('/store/{user_id}',[\App\Http\Controllers\Api\ConversationController::class,'storePrivate']);
        Route::put('/update/{conversation}',[\App\Http\Controllers\Api\ConversationController::class,'update']);
        Route::delete('/delete/{conversation}',[\App\Http\Controllers\Api\ConversationController::class,'deleteConversation']);
        //message
        Route::get('/messages/{conversation}',[\App\Http\Controllers\Api\ConversationController::class,'getMessages']);
        //get message detail
        Route::get('/message/{message}',[\App\Http\Controllers\Api\ConversationController::class,'getMessage']);
        //recover message
        Route::put('/message/recover/{message}',[\App\Http\Controllers\Api\ConversationController::class,'recoverMessage']);
        Route::put('/message/delete/{message}',[\App\Http\Controllers\Api\ConversationController::class,'deleteMyMessage']);
        Route::put('/message/read/{conversation}',[\App\Http\Controllers\Api\ConversationController::class,'updateReadMessage']);
        Route::post('/message/store/{conversation}',[\App\Http\Controllers\Api\ConversationController::class,'storeMessage']);
        Route::post('/message/store/{conversation}/{message}',[\App\Http\Controllers\Api\ConversationController::class,'replyMessage']);
        //delete overall messages in conversation
        Route::delete('/messages/delete/{conversation}',[\App\Http\Controllers\Api\ConversationController::class,'deleteMessageConversation']);


    });
});
Route::post('/auth/login',[\App\Http\Controllers\Api\AuthController::class,'signIn']);
Route::post('/auth/register',[\App\Http\Controllers\Api\AuthController::class,'signUp']);
