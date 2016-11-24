<?php

namespace App\Http\Controllers\Admin;

use App\Models\Ip;
use App\Models\PayoutTransaction;
use App\Models\Transaction;
use App\Ticket;
use Carbon\Carbon;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(){

        $today = Carbon::now()->toDay();

        $transaction = DB::table( 'transactions' )->select(
            DB::raw('
                sum(amount) as sum, 
                count(id) as count
                ')
        )
        ->where( "updated_at",  ">=", $today )
        ->where( "status",  Transaction::$success )
            ->get();


        $ticket = DB::table('tickets')->select(
            DB::raw('
                sum(amount_from_api) as sum, 
                count(id) as count
                ')
        )
            ->where( "updated_at",  ">=", $today )
            ->where( "status",  Ticket::$success )
            ->get();


        $payout = DB::table('payout_transactions')->select(
            DB::raw('
                sum(amount) as sum, 
                count(id) as count
                ')
        )
            ->where( "updated_at",  ">=", $today )
            ->where( "status",  PayoutTransaction::$success )
            ->get();


        $ips = DB::table('transactions')
            ->join('ips', 'transactions.ip', '=', 'ips.ip_key')
            ->select('ips.*')
            ->where( "transactions.updated_at",  ">=", $today )
           // ->groupby('ips.countryCode')
            ->limit(10)
            ->get();





        return view('vendor.backpack.base.dashboard', [
            'transaction' => $transaction[0],
            'payout' => $payout[0],
            'ticket' => $ticket[0],
            'ips' => $ips,
        ]);
    }
}
