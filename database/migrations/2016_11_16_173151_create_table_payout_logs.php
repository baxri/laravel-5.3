<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTablePayoutLogs extends Migration
{
    public function up()
    {
        Schema::create('payout_logs', function (Blueprint $table) {
            $table->increments('id');
            $table->string('payout_id');
            $table->text('arguments');
            $table->text('text');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('payout_logs');
    }
}
