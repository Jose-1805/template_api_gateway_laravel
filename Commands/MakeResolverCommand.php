<?php

namespace App\Console\Commands;

use App\Helpers\StubFormatter;
use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;
use Illuminate\Support\Pluralizer;

class MakeResolverCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:resolver {name} {--E|event=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Crea una clase para solucionar una petición en segundo plano';

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
        $path_resolver = base_path('app/Background') .'/' .$this->getClassName($this->argument('name')) . '.php';
        if(file_exists($path_resolver)) {
            $this->warn("Ya existe un archivo llamado ".$this->getClassName($this->argument('name')).".php");
        } else {
            $this->comment('Creando archivo ...');
            $this->comment($path_resolver.' ...');

            $path_stub_resolver = __DIR__ . '/../../../stubs/resolver-resource.stub';
            $formatter_resolver = new StubFormatter(
                $path_resolver,
                $this->getStubVariables(),
                $path_stub_resolver,
                $this->files
            );
            $formatter_resolver->make();
            $this->info('Archivo creado con éxito');

            if($this->option("event")) {
                $this->addEventSetting($this->option("event"));
            }

            $this->info('Recurso creado con éxito');
            if(!$this->option("event")) {
                $this->comment('Recuerde configurar el evento asociado en el archivo de configuración config/background.php');
            }
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
        ];
    }


    /**
     * Nombre para asignar la clase
     * @param $name
     * @return string
     */
    public function getClassName($name)
    {
        return ucwords(Pluralizer::singular(Str::of($name)->camel()));
    }

    /**
     * Agrega la configuración de evento requerida
     *
     * @return void
     */
    public function addEventSetting($event)
    {
        $file = fopen(base_path('config/background.php'), 'r+') or die('Error');

        $events_found = false;
        $event_is_added = false;

        $content = "";
        while ($line = fgets($file)) {
            // Se encuentra por primera vez la clave "events"
            if (str_contains($line, '"events" => [') && !$events_found) {
                $events_found = true;
            } elseif ($events_found && str_contains($line, "],") && !$event_is_added) {
                $content .= '        "'.$event.'" => App\\Background\\'.$this->getClassName($this->argument('name')).'::class,'.PHP_EOL;
                $event_is_added= true;
            }
            $content .= $line;
        }
        rewind($file);
        fwrite($file, $content);
        fclose($file);
    }
}
