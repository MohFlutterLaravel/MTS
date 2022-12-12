<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Typecharge extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['designation', 'user_id'];

    function user(){
        return $this->belongsTo(User::class);
    }
}
