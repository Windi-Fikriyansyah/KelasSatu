<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use TusPhp\Tus\Server;

class StartTusServer extends Command
{
    protected $signature = 'tus:serve {--port=1080}';
    protected $description = 'Start Tus resumable upload server';

    public function handle()
    {
        $server = new Server('file');
        $server->setUploadDir(storage_path('app/tus'));

        $this->info("Tus server listening on http://0.0.0.0:" . $this->option('port'));
        $server->serve();
    }
}
