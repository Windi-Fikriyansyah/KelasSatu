<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use TusPhp\Tus\Server;

class TusController extends Controller
{
    public function handle(Request $request)
    {
        $server = new Server('file'); // pakai driver file
        $server->setUploadDir(storage_path('app/tus')); // simpan di storage/app/tus

        return $server->serve();
    }
}
