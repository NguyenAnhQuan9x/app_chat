<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Icon extends Model
{
    use HasFactory;
    public $table = 'icons';
    protected $fillable = [
        'title',
        'content'
    ];
    public function message()
    {
        return $this->belongsToMany(Message::class,'message_icon','icon','id');
    }
}
