<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTicketsTable extends Migration
{
    public $table_name = 'tickets';

    public function up()
    {
        Schema::create($this->table_name, function (Blueprint $table) {
            $table->increments('id');
            $table->integer('parent_id');
            $table->integer('transaction_id');
            $table->integer('status');
            $table->integer('from');
            $table->integer('to');
            $table->date('leave');
            $table->string('train');
            $table->string('vagon');
            $table->string('class');
            $table->string('rank');
            $table->string('adults');
            $table->string('childs');

            $table->string('internet_purchase_id');
            $table->string('request_id');
            $table->string('request_guid');
            $table->string('train_id');
            $table->string('prepare_online_payment_result');

            $table->string('tarif_adult');
            $table->string('tarif_teen');
            $table->string('tarif_all');
            $table->string('amount_from_api');

            $table->string('train_name');
            $table->string('source_station');
            $table->string('destination_station');

            $table->string('train_class');
            $table->string('vagon_type');
            $table->string('vagon_class');
            $table->string('vagon_rank');

            $table->string('action_token');
            $table->string('action_token_time');

            $table->dateTime('leave_datetime');
            $table->dateTime('enter_datetime');

            $table->string('reason');
            $table->integer('get_transaction_status');
            $table->integer('second_mark_time');

            $table->integer('second_mark_count');
            $table->integer('update_passenger_count');

            $table->timestamps();
        });

        \Illuminate\Support\Facades\DB::update("ALTER TABLE ".$this->table_name." AUTO_INCREMENT = 5000000;");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists($this->table_name);
    }
}
