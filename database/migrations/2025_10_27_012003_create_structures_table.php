<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStructuresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('structures', function (Blueprint $table) {
            $table->id();
            $table->foreignId('department_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('job_code_id')->nullable()->constrained()->cascadeOnDelete();
            $table->bigInteger('parent_id')->unsigned()->default(0);
            $table->string('position_code_structure')->nullable();
            $table->string('name');
            $table->integer('quota');
            $table->string('structure_type')->nullable()->comment('Non Staff | Staff | Non Staff Leader');
            $table->integer('job_code_id_non_null')->virtualAs('COALESCE(job_code_id, 0)');
            $table->unique(['job_code_id_non_null', 'name']);
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
        Schema::dropIfExists('structures');
    }
}
