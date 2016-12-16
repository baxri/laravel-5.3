<?php

namespace App\Providers;

use App\Gateway\Payment;
use App\Transaction;
use Illuminate\Http\Request;
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
    public function boot( Request $request )
    {
        if( in_array($request->input('lang'), ['ka', 'en']) ){
            App::setLocale($request->input('lang'));
        }

        Response::macro('ok', function ( $json = null, $message = 'OK', $code = 200 ) {

            $json = (array) $json;
            $response = response(
                ['errorcode' => 0, 'message' => strtoupper($message), 'data' => $json] )
                ->setStatusCode( $code, $message )
                ->header('Access-Control-Allow-Origin', '*');

            return $response;
        });

        Response::macro('error', function ( $message = 'OK', $code = 500, $header_error = false ) {

            if( !$header_error ){

                $response = response(json_encode(
                    ['errorcode' => $code, 'message' => strtoupper($message)]
                ))
                    ->setStatusCode( 200, 'OK' )
                    ->header('Access-Control-Allow-Origin', '*');

            }else{
                $response = response(json_encode(
                    ['errorcode' => $code, 'message' => strtoupper($message)]
                ))
                    ->setStatusCode( $code, $message )
                    ->header('Access-Control-Allow-Origin', '*');
            }

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
