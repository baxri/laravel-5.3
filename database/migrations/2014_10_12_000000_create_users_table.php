<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            //$table->string('api_token', 60)->unique();
            $table->rememberToken();
            $table->timestamps();
        });

        $user = new \App\User();

        $user->create([
            'name'     => 'Admin',
            'email'    => 'admin@unipay.ge',
            'password' => bcrypt('test123456'),
            //'api_token' => str_random(60),
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
