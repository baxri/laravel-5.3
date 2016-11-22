<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePersonsTable extends Migration
{
    public function up()
    {
        Schema::create('persons', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('ticket_id');
            $table->integer('payout_transaction_id');
            $table->string('purchase')->unique();
            $table->integer('ischild');
            $table->integer('place');
            $table->integer('place_number');
            $table->string('name');
            $table->string('surname');
            $table->string('idnumber');
            $table->integer('status');

            $table->integer('tarif');
            $table->integer('price');
            $table->integer('discount_amount');
            $table->integer('returned_amount');

            $table->integer('second_mark');
            $table->integer('update_passenger');

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('persons');
    }
}
