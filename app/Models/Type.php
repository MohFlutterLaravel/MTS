<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Type extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['user_id', 'type_name', 'stock_touch'];

    function user(){
        return $this->belongsTo(User::class);
    }

    function products(){
        return $this->hasMany(Products::class);
    }
}
