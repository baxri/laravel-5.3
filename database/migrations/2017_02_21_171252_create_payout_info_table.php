<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePayoutInfoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payout_info', function (Blueprint $table) {
            $table->increments('id');

            $table->string('email');
            $table->string('index');
            $table->string('mobile');
            $table->string('index_mobile');

            $table->string('name');
            $table->string('surname');
            $table->string('idnumber');
            $table->string('birth_date');
            $table->string('iban');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('payout_info');
    }
}
