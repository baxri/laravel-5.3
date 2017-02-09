<?php

namespace App\Providers;

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
        /*
         * Set Application Language (Localization)
         *
         * */

        if( in_array($request->input('lang'), ['ka', 'en']) ){
            App::setLocale($request->input('lang'));
        }


        /*
         * Configure Application responses
         *
         * */

        Response::macro('ok', function ( $json = null, $message = 'OK', $code = 200 ) {

            $json = (array) $json;

            $response = response()->json([
                'errorcode' => 0,
                'message' => strtoupper($message),
                'data' => $json
            ], 200, [], JSON_UNESCAPED_UNICODE)
                ->header('Access-Control-Allow-Origin', '*')
                ->header('charset', 'utf-8')
                ->setStatusCode( $code, $message );

           /* $response = response(
                ['errorcode' => 0, 'message' => strtoupper($message), 'data' => $json] )
                ->header('Access-Control-Allow-Origin', '*')
                ->header('charset', 'utf-8')
                ->setStatusCode( $code, $message );*/

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
