<?php

namespace App\Console\Commands;

use App\Helpers\StubFormatter;
use App\Models\Service;
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
    protected $signature = 'make:service-connection {name} {base_uri} {access_secret} {--P|path=}';

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
        // Ruta de almacenamiento del servicio con nombre completo
        $path_service = base_path('app/Services')."/".$this->getClassName($this->argument('name')) . 'Service.php';
        // Ruta de almacenamiento del controlador con nombre completo
        $path_controller = base_path('app/Http/Controllers') .'/' .$this->getClassName($this->argument('name')) . 'Controller.php';

        // No existe ningún servicio o controlador con el nombre solicitado
        if (!$this->files->exists($path_service) && !$this->files->exists($path_controller) && !Service::where("name", strtolower(Str::of($this->argument('name'))->snake()->value))->count()) {
            $this->comment('Creando controlador ...');
            $path_stub_controller = __DIR__ . '/../../../stubs/service-controller.stub';
            $formatter_controller = new StubFormatter(
                $path_controller,
                $this->getStubVariables(),
                $path_stub_controller,
                $this->files
            );
            $formatter_controller->make();
            $this->info('Controlador creado con éxito');

            $this->comment('Agregando rutas ...');
            $this->addRoute($this->getClassName($this->argument('name')) . 'Controller', $this->getRoute());
            $this->info('Rutas agregadas con éxito');

            $this->comment('Creando servicio en base de datos ...');
            $this->info("Servicio creado, guarde de forma segura el siguiente token de acceso en el servicio: ".$this->addServiceToDatabase());
        } else {
            $this->error('Ya existe un servicio o controlador con el nombre sugerido "'.$this->argument('name').'"');
        }
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
            'PATH' => $this->getPath(),
            'SERVICE_NAME_SNAKE' => Str::of($this->argument('name'))->snake()->value,
        ];
    }

    /**
     * Agrega las rutas de api para el controlador creado
     *
     * @return void
     */
    public function addRoute($controller_name, $route_name)
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
     * Agrega la información del servicio a la base de datos con token de acceso
     */
    public function addServiceToDatabase(): string
    {
        $service = Service::create([
            "name" => Str::of($this->argument('name'))->snake()->value,
            "base_uri" => $this->argument('base_uri'),
            "path" => $this->getPath(),
            "access_secret" => $this->argument('access_secret'),
        ]);
        return $service->createToken("services")->plainTextToken;
    }
}
