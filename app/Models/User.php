<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    function categories(){
        return $this->hasMany(Category::class);
    }

    function sources(){
        return $this->hasMany(Source::class);
    }

    function virements(){
        return $this->hasMany(Virement::class);
    }

    function types(){
        return $this->hasMany(Type::class);
    }

    function products(){
        return $this->hasMany(Product::class);
    }

    function ventes(){
        return $this->hasMany(Vente::class);
    }

    function achats(){
        return $this->hasMany(Achat::class);
    }

    function payemodes(){
        return $this->hasMany(Payemode::class);
    }

    function payedettes(){
        return $this->hasMany(Payedette::class);
    }

    function operations(){
        return $this->hasMany(Operation::class);
    }

    function clients(){
        return $this->hasMany(Client::class);
    }

    function caisses(){
        return $this->hasMany(Caisse::class);
    }

    // Rest omitted for brevity

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }
}
