<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Conversation extends Model
{
    use HasFactory,SoftDeletes;
    public $table = 'conversations';
    protected $fillable=[
        'title',
        'user_id',
        'type'
    ];
    public static $sex = [
        'female'=>'Nữ',
        'male'=>'Name'
    ];
    public function users()
    {
        return $this->belongsToMany(User::class,'participants','conversation_id','user_id');
    }
    public function messages()
    {
        return $this->hasMany(Message::class,'conversation_id','id');
    }
    public function participant()
    {
        return $this->hasMany(Participant::class,'conversation_id','id');
    }
    public function createBy()
    {
        return $this->belongsTo(User::class,'user_id','id');
    }

}
