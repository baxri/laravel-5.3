<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateIpsTable extends Migration
{
    public function up()
    {
        Schema::create('ips', function (Blueprint $table) {
            $table->increments('id');
            $table->string('ip_key')->unique();
            $table->string('as');
            $table->string('city');
            $table->string('country');
            $table->string('countryCode');
            $table->string('isp');
            $table->string('lat');
            $table->string('lon');
            $table->string('org');
            $table->string('query');
            $table->string('region');
            $table->string('regionName');
            $table->string('status');
            $table->string('timezone');
            $table->string('zip');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('ips');
    }
}
