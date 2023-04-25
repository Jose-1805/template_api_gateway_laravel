<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class BackgroundRequest extends Model
{
    use HasUuids;

    protected $table = 'background_requests';

    protected $fillable = ['event', 'state', 'input_data', 'output_data', 'user_id'];

    protected $casts = [
        'input_data' => 'json',
        'output_data' => 'json',
    ];

    /**
     * Relación al usuario asociado a la solicitud
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
