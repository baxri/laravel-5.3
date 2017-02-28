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
            ->leftjoin('tickets on tickets.id = persons.ticket_id')
            ->leftjoin('transactions on transactions.id = tickets.transaction_id')
            ->where( "transactions.email", $email )
            ->get();

        d($person_statuses);
    }
}
