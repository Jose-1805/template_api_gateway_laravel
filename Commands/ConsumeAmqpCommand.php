<?php

namespace App\Console\Commands;

use App\Background\src\BackgroundRequestResolver;
use Illuminate\Console\Command;

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
        return \Amqp::consume($this->argument('queue'), function ($message, $resolver) {
            $background_request_resolver = new BackgroundRequestResolver($message->getRoutingKey(), $message->body);
            $background_request_resolver->resolve();
            $resolver->acknowledge($message);
        });
    }
}
