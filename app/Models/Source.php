<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Source extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name', 'user_id', 'phone',
         'address', 'tot_achat','tot_vers', 'solde'
        ];
    
    function user(){
        return $this->belongsTo(User::class);
    }

    function virements(){
        return $this->hasMany(Virement::class);
    }

    function achats(){
        return $this->hasMany(Achat::class);
    }
    
    function products(){
        return $this->belongstoMany(Product::class);
    }
}
