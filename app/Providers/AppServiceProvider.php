<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use App\Client;
use App\Model\ClientMasterSetting;

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

            $masterLableAgency = 'Agency';

            if (Auth::check()) {
                $clientId = Auth::user()->client_id;

                $client = Client::where('client_id', $clientId)->first();
                if ($client) {
                    $masterLableAgency = ClientMasterSetting::where('client_id', $client->client_id)
                        ->where('field_name', 'agency_name_for_client')->value('field_value');
                }
            }

            $view->with('masterLableAgency', $masterLableAgency);
        });
    }
}
