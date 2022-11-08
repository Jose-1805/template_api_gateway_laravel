<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;
use Illuminate\Support\Pluralizer;

class ServiceConnectionCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:service-connection {name} {--P|path=} {--B|base_uri}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Crear recursos y configuraciones necesarias para establecer una conexión a un servicio';

    /**
     * Filesystem instance
     * @var Filesystem
     */
    protected $files;

    /**
     * Create a new command instance.
     * @param Filesystem $files
     */
    public function __construct(Filesystem $files)
    {
        parent::__construct();

        $this->files = $files;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $path = $this->getSourceFilePath();
        $pathController = $this->getSourceFilePathController();

        $this->makeDirectory(dirname($path));
        $this->makeDirectory(dirname($pathController));

        $contents = $this->getSourceFile($this->getStubPath(), $this->getStubVariables());
        $controllerContents = $this->getSourceFile($this->getStubPathController(), $this->getStubVariables());

        if (!$this->files->exists($path)) {
            $this->comment('Creando servicio ...');
            $this->files->put($path, $contents);
            $this->info('File : {$path} created');

            $this->comment('Creando controlador ...');
            $this->files->put($pathController, $controllerContents);
            $this->info('Controlador creado con éxito');

            $this->comment('Agregando rutas ...');
            $this->addRoute($this->getClassName($this->argument('name')) . 'Controller', $this->getRoute());
            $this->info('Rutas agregadas con éxito');

            $this->comment('Asignando variables de entorno ...');
            $this->addEnvironmentVar();
            $this->addServiceConfig();
            $this->info('Variables agregadas con éxito');
        } else {
            $this->error('File : {$path} already exits');
        }
    }

    /**
     * Build the directory for the class if necessary.
     *
     * @param  string  $path
     * @return string
     */
    protected function makeDirectory($path)
    {
        if (! $this->files->isDirectory($path)) {
            $this->files->makeDirectory($path, 0777, true, true);
        }

        return $path;
    }

    /**
     * Nombre para asignar la clase del servicio
     * @param $name
     * @return string
     */
    public function getClassName($name)
    {
        return ucwords(Pluralizer::singular(Str::of($name)->camel()));
    }

    /**
     * Nombre formateado del servicio
     *
     * @param string $name
     * @return string
     */
    public function getServiceName($name): string
    {
        return Str::of($name)->snake()->value.'_service';
    }

    /**
     * Obtiene la ruta base que se asigna a las rutas del asociadas al controlador generado
     *
     * @return string
     */
    public function getRoute(): string
    {
        $route = $this->getPath();
        return str_replace('/api/', '', $route);
    }

    /**
     * Obtiene el path para agregar a la ruta base y acceder a las funciones del servicio
     *
     * @return string
     */
    public function getPath(): string
    {
        if ($this->option('path')) {
            return $this->option('path');
        } else {
            return '/api/'.Str::slug(Str::snake($this->getClassName($this->argument('name'))));
        }
    }

    /**
     * Obtiene url base para conectarse al servicio
     *
     * @return string
     */
    public function getBaseUri(): string
    {
        if ($this->option('base_uri')) {
            return $this->option('base_uri');
        } else {
            return 'http://'.Str::slug($this->getServiceName($this->argument('name')));
        }
    }

    /**
     * Return the stub file path
     * @return string
     *
     */
    public function getStubPath()
    {
        return __DIR__ . '/../../../stubs/service.stub';
    }

    /**
     * Return the stub file path controller
     * @return string
     *
     */
    public function getStubPathController()
    {
        return __DIR__ . '/../../../stubs/service-controller.stub';
    }

    /**
     * Map the stub variables present in stub to its value
     *
     * @return array
     *
     */
    public function getStubVariables()
    {
        return [
            'CLASS_NAME' => $this->getClassName($this->argument('name')),
            'SERVICE_NAME' => $this->getServiceName($this->argument('name')),
            'CLASS_NAME_PLURAL' => Pluralizer::plural($this->getClassName($this->argument('name'))) ,
            'PATH' => $this->getPath()
        ];
    }

    /**
     * Get the stub path and the stub variables
     *
     * @return bool|mixed|string
     *
     */
    public function getSourceFile($path, $vars)
    {
        //return $this->getStubContents($this->getStubPath(), $this->getStubVariables());
        return $this->getStubContents($path, $vars);
    }


    /**
     * Replace the stub variables(key) with the desire value
     *
     * @param $stub
     * @param array $stubVariables
     * @return bool|mixed|string
     */
    public function getStubContents($stub, $stubVariables = [])
    {
        $contents = file_get_contents($stub);

        foreach ($stubVariables as $search => $replace) {
            $contents = str_replace('$'.$search.'$', $replace, $contents);
        }

        return $contents;
    }

    /**
     * Get the full path of generate class
     *
     * @return string
     */
    public function getSourceFilePath()
    {
        return base_path('app/Services') .'/' .$this->getClassName($this->argument('name')) . 'Service.php';
    }

    /**
     * Get the full path of generate controller
     *
     * @return string
     */
    public function getSourceFilePathController()
    {
        return base_path('app/Http/Controllers') .'/' .$this->getClassName($this->argument('name')) . 'Controller.php';
    }

    /**
     * Agrega las rutas de api para el controlador creado
     *
     * @return void
     */
    public static function addRoute($controller_name, $route_name)
    {
        $file = fopen(base_path('routes/api.php'), 'r+') or die('Error');
        $use_is_added = false;
        $content = "";
        while ($line = fgets($file)) {
            if (str_contains($line, 'use ') && !$use_is_added) {
                $content .= "use App\Http\Controllers\\$controller_name;\n";
                $use_is_added = true;
            }
            $content .= $line;
        }
        $content .= "Route::apiResource('$route_name', $controller_name::class);\n";
        rewind($file);
        fwrite($file, $content);
        fclose($file);
    }

    /**
     * Asigna el valor de base_uri en el archivo de configuración de variables de entorno
     *
     * @return void
     */
    public function addEnvironmentVar()
    {
        file_put_contents(base_path('.env'), "\n".strtoupper(Str::of($this->argument('name'))->snake()->value).'_SERVICE_BASE_URL='.$this->getBaseUri(), FILE_APPEND);
    }

    /**
     * Agrega la configuración del servicio en el archivos config/services.php
     *
     * @return void
     */
    public function addServiceConfig()
    {
        $file = fopen(base_path('config/services.php'), 'r+') or die('Error');
        $is_added = false;
        $content = "";
        while ($line = fgets($file)) {
            $content .= $line;
            if (str_contains($line, 'cluster_services') && !$is_added) {
                $content .= "        '".$this->getServiceName($this->argument('name'))."' => ['base_uri' => env('".strtoupper(Str::of($this->argument('name'))->snake()->value)."_SERVICE_BASE_URL')],\n";
                $is_added = true;
            }
        }
        rewind($file);
        fwrite($file, $content);
        fclose($file);
    }
}
