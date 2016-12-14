<?php

namespace App\Http\Controllers;

use App\Gateway\Ip;
use App\Train;
use App\Models\Transaction;
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
