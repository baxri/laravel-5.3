<?php

namespace App\Http\Middleware;

use App\Person;
use Closure;
use Illuminate\Http\Request;

class CheckTicketAuth
{
    public function handle( Request $request, Closure $next )
    {
        $ticket = $request->route()->parameter('ticket');

        if( !$ticket->authorized() ){
            return response()->error('UNAUTORIZED_ACCESS_FOR_TICKET_ACTION');
        }

        return $next($request);
    }
}
