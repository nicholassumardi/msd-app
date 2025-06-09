<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRkisTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rkis', function (Blueprint $table) {
            $table->id();
            $table->string('position_job_code')->nullable();
            $table->foreignId('ikw_id')->nullable()->constrained()->cascadeOnDelete();
            $table->integer('training_time')->nullable();
            $table->integer('ikw_id_non_null')->virtualAs('COALESCE(ikw_id, 0)');
            $table->unique(['position_job_code', 'ikw_id_non_null']);
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
        Schema::dropIfExists('rkis');
    }
}
