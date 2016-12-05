<?php

namespace App\Console\Commands;

use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Console\Command;

class ClearTransactions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'transaction:clear';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command clears all transactions in pending status';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $transactions = Transaction::where( [
            ['status', Transaction::$pending],
            ['updated_at', '<', Carbon::today(config('app.timezone'))]
        ] )->get();

        $count = 0;

        if( !empty($transactions) ){
            foreach ($transactions as $transaction){
                $transaction = Transaction::find($transaction->id);
                $transaction->delete();
                $count++;
            }
        }

        $this->info( Carbon::today(config('app.timezone'))." - $count Pending transactions deleted.");
    }
}
