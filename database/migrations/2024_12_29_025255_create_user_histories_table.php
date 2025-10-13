<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('history_log_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->foreignId('company_id')->nullable()->constrained()->cascadeOnDelete()->comment('PT || KAS || HAS || KIAS');
            $table->foreignId('department_id')->nullable()->constrained()->cascadeOnDelete()->comment('QC || REF || PRS || TEK');
            $table->date('date_of_birth')->nullable();
            $table->string('identity_card')->nullable();
            $table->enum('gender', ['male', 'female'])->nullable();
            $table->string('religion')->nullable();
            $table->string('email')->unique()->nullable();
            $table->string('photo')->nullable();
            $table->string('education')->nullable();
            $table->string('marital_status')->nullable();
            $table->string('address')->nullable();
            $table->string('phone')->nullable();
            $table->tinyInteger('status')->nullable()->comment('1 : AKTIF || 2: NON AKTIF');
            $table->string('employee_type')->comment('Klasifikasi : Staff || Outsourcing || Karyawan || PHL || Kantin')->nullable();
            $table->string('section')->comment('QC || PRD || B-PO2C || FRAK 2 || TEK M')->nullable();
            $table->string('position_code')->comment('SPV || KRB || KARU 1 || MTC || BONGKAR 1 || ADM')->nullable();
            $table->string('status_twiji')->comment('Guteji || Intiji')->nullable();
            $table->string('schedule_type')->comment('Shift || Non Shift')->nullable();
            $table->string('employee_number')->unique();
            $table->date('join_date')->nullable();
            $table->date('leave_date')->nullable();
            $table->date('contract_start_date')->nullable();
            $table->date('contract_end_date')->nullable();
            $table->date('resign_date')->nullable();
            $table->tinyInteger('contract_status')->nullable();
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
        Schema::dropIfExists('user_histories');
    }
}
