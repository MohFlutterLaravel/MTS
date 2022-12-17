<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id', 'category_id', 'type_id',
        'barecode', 'designation', 'qte',
        'pa', 'pv', 'tva', 'image'
    ];
    

    function user(){
        return $this->belongsTo(User::class);
    }

    function category(){
        return $this->belongsTo(Category::class);
    }

    function type(){
        return $this->belongsTo(Type::class);
    }
    
    function sources(){
        return $this->belongstoMany(Source::class);
    }

    function achats(){
        return $this->belongstoMany(Achat::class)
        ->withPivot('product_id','achat_id','qte');
    }

    function ventes(){
        return $this->belongstoMany(Vente::class)
        ->withPivot('product_id','vente_id','qte', 'prix', 'mantant');
    }
}
