<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PersonController extends Controller
{
    public function passengersByEmail( $email ){

        $person_statuses = DB::table('persons')->select(
            DB::raw(' 
                persons.name,
                persons.surname,
                persons.idnumber
                ')
        )
            ->join('tickets', 'tickets.id', '=', 'persons.ticket_id')
            ->join('transactions', 'transactions.id', '=', 'tickets.transaction_id')
            ->where( "transactions.email", $email )
            ->groupBy('persons.idnumber')
            ->get();

        d($person_statuses);
    }
}
