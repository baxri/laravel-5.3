<?php

namespace App\Http\Controllers;

use App\Gateway\Ip;
use App\Models\PayoutTransaction;
use App\PayoutInfo;
use App\Person;
use App\Ticket;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Mockery\Exception;

class PayoutController extends Controller
{
    public function make( Request $request, Ticket $ticket ){

        try{

            $payout = new PayoutTransaction();
            $payout->make( $ticket, [
                'name' => $request->input('name'),
                'surname' => $request->input('surname'),
                'idnumber' => $request->input('idnumber'),
                'birth_date' => Carbon::parse($request->input('birth_date'))->format('Y-m-d'),
                'iban' => $request->input('iban'),
                'ip' => Ip::current(),
            ], Person::needPayout( $ticket->id )->get() );

            return response()->ok($payout->toArray());
        }catch( Exception $e ){
            return response()->error($e->getMessage());
        }

    }
}
