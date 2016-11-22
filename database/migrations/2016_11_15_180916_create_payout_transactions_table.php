<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePayoutTransactionsTable extends Migration
{
    public function up()
    {
        Schema::create('payout_transactions', function (Blueprint $table) {
            $table->increments('id');
            $table->string('payout_hash_id');
            $table->string('name');
            $table->string('surname');
            $table->string('idnumber');
            $table->string('birth_date');
            $table->string('iban');
            $table->string('bank');

            $table->integer('status');
            $table->integer('amount');
            $table->string('description');

            $table->string('ip');

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('payout_transactions');
    }
}
