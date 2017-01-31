<?php

namespace App\Http\Controllers;

use App\Person;
use App\Ticket;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Mockery\Exception;
use Tymon\JWTAuth\Facades\JWTAuth;

class TicketController extends Controller
{
    public function jwt( Request $request ){

        $credentials = $request->only('email', 'password');

        $result = JWTAuth::attempt($credentials);
        return response()->json(['tocken' => $result]);

    }

    public function authenticate( Ticket $ticket ){
        $ticket->authenticate();
        return response()->ok([
            'sent_mail' => 1,
            'grant_type' => 'ticket_actions',
            'autorize_code' => $ticket->action_token
        ]);
    }

    public function auth( Ticket $ticket, Request $request ){
        try{

            $ticket_action_lifetime = config('railway.ticket_action_lifetime');

            $ticket->authorize( $request->input('code') );
            return response()->ok([
                'grant_type' => 'ticket_actions',
                'action_token' => $ticket->action_token,
                'lifetime' => $ticket_action_lifetime*60,
            ]);
        }catch( Exception $e ){
            return response()->error( $e->getMessage() );
        }
    }

    public function index( $request_id ){

        $ticket = Ticket::where('request_id', $request_id )
            ->get()
            ->toArray();

        if( empty($ticket[0]) ){
            return response()->error( 'TICKET_NOT_FOUND' );
        }

        return response()->ok($ticket[0]);
    }

    public function register( Request $request, Ticket $ticket )
    {
        try{

            $return = $ticket->child();

            $train = (array)$request->input('train');
            $class = (array)$request->input('class');
            $rank = (array)$request->input('rank');

            $adult = (array)$request->input('adult');
            $child = (array)$request->input('child');

            $passengers = 0;

            $max_passengers_per_ticket = config('railway.max_passengers_per_ticket');

            $ticket->register( $train[0], $class[0], $rank[0], $adult[0], $child[0] );
            $passengers = count( $ticket->persons );

            if( $passengers > $max_passengers_per_ticket ){
                throw new Exception('TOO_MANY_PASSENGERS_PER_TRANSACTION');
            }

            if( $return ){
                $return->register( $train[1], $class[1], $rank[1], $adult[1], $child[1] );
                $passengers = count( $return->persons );

                if( $passengers > $max_passengers_per_ticket ){
                    throw new Exception('TOO_MANY_PASSENGERS_PER_TRANSACTION');
                }
            }


            return response()->ok([
                'departure' => $ticket->toArray(),
                'return' => $return ? $return->toArray() : (object)[],
            ]);

        }catch( Exception $e ){
            return response()->error( $e->getMessage() );
        }
    }

    public function confirm( Request $request, Ticket $ticket )
    {
        try{

            $index =  $request->input('index');
            $mobile =  $request->input('mobile');
            $email =  $request->input('email');

            $return = $ticket->child();

            $id =  (array)$request->input('id');
            $name =  (array)$request->input('name');
            $surname =  (array)$request->input('surname');
            $idnumber =  (array)$request->input('idnumber');

            $ticket->confirm( $id[$ticket->id], $name[$ticket->id], $surname[$ticket->id], $idnumber[$ticket->id] );

            if( $return ){
                $return->confirm( $id[$return->id], $name[$return->id], $surname[$return->id], $idnumber[$return->id] );
            }

            $transaction = Transaction::find( $ticket->transaction_id );

            $transaction->bayer([
                'index' => $index,
                'mobile' => $mobile,
                'email' => $email,
            ]);

            $sum = $ticket->amount_from_api;

            if( $return ){
                $sum += $return->amount_from_api;
            }

            $transaction->setAmount( $sum );
            $transaction->save();

            return response()->ok($transaction->toArray());
        }catch( Exception $e ){
            return response()->error( $e->getMessage() );
        }
    }

    public function ret( Ticket $ticket, Request $request ){
        try{
            $persons = $request->input('id');

            if( is_array( $persons ) ){
                foreach ( $persons as $person_id ){

                    $person = Person::find($person_id);

                    if( $person ){
                        $person->ret( true );
                    }
                }
            }else{

                $person = Person::find($persons);
                $person->ret( true );
            }

            return response()->ok($ticket->toArray());
    }catch( Exception $e ){
            return response()->error( $e->getMessage() );
        }
    }

    public function schedule( Ticket $ticket )
    {
        try{
            return response()->ok($ticket->toArray());
        }catch( Exception $e ){
            return response()->error( $e->getMessage() );
        }
    }
}
