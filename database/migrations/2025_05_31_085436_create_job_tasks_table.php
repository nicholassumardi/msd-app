<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateJobTasksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('job_tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_description_id')->nullable()->constrained()->cascadeOnDelete();
            $table->text('description')->nullable();
            $table->integer('job_desc_id_non_null')->virtualAs('COALESCE(job_description_id, 0)');
            $table->unique(['job_desc_id_non_null', 'description']);
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
        Schema::dropIfExists('job_tasks');
    }
}
