<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLoanApplicationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('loan_applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->references('id')->on('users');
            $table->foreignId('loan_type_id')->references('id')->on('loan_types');
            $table->string('reason');
            $table->string('bank_name');
            $table->string('account_number');
            $table->enum('account_type', ['savings','current']);
            $table->enum('loan_status', ['pending','successful','failed'])->default('pending');
            $table->boolean('active')->default(0);
            $table->boolean('repaid')->default(0);
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
        Schema::dropIfExists('loan_applications');
    }
}
