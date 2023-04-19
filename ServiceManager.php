<?php

namespace App\Services;

use App\Traits\ServiceConsumer;

class ServiceManager
{
    use ServiceConsumer;

    /**
     * Url base de conexión al micro servicio
     *
     * @var string
     */
    public $base_uri;
    public $access_secret;

    public function __construct()
    {
        $this->base_uri = "Without URL";
        $this->access_secret = "";
    }
}
