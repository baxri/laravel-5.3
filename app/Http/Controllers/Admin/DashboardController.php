<?php

namespace App\Http\Controllers\Admin;

use App\Models\Ip;
use App\Models\PayoutTransaction;
use App\Models\Transaction;
use App\Person;
use App\Ticket;
use Carbon\Carbon;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(){

        $today = Carbon::now()->toDay();

        /*
         * Load Transactions Count and Sum
         *
         * */

        $transaction = DB::table( 'transactions' )->select(
            DB::raw('
                sum(amount) as sum, 
                count(id) as count
                ')
        )
        ->where( "created_at",  ">=", $today )
        ->where( "status",  Transaction::$success )
            ->get();

        /*
         * |||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||
         *
         * */


        /*
         * Load Transactions all statuses
         *
         * */

        $transaction_statuses = DB::table('transactions')->select(
            DB::raw('
                sum(amount) as sum, 
                count(id) as count,
                status
                ')
        )
            ->where( "status",  "!=", Transaction::$pending )
            ->where( "status",  "!=", Transaction::$process )
            ->where( "created_at",  ">=", $today )
            ->groupby( "status" )
            ->get();

        /*
         * |||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||
         *
         * */


        /*
         * Load Bank Payout Count and Sum
         *
         * */

        $payout = DB::table('payout_transactions')->select(
            DB::raw('
                sum(amount) as sum, 
                count(id) as count
                ')
        )
            ->where( "updated_at",  ">=", $today )
            ->where( "status",  PayoutTransaction::$success )
            ->get();

        /*
         * |||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||
         *
         * */

        /*
         * Load Bank Payout all statuses
         *
         * */

        $payout_statuses = DB::table('payout_transactions')->select(
            DB::raw('
                sum(amount) as sum, 
                count(id) as count,
                status
                ')
        )
            ->where( "status",  "!=", PayoutTransaction::$pending )
            ->where( "updated_at",  ">=", $today )
            ->groupby( "status" )
            ->get();

        /*
         * |||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||
         *
         * */


        /*
         * Load Ticket all statuses
         *
         * */

        $ticket_statuses = DB::table('tickets')->select(
            DB::raw('
                sum(amount_from_api) as sum, 
                count(id) as count,
                status,
                created_at
                ')
        )
            ->where( "status",  "!=", Ticket::$pending )
            ->where( "created_at",  ">=", $today )
            ->groupby( "status" )
            ->get();

        /*
         * |||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||
         *
         * */


        /*
         * Load Persons all statuses
         *
         * */

        $person_statuses = DB::table('persons')->select(
            DB::raw('                
                count(id) as count,
                status,
                created_at
                ')
        )
            ->where( "status",  "!=", Person::$pending )
            ->where( "status",  "!=", Person::$process )
            ->where( "created_at",  ">=", $today )
            ->groupby( "status" )
            ->get();

        /*
         * |||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||
         *
         * */


        /*
         * Load Last ip activity today
         *
         * */

        $ips = DB::table('transactions')
            ->join('ips', 'transactions.ip', '=', 'ips.ip_key')
            ->select('ips.*')
            ->where( "transactions.updated_at",  ">=", $today )
            ->limit(config('railway.last_ip_activity_count'))
            ->groupby( "ips.ip_key" )
            ->get();

        /*
         * |||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||
         *
         * */


        return view('vendor.backpack.base.dashboard', [
            'transaction' => $transaction[0],
            'transaction_statuses' => $transaction_statuses,

            'payout' => $payout[0],
            'payout_statuses' => $payout_statuses,

            'person_statuses' => $person_statuses,

            'ticket_statuses' => $ticket_statuses,
            'ips' => $ips,
        ]);
    }
}
