<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\PersonalAccessToken;
use Laravel\Sanctum\Sanctum;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Sanctum::usePersonalAccessTokenModel(PersonalAccessToken::class);

        // Solución SSL para desarrollo local en Windows
        if (app()->environment('local')) {
            $caFile = base_path('cacert.pem');

            if (file_exists($caFile)) {
                $streamContext = stream_context_create([
                    'ssl' => [
                        'cafile'           => $caFile,
                        'verify_peer'      => false,
                        'verify_peer_name' => false,
                    ]
                ]);
                libxml_set_streams_context($streamContext);
            }
        }
    }
}