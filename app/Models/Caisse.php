<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Caisse extends Model
{
    use HasFactory;





    protected $fillable = ['label', 'user_id', 'tot_enc', 'tot_dec', 'solde_ini', 'solde'];
    
    function user(){
        return $this->belongsTo(User::class);
    }

    function virements(){
        return $this->hasMany(Virement::class);
    }

    function ventes(){
        return $this->hasMany(Vente::class);
    }

    function operations(){
        return $this->hasMany(Operation::class);
    }

    function achats(){
        return $this->hasMany(Achat::class);
    }
}
