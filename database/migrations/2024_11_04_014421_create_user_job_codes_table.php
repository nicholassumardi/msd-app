<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserJobCodesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_job_codes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->cascadeOnDelete();
            $table->bigInteger('parent_id')->unsigned()->default(0);
            $table->foreignId('job_code_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('user_structure_mapping_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('id_structure')->nullable();
            $table->string('id_staff')->nullable();
            $table->string('position_code_structure')->nullable();
            $table->char('group')->comment("GROUP A || B || C || D")->nullable();
            $table->string('employee_type')->nullable();
            $table->date('assign_date')->nullable();
            $table->date('reassign_date')->nullable();
            $table->tinyInteger('status');
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
        Schema::dropIfExists('user_job_codes');
    }
}
