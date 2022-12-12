<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Payemode extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['user_id', 'mode', 'ispay'];
    

    function user(){
        return $this->belongsTo(User::class);
    }

    function ventes(){
        return $this->hasMany(Vente::class);
    }

    function achats(){
        return $this->hasMany(Achat::class);
    }
    
}
