<?php

namespace App\Providers;

use Laravel\Passport\Passport;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        Passport::ignoreRoutes();

    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Schema::defaultStringLength(191);
        Response::macro('success', function (int $statusCode, string $message, $data) {

            $final = [
                'statusCode' => $statusCode,
                'message' => $message,
                'results' => $data,
                'dateTime' => new \DateTime()
            ];

            return Response::json($final, $statusCode);

        });

        Response::macro('errors', function (int $statusCode , string $message ,$data) {

            $final = [
                'statusCode' => $statusCode,
                'message' => $message,
                'errors' => $data,
                'dateTime' => new \DateTime()
            ];

            return Response::json($final, $statusCode);

        });
    }
}
