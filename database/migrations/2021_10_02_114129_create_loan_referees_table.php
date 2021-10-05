<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLoanRefereesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('loan_referees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('loan_application_id')->references('id')->on('loan_applications');
            $table->text('first_name');
            $table->text('last_name');
            $table->text('middle_name')->nullable();
            $table->enum('gender', ['male','female']);
            $table->enum('relationship', ['brother','sister','relative','niece','cousin','friend']);
            $table->foreignId('nok_country_id')->references('id')->on('countries');
            $table->foreignId('nok_state_id')->references('id')->on('states');
            $table->string('city');
            $table->string('address');
            $table->string('phone');
            $table->string('alternate_phone')->nullable();
            $table->string('email')->nullable();
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
        Schema::dropIfExists('loan_referees');
    }
}
