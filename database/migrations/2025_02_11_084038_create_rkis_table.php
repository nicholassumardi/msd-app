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
            $table->foreignId('structure_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('ikw_id')->nullable()->constrained()->cascadeOnDelete();
            $table->integer('training_time')->nullable();
            $table->integer('structure_id_non_null')->virtualAs('COALESCE(structure_id, 0)');
            $table->integer('ikw_id_non_null')->virtualAs('COALESCE(ikw_id, 0)');
            $table->unique(['structure_id_non_null', 'ikw_id_non_null']);
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
