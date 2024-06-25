<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Message extends Model
{
    use HasFactory,SoftDeletes;
    public $table = 'messages';
    protected $fillable = [
        'content',
        'conversation_id',
        'user_id',
        'parent_id',
        'status',
        'is_read'
    ];
    public function users()
    {
        return $this->belongsTo(User::class,'user_id','id');
    }
    public function conversation()
    {
        return $this->belongsTo(Conversation::class,'conversation_id','id');
    }
    public function replyMessage()
    {
        return $this->belongsTo(Message::class,'parent_id','id')
                    ->with('users');
    }
}
