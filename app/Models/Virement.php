<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Virement extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['caisse_id', 'user_id', 'source_id', 'mantant'];
    

    function user(){
        return $this->belongsTo(User::class);
    }

    function source(){
        return $this->belongsTo(Source::class);
    }

    function caisse(){
        return $this->belongsTo(Caisse::class);
    }


}
