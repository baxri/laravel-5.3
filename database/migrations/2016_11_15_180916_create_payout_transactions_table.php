<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePayoutTransactionsTable extends Migration
{
    public $table_name = 'payout_transactions';

    public function up()
    {
        Schema::create( $this->table_name, function (Blueprint $table) {
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
            $table->integer('commission');
            $table->string('description');

            $table->string('ip');

            $table->timestamps();
        });

        \Illuminate\Support\Facades\DB::update("ALTER TABLE ".$this->table_name." AUTO_INCREMENT = 7000000;");
    }

    public function down()
    {
        Schema::dropIfExists( $this->table_name );
    }
}
