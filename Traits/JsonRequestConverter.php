<?php

namespace App\Traits;

use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;

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
        foreach ($request->allFiles() as $fieldName => $files) {
            $fileData = null;
            if(is_a($files, UploadedFile::class)) {
                $fileData = $this->getFileData($files);
            } elseif(gettype($files) == 'array') {
                $fileData = [];
                foreach ($files as $file) {
                    $fileData[] = $this->getFileData($file);
                }
            }
            $requestData[$fieldName] = $fileData;
        }

        // Convert the array to a JSON string and return it
        return json_encode($requestData);
    }

    /**
     * Obtiene la información de un archivo y su contenido
     *
     * @param UploadedFile $file
     */
    public function getFileData(UploadedFile $file): array
    {
        $fileContent = base64_encode(file_get_contents($file->path()));
        return [
            'name' => $file->getClientOriginalName(),
            'type' => $file->getClientMimeType(),
            'content' => $fileContent
        ];
    }
}
