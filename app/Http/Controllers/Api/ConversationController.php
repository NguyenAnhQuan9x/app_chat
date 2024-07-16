<?php

namespace App\Http\Controllers\Api;

use App\Events\NewChatMessage;
use App\Http\Controllers\Controller;
use App\Http\Requests\GroupRequest;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\Participant;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PhpParser\Node\Expr\FuncCall;
use Illuminate\Support\Str;

class ConversationController extends Controller
{
    //
    //Get list conversation
    public function index(Request $request)
    {
        $user = Auth::user();
        $conversations = $user->myConversation()->with('users')->withCount(['messages'=>function(Builder $query){
            $query->where('user_id','!=',Auth::id())
                  ->where('is_read',0);
        }])->with('messages',function($message){
            $message->latest();
        });
        $keyword = '';
        if($request->get('keyword')){
            $keyword = $request->get('keyword');
            $conversations = $conversations->where('title','LIKE',"%{$keyword}%");
        }
        $conversations = $conversations->get();
        if($conversations){
            return response()->json([
                'status'=>true,
                'data'=>$conversations
            ],200);
        }
    }
    //get detail conversation
    public function show($conversation)
    {
        $conversation = Conversation::where('id',$conversation)->with('users')->withCount('users')->first(); 
        return response()->json([
            'status'=>true,
            'data'=>$conversation
        ],200);
    }
    //get messages in conversation
    public function getMessages($conversation)
    { 
        try {
            $conversation = Conversation::find($conversation);
        $messages = $conversation->messages()->with('replyMessage','users')->with('users')->oldest()->get();
        $datetime = Carbon::now();
        return response()->json([
            'status'=>true,
            'data'=>$messages,
        ],200);
        } catch (\Throwable $th) {
            return response()->json([
                'status'=>false,
                'message'=>'get message failed'
            ],500);
        }
    }
    //get message detail
    public function getMessage($message)
    {
        $message = Message::where('id',$message)->with('users')->first();
        if($message){   
            return response()->json([
                'status'=>true,
                'message'=>'get message successfully',
                'data'=>$message
            ],200);
        }
    }
    //update status message
    public function updateReadMessage(Conversation $conversation)
    {
        try {
            $user_id = Auth::id();
        $messages = $conversation->messages()->where('user_id','!=',$user_id)->get();
        if(!empty($messages)){
            foreach($messages as $message)
            {
                $message->update([
                    'is_read'=>1
                ]);
            }
        }
        return response()->json([
            'status'=>true,
            'message'=>'update is_read successfully'
        ],200);
        } catch (\Throwable $th) {
            return response()->json([
                'status'=>false,
                'message'=>'update is_read failed'
            ],500);
        }
    }
    //save new message
    public function storeMessage(Request $request,$conversation)
    {
        $user_id = Auth::id();
        $message = Message::create([
            'content'=>$request->msg_content,
            'conversation_id'=>$conversation,
            'user_id'=>$user_id
        ]);
        $message = Message::where('id',$message->id)->with('replyMessage','users')->first();
        broadcast(new NewChatMessage($message))->toOthers();
        if($message){
            return response()->json([
                'status'=>true,
                'message'=>"Save message successfully",
                'data'=>$message
            ],200);
        }
    }
    //reply message
    public function replyMessage(Request $request,$conversation,$message)
    {
        $user_id = Auth::id();
        $message = Message::create([
            'content'=>$request->msg_content,
            'conversation_id'=>$conversation,
            'user_id'=>$user_id,
            'parent_id'=>$message
        ]);
        $message = Message::where('id',$message->id)->with('replyMessage','users')->first();
        broadcast(new NewChatMessage($message))->toOthers();
        if($message){
            return response()->json([
                'status'=>true,
                'message'=>"Save message successfully",
                'data'=>$message
            ],200);
        }
    }
    //recover message
    public function recoverMessage($message)
    {
        try {
            //type is_published is integer 
            //away under not update
            //$data = Message::where('id',$message)->get();
            //dd($data->toQuery());
            // $data[0]->update(
            //     [
            //         'is_published'=>8
            //     ]
            //     );
            $user_id = Auth::id();
            $update = Message::where('id',$message)
                    ->where('user_id',$user_id)
            ->update([
                'is_published'=>0
            ]);
            if($update){
                return response()->json([
                    'status'=>true,
                    'message'=>'Recover message successfully',
                    'data'=>$message
                ],200);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'status'=>false,
                'message'=>'Recover message failed'
            ]);
        }
    }
    //delete my message
    public function deleteMyMessage($message)
    {
        try {
            //type is_published is integer 
            //away under not update
            //$data = Message::where('id',$message)->get();
            //dd($data->toQuery());
            // $data[0]->update(
            //     [
            //         'is_published'=>8
            //     ]
            //     );
            $user_id = Auth::id();
            $update = Message::where('id',$message)
            ->update([
                'status'=>0
            ]);
            if($update){
                return response()->json([
                    'status'=>true,
                    'message'=>'Delete message successfully',
                    'data'=>$message
                ],200);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'status'=>false,
                'message'=>'Recover message failed'
            ],500);
        }
    }
    //search message
    public function searchMessage(Conversation $conversation,Request $request)
    {
        try {
        $messages = '';
        $keyword = '';
        if($request->get('keyword')){
            $keyword = $request->get('keyword');
            $messages = $conversation->messages()->with('replyMessage','users')->with('users')
            ->where('content','like',"%{$keyword}%");
            $messages = $messages->get();
        }else{
            $messages = '';
        }
        $messages_search = [];
        if(!empty($messages))
        {
            foreach($messages as $message)
            {
                $message->content = Str::replaceFirst([$keyword,Str::upper($keyword)],'<span class="search-marker">'.$keyword.'</span>',$message->content);
                if(Str::length($message->content)>100){
                    $check_position = Str::substr($message->content,0,50);
                    if(!Str::contains($check_position,'<span class="search-marker">'.$keyword.'</span>')){
                        $message->content = $check_position.'... '.'<span class="search-marker">'.$keyword.'</span>'.' ... ';
                    }else{
                        $message->content = Str::substr($message->content,0,75);
                    }
                }
                array_push($messages_search,$message);
            }
        }
        return response()->json([
            'status'=>true,
            'data'=>$messages,
        ],200);
        } catch (\Throwable $th) {
            return response()->json([
                'status'=>false,
                'message'=>'get message failed',
            ],500);
        }
    }
    //create private chat
    public function storePrivate($user_id)
    {
        try {
            $user = User::where('id',$user_id)->get();
        $conversation = Conversation::create([
            'user_id'=>Auth::id(),
            'type'=>'private',
            'title'=>$user[0]->name
        ]); 
        $participant_data = [
            [
                'user_id'=>Auth::id()
            ],
            [
                'user_id'=>$user_id
            ]
        ];
        $conversation->participant()->createMany($participant_data);
        return response()->json([
            'status'=>true,
            'message'=>'Create conversation successfully'
        ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status'=>false,
                'message'=>'Create conversation failed'
            ],500);
        }
        
    }
    //delete conversation
    public function deleteConversation(Conversation $conversation)
    {
       try {
        $messages = $conversation->messages();
        $messages->delete();
        $participant = $conversation->participant();
        $participant->delete();
        $conversation->delete();
        return response()->json([
            'status'=>true,
            'message'=>'Delete conversation successfully'
        ]);
       } catch (\Throwable $th) {
        return response()->json([
            'status'=>false,
            'message'=>'Create conversation failed'
        ],500);
       }

    }
    //delete overall messages in conversation
    public function deleteMessageConversation(Conversation $conversation)
    {
        $messages = $conversation->messages();
        if($messages->delete()){
            return response()->json([
                'status'=>true,
            'message'=>'Delete conversation successfully'
            ]);
        }
    }
    public function storeGroup(Request $request)
    {
        try {
            if($request->title == ''){
                return response()->json([
                    'status'=>false,
                    'message'=>'Create group failed'
                ],204);
            }
            $conversation = Conversation::create([
                'title'=>$request->title,
                'user_id'=>Auth::id(),
                'type'=>'group'
            ]);
            $user_id = $request->user_id;
            $participant_data = [];
            $participant_data[] = ['user_id'=>Auth::id()];
            if(!empty($user_id)){
                foreach($user_id as $value){
                    $participant_data[] = ['user_id'=>$value];
                }
            }
            $conversation?->participant()->createMany($participant_data);
            return response()->json([
                'status'=>true,
                'message'=>'Create group successfully'
            ],200);
        } catch (\Throwable $th) {
            return response()->json([
                'status'=>false,
                'message'=>'Create group failed'
            ],500);
        }
    }
    public function addMemberGroup(Conversation $conversation,Request $request)
    {
        try {
            $user_id = $request->user_id;
            $participant = $conversation->participant()->whereIn('user_id',$user_id)->get();
            if(count($participant)>0){
                return response()->json([
                    'status'=>false,
                    'message'=>'add member to group failed'
                ],422);
            }
            $participant_data = [];
            if(!empty($user_id)){
                foreach($user_id as $value){
                    $participant_data[] = ['user_id'=>$value];
                }
            }
            $conversation?->participant()->createMany($participant_data);
            return response()->json([
                'status'=>true,
                'message'=>'add member to group successfully'
            ],200);
        } catch (\Throwable $th) {
            return response()->json([
                'status'=>false,
                'message'=>'add member to group failed'
            ],500);
        }
    }
    public function deleteMemberGroup(Conversation $conversation,$user)
    {
        try {
            if($conversation->user_id != Auth::id()){
                return response()->json([
                    'status'=>false,
                    'message'=>'Remove user failed'
                ],403);
            }
            
            $conversation->participant()->where('user_id',$user)->delete();
            $conversation->messages()->where('user_id',$user)->delete();
            return response()->json([
                'status'=>true,
                'message'=>'Remove user successfully'
            ],200);
        } catch (\Throwable $th) {
            return response()->json([
                'status'=>false,
                'message'=>'add member to group failed'
            ],500);
        }
    }

}
