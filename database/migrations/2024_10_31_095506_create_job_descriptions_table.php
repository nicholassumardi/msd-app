<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateJobDescriptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('job_descriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_code_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('ikw_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('user_structure_mapping_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('code')->nullable();
            $table->text('description')->nullable();
            $table->integer('ikw_id_non_null')->virtualAs('COALESCE(ikw_id, 0)');
            $table->unique(['ikw_id_non_null', 'code']);
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
        Schema::dropIfExists('job_descriptions');
    }
}
