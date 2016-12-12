<?php

namespace App\Providers;

use App\Gateway\Payment;
use App\Transaction;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\ServiceProvider;

class RailwayServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        Response::macro('ok', function ( $json = null, $message = 'OK', $code = 200 ) {

            $json = (array) $json;
            $response = response(
                ['errorcode' => 0, 'message' => strtolower($message), 'data' => $json] )
                ->setStatusCode( $code, $message )
                ->header('Access-Control-Allow-Origin', '*');

            return $response;
        });

        Response::macro('error', function ( $message = 'OK', $code = 500, $json = null ) {

            $json = (array) $json;

            $response = response(json_encode(
                ['errorcode' => $code, 'message' => strtolower($message)]
            ))
                ->setStatusCode( 200, 'OK' )
                ->header('Access-Control-Allow-Origin', '*');

            return $response;
        });
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {

    }
}
