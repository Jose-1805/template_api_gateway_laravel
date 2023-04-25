<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Traits\JsonRequestConverter;
use Illuminate\Http\Request;

class BackgroundRequest extends Model
{
    use HasUuids;
    use JsonRequestConverter;

    protected $table = 'background_requests';

    protected $fillable = ['state', 'input_data', 'output_data', 'user_id'];

    protected $casts = [
        'input_data' => 'json',
        'output_data' => 'json',
    ];

    /**
     * Asigna los datos de entrada de la solicitud en le objecto
     *
     * @param Request $request
     */
    public function setInputData(Request $request): void
    {
        $this->update([
            "input_data" => $this->requestToJson($request)
        ]);
    }

    /**
     * Relación al usuario asociado a la solicitud
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
