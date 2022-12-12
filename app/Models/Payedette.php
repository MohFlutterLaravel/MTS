<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class Payedette extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['caisse_id', 'user_id', 'client_id', 'mantant', 'observation'];

    function user(){
        return $this->belongsTo(User::class);
    }

    function client(){
        return $this->belongsTo(Client::class);
    }
}
