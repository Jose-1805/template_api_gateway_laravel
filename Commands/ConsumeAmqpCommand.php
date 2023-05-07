<?php

namespace App\Console\Commands;

use App\Background\src\BackgroundRequestResolver;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ConsumeAmqpCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'consume:amqp {queue}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Crea worker para consumir mensajes amqp en una cola';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     */
    public function handle()
    {
        Log::info("Conectando a Rabbit MQ");
        while(!$this->connect()) {
            sleep(config("amqp.interval_connection", 5));
        }
    }

    public function connect()
    {
        $result = null;

        try {
            $result = \Amqp::consume($this->argument('queue'), function ($message, $resolver) {
                $background_request_resolver = new BackgroundRequestResolver($message->getRoutingKey(), $message->body);
                $background_request_resolver->resolve();
                $resolver->acknowledge($message);
            });
        } catch (Exception $e) {
            $result = null;
            Log::error("Error en conexión a Rabbit MQ", ["error" => $e->getMessage()]);
        }
        return $result;
    }
}
