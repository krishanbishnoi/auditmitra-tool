<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use App\Client;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(): void
    {
        View::composer('*', function ($view) {
            $agencyLabel = 'Agency';

            if (Auth::check()) {
                $clientId = Auth::user()->client_id;

                $client = Client::where('client_id', $clientId)->first();

                if ($client) {
                    $agencyLabel = match ($client->client_name) {
                        'Sunstone' => 'Campus',
                        'IA Spaces' => 'Location',
                        'Tester' => 'Agency',
                        'default' => 'Agency',
                    };
                }
            }

            $view->with('agencyLabel', $agencyLabel);
        });
    }
}
