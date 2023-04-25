<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Model
{
    use HasFactory;
    use HasApiTokens;
    use HasRoles;
    use HasUuids;


    protected $fillable = [
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'remember_token',
    ];

    /**
     * Relación a los tokens asociados al usuario
     */
    public function otherTokens()
    {
        return $this->hasMany(Token::class);
    }

    /**
     * Relación a las solicitudes en segundo plano
     */
    public function backgroundRequest()
    {
        return $this->hasMany(BackgroundRequest::class);
    }
}
