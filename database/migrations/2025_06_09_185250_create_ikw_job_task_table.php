<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIkwJobTaskTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ikw_job_task', function (Blueprint $table) {
            $table->id();
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
        Schema::dropIfExists('ikw_job_task');
    }
}
