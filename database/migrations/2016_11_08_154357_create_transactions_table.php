<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTransactionsTable extends Migration
{
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->increments('id');

            $table->string('checkout_id');
            $table->integer('amount');
            $table->integer('commission');
            $table->integer('status');
            $table->string('index');
            $table->string('mobile');
            $table->string('index_mobile');
            $table->string('email');
            $table->integer('email_delivery');
            $table->integer('sms_delivery');
            $table->string('ip');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('transactions');
    }
}
