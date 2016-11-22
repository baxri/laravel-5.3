<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSmssTable extends Migration
{
    public function up()
    {
        Schema::create('smss', function (Blueprint $table) {
            $table->increments('id');
            $table->string('transaction_id');
            $table->string('op');
            $table->text('arguments');
            $table->text('text');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('smss');
    }
}
