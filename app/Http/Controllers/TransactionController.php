<?php

namespace App\Http\Controllers;

use App\Gateway\Payment;
use App\Models\TransactionLog;
use App\Ticket;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Mockery\Exception;

class TransactionController extends Controller
{
    public function index( Transaction $transaction ){
        try{
            return response()->ok($transaction->toArray());
        }catch( Exception $e ){
            return response()->error( $e->getMessage() );
        }
    }

    public function  checkout( Transaction $transaction ){
        try{
            return response()->ok($transaction
                                    ->checkout()
                                    ->redirect());
        }catch( Exception $e ){
            return response()->error( $e->getMessage() );
        }
    }

    public function  finish( Request $request ){

            $MerchantOrderID = $request->input('MerchantOrderID');
            $Status = $request->input('Status');
            $Hash = $request->input('Hash');

            if( empty($MerchantOrderID) ){
                die('NULL MerchantOrderID');
            }

            $transaction = Transaction::find( $MerchantOrderID );

            $log = [
                'transaction_id' => $transaction->id,
                'op' => 'callback',
                'arguments' => json_encode($request->toArray()),
            ];

            try{

                if( $transaction->status != Transaction::$process ){
                    throw new Exception('TRANSACTION_ALREADY_PROCESED', Payment::$error_dublicated_request);
                }

                /*
                 * @ If Such transaction not found reverse money
                 *
                 * */

                if( empty( $transaction ) )
                    Payment::autoReversal('TRANSACTION_NOT_FOUND_WHIT_THIS_ID');

                /*
                 * @ If payment is success but tickets
                 * @ Otherwise delete transaction
                 *
                 * */

                if( $Status == Payment::$success ){

                    $transaction->success();

                    $statuses = [];
                    foreach ( $transaction->tickets as $ticket ){
                        $statuses[] = $ticket->buy();
                    }

                    /*
                     * @ If all tickets canceled return money to user
                     * @ Otherwise return 200 OK
                     *
                     * */

                    $statuses = array_unique( $statuses );

                    if( count( $statuses ) == 1 && $statuses[0] == Ticket::$cancel )
                        Payment::autoReversal( $ticket->reason );

                    $transaction->notify();
                }else{
                    $transaction->cancel();
                }

                $log['text'] = json_encode($transaction->toArray());

                TransactionLog::create($log);

                return response()->ok();
        }catch( Exception $e ){

            $code = 500;

            if( $e->getCode() > 0 ){
                $code = $e->getCode();
            }

            $log['text'] = json_encode([
                'code' => $code,
                'message' => $e->getMessage(),
            ]);

            TransactionLog::create($log);

            return response()->error( $e->getMessage(), $code, [
                'error' => strtolower($e->getMessage())
            ] );
        }
    }
}
