<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

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
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('name');
            $table->foreignId('company_id')->nullable()->constrained()->cascadeOnDelete()->comment('PT || KAS || HAS || KIAS');
            $table->foreignId('department_id')->nullable()->constrained()->cascadeOnDelete()->comment('QC || REF || PRS || TEK');
            $table->date('date_of_birth')->nullable();
            $table->string('identity_card')->nullable();
            // $table->string('identity_card')->unique()->nullable();
            $table->enum('gender', ['male', 'female'])->nullable();
            $table->string('religion')->nullable();
            $table->string('email')->unique()->nullable();
            $table->string('photo')->nullable();
            $table->string('education')->nullable();
            $table->string('marital_status')->nullable();
            $table->string('address')->nullable();
            $table->string('phone')->nullable();
            $table->tinyInteger('status')->nullable()->comment('1 : AKTIF || 0: NON AKTIF');
            $table->string('employee_type')->comment('Klasifikasi : Staff || Outsourcing || Karyawan || PHL || Kantin')->nullable();
            $table->string('section')->comment('QC || PRD || B-PO2C || FRAK 2 || TEK M')->nullable();
            $table->string('position_code')->comment('SPV || KRB || KARU 1 || MTC || BONGKAR 1 || ADM')->nullable();
            $table->string('status_twiji')->comment('Guteji || Intiji')->nullable();
            $table->string('schedule_type')->comment('Shift || Non Shift')->nullable();
            $table->string('password');
            $table->tinyInteger('status_account')->nullable()->comment('1: AKTIF || 2: NON AKTIF');
            $table->timestamps();
            $table->softDeletes();
        });
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
