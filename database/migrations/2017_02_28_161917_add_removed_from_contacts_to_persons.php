<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddRemovedFromContactsToPersons extends Migration
{
    public function up()
    {
        Schema::table('persons', function($table) {
            $table->dateTime('removed_from_contacts');
        });
    }

    public function down()
    {
        Schema::table('persons', function($table) {
            $table->dropColumn('removed_from_contacts');
        });
    }
}
