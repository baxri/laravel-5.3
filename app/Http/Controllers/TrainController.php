<?php

namespace App\Http\Controllers;

use App\Gateway\Ip;
use App\Ticket;
use App\Train;
use App\Models\Transaction;
use Illuminate\Support\Facades\App;
use Mockery\Exception;

class TrainController extends Controller
{
    public function index( $date, $from, $to, $return = null )
    {
        try{

            /*
             * Start transaction with pending
             *
             * */
            $transaction = Transaction::create([
                'status' => Transaction::$pending,
                'ip' => IP::current(),
                'lang' => App::getLocale(),
            ]);

            $result = Train::trains( $date, $from, $to, $transaction->id );

            return response()->ok(
                array(
                    'departure' => $result,
                    'return' => $return ? Train::trains( $return, $to, $from, $transaction->id, $result['ticket'] ) : (object)[]
                )
            );
        }catch( Exception $e ){
            return response()->error( $e->getMessage() );
        }
    }
}
