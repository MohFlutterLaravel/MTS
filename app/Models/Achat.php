<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class Achat extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['caisse_id', 'user_id', 'source_id', 'payemode_id', 'mantant', 'observation', 'isvalid'];

    function user(){
        return $this->belongsTo(User::class);
    }
    
    function caisse(){
        return $this->belongsTo(Caisse::class);
    }

    function source(){
        return $this->belongsTo(Source::class);
    }

    function payemode(){
        return $this->belongsTo(Payemode::class);
    }

    function products(){
        return $this->belongstoMany(Product::class)
        ->withPivot('product_id','achat_id','qte');
    }
    
}
