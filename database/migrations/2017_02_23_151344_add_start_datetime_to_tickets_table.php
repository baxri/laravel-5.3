<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Migrations\Migration;

class AddStartDatetimeToTicketsTable extends Migration
{
    public function up()
    {
        Schema::table('tickets', function($table) {
            $table->dateTime('start_datetime')->before('leave_datetime');
        });
    }

    public function down()
    {
        Schema::table('tickets', function($table) {
            $table->dropColumn('start_datetime');
        });
    }
}
