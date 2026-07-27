<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SwitchClientDatabase
{
    

public function handle($request, Closure $next)
{
    if (auth()->check()) {
        $user = auth()->user();
        Log::info("Authenticated user: ID {$user->id}, Client ID: {$user->client_id}");

        if ($user->client_id == 78) {
            Log::info("Switching to DB: qdegrees_rbl");

            config([
                'database.connections.tenant' => [
                    'driver' => 'mysql',
                    'host' => 'localhost',
                    'port' => '3306',
                    'database' => 'qdegrees_rbl',
                    'username' => 'root',
                    'password' => 'India@2025',

                    
                ],
                'database.default' => 'tenant',
            ]);

            \DB::purge('tenant');
            \DB::reconnect('tenant');
        }

        
    } else {
        Log::warning('User is not authenticated in middleware.');
    }

    return $next($request);
}

}



