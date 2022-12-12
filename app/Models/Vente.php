<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Vente extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['caisse_id', 'user_id', 'client_id', 'payemode_id', 'mantant', 'observation', 'isvalid'];

    function user(){
        return $this->belongsTo(User::class);
    }
    
    function caisse(){
        return $this->belongsTo(Caisse::class);
    }

    function client(){
        return $this->belongsTo(Client::class);
    }

    function payemode(){
        return $this->belongsTo(Payemode::class);
    }

    function products(){
        return $this->belongstoMany(Product::class);
    }
}
