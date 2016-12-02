<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTransactionsTable extends Migration
{
    public $table_name = 'transactions';

    public function up()
    {
        Schema::create($this->table_name, function (Blueprint $table) {
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
            $table->timestampsTz();
        });

        \Illuminate\Support\Facades\DB::update("ALTER TABLE ".$this->table_name." AUTO_INCREMENT = 4000000;");
    }

    public function down()
    {
        Schema::dropIfExists( $this->table_name );
    }
}
