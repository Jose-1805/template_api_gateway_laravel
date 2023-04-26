<?php

namespace App\Models;

use App\Traits\ServiceConsumer;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

class Service extends Model
{
    use HasApiTokens;
    use HasUuids;
    use ServiceConsumer;


    protected $fillable = [
        'name',
        'base_uri',
        'path',
        'access_token'
    ];
}
