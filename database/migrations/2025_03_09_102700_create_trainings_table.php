<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTrainingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trainings', function (Blueprint $table) {
            $table->id();
            $table->string('no_training')->nullable();
            $table->bigInteger('trainee_id')->unsigned()->nullable();
            $table->foreign('trainee_id')->references('id')->on('users')->cascadeOnDelete();
            $table->bigInteger('trainer_id')->unsigned()->nullable();
            $table->foreign('trainer_id')->references('id')->on('users')->cascadeOnDelete();
            $table->bigInteger('assessor_id')->unsigned()->nullable();
            $table->foreign('assessor_id')->references('id')->on('users')->cascadeOnDelete();
            $table->foreignId('ikw_revision_id')->nullable()->constrained()->cascadeOnDelete();
            $table->date('training_plan_date')->nullable();
            $table->date('training_realisation_date')->nullable();
            $table->integer('training_duration')->nullable();
            $table->date('ticket_return_date')->nullable();
            $table->date('assessment_plan_date')->nullable();
            $table->date('assessment_realisation_date')->nullable();
            $table->integer('assessment_duration')->nullable();
            $table->tinyInteger('status_fa_print')->nullable();
            $table->string('assessment_result')->nullable()->comment('K | BK | RK');
            $table->tinyInteger('status')->nullable()->comment('1:Done | 0:Not Done');
            $table->text('description')->nullable();
            $table->tinyInteger('status_active')->nullable()->comment('1:Active | 0:Not Active');
            $table->unique(['no_training', 'trainee_id']);
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
        Schema::dropIfExists('trainings');
    }
}
