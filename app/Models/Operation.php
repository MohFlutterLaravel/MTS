<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Operation extends Model
{
    use HasFactory;

    protected $fillable = ['caisse_id', 'user_id', 'enc', 'dec', 'ancien_solde', 'nv_solde', 'observation'];

    function user(){
        return $this->belongsTo(User::class);
    }
    
    function caisse(){
        return $this->belongsTo(Caisse::class);
    }
}
