<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddRowsToTransactionsTable extends Migration
{
    public function up()
    {
        Schema::table('transactions', function($table) {
            $table->string('request_ids')->default("");
            $table->integer('quantity')->default(0);
            $table->integer('canceled')->default(0);
            $table->integer('returned_amount')->default(0);
        });
    }

    public function down()
    {
        Schema::table('transactions', function($table) {
            $table->dropColumn('request_ids');
            $table->dropColumn('quantity');
            $table->dropColumn('canceled');
            $table->dropColumn('returned_amount');
        });
    }
}
