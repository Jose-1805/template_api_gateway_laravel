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

    protected $fillable = ['state', 'input_data', 'output_data'];

    protected $casts = [
        'input_data' => 'json',
        'output_data' => 'json',
    ];

    public function setInputData(Request $request)
    {
        $this->update([
            "input_data" => $this->requestToJson($request)
        ]);
    }
}
