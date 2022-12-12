<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Client extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['phone', 'user_id', 'name', 'dette'];
    

    function user(){
        return $this->belongsTo(User::class);
    }

    function ventes(){
        return $this->hasMany(Vente::class);
    }

    function payedettes(){
        return $this->hasMany(Payedette::class);
    }
}
