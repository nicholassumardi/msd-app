<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateJobDescDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('job_desc_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_structure_mapping_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('ikw_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('job_description_id')->nullable()->constrained()->cascadeOnDelete();
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
        Schema::dropIfExists('job_desc_details');
    }
}
