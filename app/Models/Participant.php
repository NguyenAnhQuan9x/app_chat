<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Participant extends Model
{
    use HasFactory;
    public $table = 'participants';
    protected $fillable = [
        'conversation_id',
        'user_id'
    ];
    public function conversation()
    {
        return $this->belongsTo(Conversation::class,'conversation_id','id');
    }
    public function user()
    {
        return $this->belongsTo(User::class,'user_id','id');
    }
}
