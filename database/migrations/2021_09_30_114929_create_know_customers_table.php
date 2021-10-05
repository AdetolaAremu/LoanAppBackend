<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateKnowCustomersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('know_customers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->references('id')->on('users');
            $table->foreignId('country_id')->references('id')->on('countries');
            $table->foreignId('state_id')->references('id')->on('states');
            $table->string('city');
            $table->string('address');
            $table->enum('identification_type', ['nin', 'bvn']);
            $table->string('id_number');
            $table->string('nok_first_name');
            $table->string('nok_last_name');
            $table->string('nok_middle_name')->nullable();
            $table->string('nok_email')->nullable();
            $table->string('nok_phone');
            $table->foreignId('nok_country_id')->references('id')->on('countries');
            $table->foreignId('nok_state_id')->references('id')->on('states');
            $table->enum('status', ['pending','successful','failed'])->default('pending');
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
        Schema::dropIfExists('know_customers');
    }
}
