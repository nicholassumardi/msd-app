<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStructurePlotsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('structure_plots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('structure_id')->nullable()->constrained()->cascadeOnDelete();
            $table->bigInteger('parent_id')->unsigned()->default(0);
            $table->string('id_structure')->unique()->comment("this is actually id_plot that given to each plot")->nullable();
            $table->string('position_code_structure')->nullable();
            $table->string('suffix')->nullable();
            $table->char('group')->comment("GROUP A || B || C || D")->nullable();
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
        Schema::dropIfExists('structure_plots');
    }
}
