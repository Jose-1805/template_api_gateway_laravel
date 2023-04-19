<?php

namespace App\Traits;

use Illuminate\Http\Request;

trait JsonRequestConverter
{
    /**
     * Convierte los datos de un request en un Json
     *
     * @param Request $request
     */
    public function requestToJson(Request $request): string
    {
        $requestData = $request->all();

        // Se verifica si el request contiene archivos
        if ($request->hasFile()) {
            foreach ($request->allFiles() as $fieldName => $files) {
                $fileData = [];
                foreach ($files as $file) {
                    $fileContent = base64_encode(file_get_contents($file->path()));
                    $fileData[] = [
                        'name' => $file->getClientOriginalName(),
                        'type' => $file->getClientMimeType(),
                        'content' => $fileContent
                    ];
                }
                $requestData[$fieldName] = $fileData;
            }
        }

        // Convert the array to a JSON string and return it
        return json_encode($requestData);
    }
}
