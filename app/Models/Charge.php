<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Charge extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['designation', 'user_id', 'caisse_id', 'mantant'];

    function user(){
        return $this->belongsTo(User::class);
    }

    function caisse(){
        return $this->belongsTo(Caisse::class);
    }

    function typecharge(){
        return $this->belongsTo(Typecharge::class);
    }
}
