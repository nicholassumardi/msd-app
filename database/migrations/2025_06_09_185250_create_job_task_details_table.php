<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateJobTaskDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('job_task_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_structure_mapping_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('ikw_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('job_task_id')->nullable()->constrained()->cascadeOnDelete();
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
        Schema::dropIfExists('job_task_details');
    }
}
