<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MessageIcon extends Model
{
    use HasFactory;
    public $table = 'message_icon';
    protected $fillable = [
        'message_id',
        'icon'
    ];
    public function message()
    {
        return $this->belongsTo(Message::class,'message_id','id');
    }
    public function icon()
    {
        return $this->belongsTo(Icon::class,'icon','id');
    }

}
